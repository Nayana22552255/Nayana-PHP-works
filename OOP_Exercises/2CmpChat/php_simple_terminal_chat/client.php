<?php
// Simple PHP terminal client. No Composer and no database.
// Run: php client.php [server-ip] [port]
declare(strict_types=1);

$host = $argv[1] ?? '127.0.0.1';
$port = isset($argv[2]) ? (int)$argv[2] : 9000;

function sendRequest($socket, array $data): void {
    fwrite($socket, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) . "\n");
}
function readResponse($socket, int $timeout = 5): ?array {
    $end = microtime(true) + $timeout;
    while (microtime(true) < $end) {
        $read = [$socket]; $write = $except = [];
        $ready = @stream_select($read, $write, $except, 1);
        if ($ready === false) return null;
        if ($ready > 0) {
            $line = fgets($socket);
            if ($line === false) return null;
            $data = json_decode(trim($line), true);
            if (is_array($data)) return $data;
        }
    }
    return null;
}
function printNotice(array $r): void {
    $type = $r['type'] ?? '';
    if (in_array($type, ['error','info','success'], true)) echo "\n" . strtoupper($type) . ': ' . ($r['message'] ?? '') . "\n";
    elseif ($type === 'message' && isset($r['record'])) {
        $m = $r['record'];
        echo "\n[" . ($m['time'] ?? '') . "] " . ($m['from'] ?? '?') . ": " . ($m['message'] ?? '') . "\n";
    }
}
function drainSocketMessages($socket): array {
    $messages = [];
    stream_set_blocking($socket, false);
    while (($line = fgets($socket)) !== false) {
        $line = trim((string)$line);
        if ($line === '') continue;
        $incoming = json_decode($line, true);
        if (is_array($incoming)) {
            $messages[] = $incoming;
        }
    }
    stream_set_blocking($socket, true);
    foreach ($messages as $incoming) {
        printNotice($incoming);
    }
    return $messages;
}

$socket = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 5);
if (!$socket) {
    fwrite(STDERR, "Cannot connect to {$host}:{$port}: {$errstr}\nStart server.php first.\n");
    exit(1);
}
stream_set_blocking($socket, true);
echo "Connected to {$host}:{$port}\n";
$greeting = readResponse($socket, 2);

while (true) {
    echo "\n==== PHP TERMINAL CHAT ====\n1. Register\n2. Login\n3. Exit\n";
    $choice = trim((string)readline("Choose: "));
    if ($choice === '3') { fclose($socket); exit(0); }
    if (!in_array($choice, ['1','2'], true)) { echo "Choose 1, 2, or 3.\n"; continue; }
    $username = trim((string)readline("Username: "));
    $password = trim((string)readline("Password (8+ characters): "));
    sendRequest($socket, ['action'=>$choice === '1' ? 'register' : 'login','username'=>$username,'password'=>$password]);
    $response = readResponse($socket, 5);
    if (!$response) { echo "No response from server.\n"; continue; }
    printNotice($response);
    if (($response['type'] ?? '') === 'login_ok') break;
}

$loggedIn = isset($response['type']) && $response['type'] === 'login_ok';
if (!$loggedIn) { fclose($socket); exit(0); }
echo "Logged in as " . $response['username'] . "\n";
echo "Tip: use menu options. During a chat, messages from your friend appear when you press Enter or return to the menu.\n";

while (true) {
    echo "\n1. List users\n2. Chat with friend\n3. View chat history\n4. Exit\n";
    $choice = trim((string)readline("Choose: "));
    if ($choice === '4') { sendRequest($socket, ['action'=>'logout']); fclose($socket); echo "Goodbye!\n"; exit(0); }
    if ($choice === '1') {
        sendRequest($socket, ['action'=>'users']);
        $r = readResponse($socket, 5);
        if (($r['type'] ?? '') === 'users') {
            $online = array_map('strtolower', $r['online'] ?? []);
            echo "Users:\n";
            foreach ($r['users'] as $u) echo " - {$u}" . (in_array(strtolower($u), $online, true) ? ' (online)' : ' (offline)') . "\n";
        } elseif ($r) printNotice($r);
        continue;
    }
    if ($choice === '2' || $choice === '3') {
        $peer = trim((string)readline("Friend username: "));
        if ($peer === '') continue;
        if ($choice === '3') {
            sendRequest($socket, ['action'=>'history','peer'=>$peer]);
            $r = readResponse($socket, 5);
            if (($r['type'] ?? '') === 'history') {
                echo "--- History with {$peer} ---\n";
                foreach ($r['messages'] as $m) echo "[" . $m['time'] . "] " . $m['from'] . ": " . $m['message'] . "\n";
            } elseif ($r) printNotice($r);
            continue;
        }
        sendRequest($socket, ['action'=>'history','peer'=>$peer]);
        $r = readResponse($socket, 5);
        if (($r['type'] ?? '') !== 'history') { if ($r) printNotice($r); continue; }
        echo "--- Chat with {$peer}; type /back to return ---\n";
        foreach ($r['messages'] as $m) echo "[" . $m['time'] . "] " . $m['from'] . ": " . $m['message'] . "\n";
        $lastIncomingFrom = '';
        while (true) {
            foreach (drainSocketMessages($socket) as $incoming) {
                if (($incoming['type'] ?? '') === 'message' && isset($incoming['record'])) {
                    $sender = (string)($incoming['record']['from'] ?? '');
                    if ($sender !== '') $lastIncomingFrom = $sender;
                }
            }
            $message = trim((string)readline("> "));
            if ($message === '/back') break;
            if ($message === '') continue;

            $targetPeer = $peer;
            if (preg_match('/^\/(r|reply)\b\s*(.*)$/i', $message, $matches)) {
                $replyText = trim((string)$matches[2]);
                if ($replyText === '') {
                    echo "Reply message cannot be empty.\n";
                    continue;
                }
                if ($lastIncomingFrom === '') {
                    echo "No incoming message to reply to yet.\n";
                    continue;
                }
                $targetPeer = $lastIncomingFrom;
                $message = $replyText;
            }

            sendRequest($socket, ['action'=>'send','to'=>$targetPeer,'message'=>$message]);
            usleep(100000);
            foreach (drainSocketMessages($socket) as $incoming) {
                if (($incoming['type'] ?? '') === 'message' && isset($incoming['record'])) {
                    $sender = (string)($incoming['record']['from'] ?? '');
                    if ($sender !== '') $lastIncomingFrom = $sender;
                }
            }
        }
    } else {
        echo "Invalid choice.\n";
    }
}
