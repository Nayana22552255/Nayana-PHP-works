<?php
// Simple PHP TCP chat server. No Composer and no database.
// Run: php server.php
declare(strict_types=1);

$host = '0.0.0.0';
$port = 9000;
$dataDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
$chatDir = $dataDir . DIRECTORY_SEPARATOR . 'chats';
$usersFile = $dataDir . DIRECTORY_SEPARATOR . 'users.json';

if (!is_dir($dataDir)) mkdir($dataDir, 0700, true);
if (!is_dir($chatDir)) mkdir($chatDir, 0700, true);
if (!file_exists($usersFile)) file_put_contents($usersFile, "{}");

function readJsonFile(string $path): array {
    $raw = @file_get_contents($path);
    $data = json_decode($raw ?: '{}', true);
    return is_array($data) ? $data : [];
}
function writeJsonFile(string $path, array $data): bool {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return $json !== false && file_put_contents($path, $json, LOCK_EX) !== false;
}
function validUsername(mixed $name): bool {
    return is_string($name) && preg_match('/^[A-Za-z0-9_]{3,20}$/', $name) === 1;
}
function sendJson($conn, array $data): void {
    @fwrite($conn, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) . "\n");
}
function chatPath(string $a, string $b, string $chatDir): string {
    $names = [strtolower($a), strtolower($b)];
    sort($names, SORT_STRING);
    return $chatDir . DIRECTORY_SEPARATOR . hash('sha256', $names[0] . '|' . $names[1]) . '.json';
}
function saveMessage(string $from, string $to, string $message, string $chatDir): array {
    $path = chatPath($from, $to, $chatDir);
    $fp = fopen($path, 'c+');
    if (!$fp) throw new RuntimeException('Cannot open chat history.');
    flock($fp, LOCK_EX);
    $raw = stream_get_contents($fp);
    $messages = json_decode($raw ?: '[]', true);
    if (!is_array($messages)) $messages = [];
    $record = ['from' => $from, 'to' => $to, 'message' => $message, 'time' => date('Y-m-d H:i:s')];
    $messages[] = $record;
    if (count($messages) > 2000) $messages = array_slice($messages, -2000);
    rewind($fp);
    ftruncate($fp, 0);
    fwrite($fp, json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return $record;
}

$server = stream_socket_server("tcp://{$host}:{$port}", $errno, $errstr);
if (!$server) {
    fwrite(STDERR, "Could not start server: {$errstr} ({$errno})\n");
    exit(1);
}
stream_set_blocking($server, false);
echo "Simple PHP Chat server running on {$host}:{$port}\n";
echo "Keep this window open. Press Ctrl+C to stop.\n";

$clients = []; // resource id => ['socket'=>resource, 'buffer'=>'', 'user'=>null]
while (true) {
    $read = [$server];
    foreach ($clients as $c) $read[] = $c['socket'];
    $write = $except = [];
    $changed = @stream_select($read, $write, $except, 1);
    if ($changed === false) continue;
    if (in_array($server, $read, true)) {
        $conn = @stream_socket_accept($server, 0);
        if ($conn) {
            stream_set_blocking($conn, false);
            $id = (int)$conn;
            $clients[$id] = ['socket' => $conn, 'buffer' => '', 'user' => null];
            sendJson($conn, ['type' => 'info', 'message' => 'Connected. Register or login.']);
        }
        $read = array_values(array_filter($read, fn($s) => $s !== $server));
    }

    foreach ($read as $sock) {
        $id = (int)$sock;
        if (!isset($clients[$id])) continue;
        $chunk = @fread($sock, 8192);
        if ($chunk === false || ($chunk === '' && feof($sock))) {
            $oldUser = $clients[$id]['user'];
            unset($clients[$id]);
            @fclose($sock);
            if ($oldUser) echo "{$oldUser} disconnected.\n";
            continue;
        }
        if ($chunk === '') continue;
        $clients[$id]['buffer'] .= $chunk;
        if (strlen($clients[$id]['buffer']) > 12000) {
            sendJson($sock, ['type' => 'error', 'message' => 'Request too large.']);
            unset($clients[$id]);
            @fclose($sock);
            continue;
        }
        while (($newline = strpos($clients[$id]['buffer'], "\n")) !== false) {
            $line = trim(substr($clients[$id]['buffer'], 0, $newline));
            $clients[$id]['buffer'] = substr($clients[$id]['buffer'], $newline + 1);
            $req = json_decode($line, true);
            if (!is_array($req) || !isset($req['action'])) {
                sendJson($sock, ['type' => 'error', 'message' => 'Invalid request.']);
                continue;
            }
            $action = $req['action'];
            $username = $clients[$id]['user'];

            if ($action === 'register') {
                $name = $req['username'] ?? null;
                $pass = $req['password'] ?? null;
                if (!validUsername($name) || !is_string($pass) || strlen($pass) < 8 || strlen($pass) > 200) {
                    sendJson($sock, ['type'=>'error','message'=>'Username: 3-20 letters/numbers/underscore; password: at least 8 characters.']);
                    continue;
                }
                $users = readJsonFile($usersFile);
                $key = strtolower($name);
                if (isset($users[$key])) {
                    sendJson($sock, ['type'=>'error','message'=>'Username already exists.']);
                } else {
                    $users[$key] = ['username'=>$name, 'password_hash'=>password_hash($pass, PASSWORD_DEFAULT)];
                    writeJsonFile($usersFile, $users);
                    sendJson($sock, ['type'=>'success','message'=>'Account created. Now log in.']);
                }
                continue;
            }

            if ($action === 'login') {
                $name = $req['username'] ?? null;
                $pass = $req['password'] ?? null;
                $users = readJsonFile($usersFile);
                $entry = is_string($name) ? ($users[strtolower($name)] ?? null) : null;
                if (!validUsername($name) || !is_string($pass) || !is_array($entry) ||
                    !password_verify($pass, $entry['password_hash'] ?? '')) {
                    sendJson($sock, ['type'=>'error','message'=>'Incorrect username or password.']);
                    continue;
                }
                $alreadyOnline = false;
                foreach ($clients as $otherId => $c) {
                    if ($otherId !== $id && is_string($c['user']) && strtolower($c['user']) === strtolower($name)) $alreadyOnline = true;
                }
                if ($alreadyOnline) {
                    sendJson($sock, ['type'=>'error','message'=>'This account is already logged in.']);
                    continue;
                }
                $clients[$id]['user'] = $entry['username'];
                sendJson($sock, ['type'=>'login_ok','username'=>$entry['username']]);
                echo $entry['username'] . " logged in.\n";
                continue;
            }

            if (!$username) {
                sendJson($sock, ['type'=>'error','message'=>'Please log in first.']);
                continue;
            }

            if ($action === 'users') {
                $users = readJsonFile($usersFile);
                $list = [];
                foreach ($users as $u) {
                    if (strtolower($u['username']) !== strtolower($username)) $list[] = $u['username'];
                }
                $online = [];
                foreach ($clients as $c) if ($c['user']) $online[] = $c['user'];
                sendJson($sock, ['type'=>'users','users'=>$list,'online'=>$online]);
                continue;
            }

            if ($action === 'history') {
                $peer = $req['peer'] ?? '';
                $users = readJsonFile($usersFile);
                if (!validUsername($peer) || !isset($users[strtolower($peer)]) || strtolower($peer) === strtolower($username)) {
                    sendJson($sock, ['type'=>'error','message'=>'Unknown recipient.']);
                    continue;
                }
                $history = readJsonFile(chatPath($username, $peer, $chatDir));
                sendJson($sock, ['type'=>'history','peer'=>$peer,'messages'=>array_slice($history, -50)]);
                continue;
            }

            if ($action === 'send') {
                $peer = $req['to'] ?? '';
                $message = trim((string)($req['message'] ?? ''));
                $users = readJsonFile($usersFile);
                if (!validUsername($peer) || !isset($users[strtolower($peer)]) || strtolower($peer) === strtolower($username)) {
                    sendJson($sock, ['type'=>'error','message'=>'Choose a valid registered recipient.']);
                    continue;
                }
                if ($message === '' || strlen($message) > 2000) {
                    sendJson($sock, ['type'=>'error','message'=>'Message must be 1-2000 bytes.']);
                    continue;
                }
                $record = saveMessage($username, $users[strtolower($peer)]['username'], $message, $chatDir);
                sendJson($sock, ['type'=>'sent','record'=>$record]);
                $delivered = false;
                foreach ($clients as $otherId => $c) {
                    if ($otherId !== $id && is_string($c['user']) && strtolower($c['user']) === strtolower($peer)) {
                        sendJson($c['socket'], ['type'=>'message','record'=>$record]);
                        $delivered = true;
                    }
                }
                if (!$delivered) sendJson($sock, ['type'=>'info','message'=>'Friend is offline. Message saved in history.']);
                continue;
            }

            if ($action === 'logout') {
                $clients[$id]['user'] = null;
                sendJson($sock, ['type'=>'success','message'=>'Logged out.']);
                continue;
            }

            sendJson($sock, ['type'=>'error','message'=>'Unknown action.']);
        }
    }
}
