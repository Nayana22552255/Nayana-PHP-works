<?php

class ChatServer
{
    private $server;
    private array $clients = [];
    private array $buffers = [];
    private string $historyFile;

    public function __construct()
    {
        $this->historyFile = __DIR__ . "/chat_history.json";

        if (!file_exists($this->historyFile)) {
            file_put_contents($this->historyFile, "[]");
        }
    }

    public function start(): void
    {
        $this->server = stream_socket_server(
            "tcp://0.0.0.0:8000",
            $errorCode,
            $errorMessage
        );

        if ($this->server === false) {
            die("Server error: $errorMessage ($errorCode)\n");
        }

        stream_set_blocking($this->server, false);

        echo "=== GROUP CHAT SERVER ===\n";
        echo "Server IP: 127.0.0.1\n";
        echo "Port: 8000\n";
        echo "Waiting for clients...\n\n";

        while (true) {
            $read = [$this->server];

            foreach ($this->clients as $client) {
                $read[] = $client["socket"];
            }

            $write = null;
            $except = null;

            $ready = @stream_select(
                $read,
                $write,
                $except,
                1
            );

            if ($ready === false || $ready === 0) {
                continue;
            }

            foreach ($read as $stream) {
                if ($stream === $this->server) {
                    $this->acceptClient();
                } else {
                    $this->receiveData($stream);
                }
            }
        }
    }

    private function acceptClient(): void
    {
        $socket = @stream_socket_accept($this->server, 0);

        if ($socket === false) {
            return;
        }

        stream_set_blocking($socket, false);

        $id = (int) $socket;

        $this->clients[$id] = [
            "socket" => $socket,
            "role" => null,
            "username" => "Guest"
        ];

        $this->buffers[$id] = "";

        echo "New connection accepted.\n";
    }

    private function receiveData($socket): void
    {
        $id = (int) $socket;
        $data = @fread($socket, 4096);

        if ($data === false || ($data === "" && feof($socket))) {
            $this->removeClient($id);
            return;
        }

        if ($data === "") {
            return;
        }

        $this->buffers[$id] .= $data;

        while (($position = strpos(
            $this->buffers[$id],
            "\n"
        )) !== false) {
            $line = substr(
                $this->buffers[$id],
                0,
                $position
            );

            $this->buffers[$id] = substr(
                $this->buffers[$id],
                $position + 1
            );

            $line = rtrim($line, "\r");

            if ($line === "") {
                continue;
            }

            // Identify the connection on its first line.
            if ($this->clients[$id]["role"] === null) {
                $parts = explode("|", $line, 2);

                if (count($parts) !== 2) {
                    $this->removeClient($id);
                    return;
                }

                $role = strtoupper($parts[0]);
                $username = preg_replace(
                    '/[\x00-\x1F\x7F|]/',
                    '',
                    trim($parts[1])
                );

                $username = substr($username, 0, 30);

                if ($username === "") {
                    $username = "Guest";
                }

                if (!in_array($role, ["SEND", "RECEIVE"], true)) {
                    $this->removeClient($id);
                    return;
                }

                $this->clients[$id]["role"] = $role;
                $this->clients[$id]["username"] = $username;

                echo "$username connected ($role).\n";

                if ($role === "RECEIVE") {
                    $this->sendHistory($socket);
                }

                continue;
            }

            // Receiving connections cannot send messages.
            if ($this->clients[$id]["role"] !== "SEND") {
                continue;
            }

            if ($line === "/quit") {
                $this->removeClient($id);
                return;
            }

            $username = $this->clients[$id]["username"];
            $message = substr($line, 0, 1000);

            $formatted = "[" . date("H:i:s") . "] "
                . $username . ": " . $message;

            $this->saveMessage($username, $message);

            echo $formatted . "\n";

            // Forward the message to both clients' receive connections.
            $this->broadcast($formatted);
        }
    }

    private function saveMessage(
        string $username,
        string $message
    ): void {
        $history = [];

        $data = @file_get_contents($this->historyFile);
        $decoded = json_decode($data ?: "[]", true);

        if (is_array($decoded)) {
            $history = $decoded;
        }

        $history[] = [
            "sender" => $username,
            "message" => $message,
            "time" => date("Y-m-d H:i:s")
        ];

        $saved = file_put_contents(
            $this->historyFile,
            json_encode(
                $history,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ),
            LOCK_EX
        );

        if ($saved === false) {
            echo "Warning: Unable to save chat history.\n";
        }
    }

    private function sendHistory($socket): void
    {
        $data = @file_get_contents($this->historyFile);
        $history = json_decode($data ?: "[]", true);

        @fwrite($socket, "----- CHAT HISTORY -----\n");

        if (!is_array($history)) {
            $history = [];
        }

        foreach ($history as $chat) {
            $line = "[" . ($chat["time"] ?? "") . "] "
                . ($chat["sender"] ?? "Unknown") . ": "
                . ($chat["message"] ?? "");

            @fwrite($socket, $line . "\n");
        }

        @fwrite($socket, "----- LIVE CHAT -----\n");
    }

    private function broadcast(string $message): void
    {
        foreach ($this->clients as $client) {
            if ($client["role"] === "RECEIVE") {
                @fwrite(
                    $client["socket"],
                    $message . "\n"
                );
            }
        }
    }

    private function removeClient(int $id): void
    {
        if (!isset($this->clients[$id])) {
            return;
        }

        $username = $this->clients[$id]["username"];

        fclose($this->clients[$id]["socket"]);

        unset($this->clients[$id], $this->buffers[$id]);

        echo "$username disconnected.\n";
    }
}

$server = new ChatServer();
$server->start();