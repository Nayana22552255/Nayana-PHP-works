<?php

class ChatClient
{
    private $socket;
    private $receiverProcess = null;

    public function start(): void
    {
        // Internal mode used by the background receiver.
        global $argv;

        if (($argv[1] ?? "") === "--receive") {
            $this->runReceiver(
                $argv[2] ?? "127.0.0.1",
                $argv[3] ?? "Guest"
            );
            return;
        }

        echo "=== PHP GROUP CHAT CLIENT ===\n";

        $serverIp = trim(readline(
            "Enter server IP (127.0.0.1): "
        ));

        if ($serverIp === "") {
            $serverIp = "127.0.0.1";
        }

        $username = trim(readline("Enter username: "));

        $username = preg_replace(
            '/[\r\n\x00-\x1F\x7F|]/',
            '',
            $username
        );

        $username = substr($username, 0, 30);

        if ($username === "") {
            $username = "Guest";
        }

        // Start a separate process to receive messages.
        $this->receiverProcess = proc_open(
            [
                PHP_BINARY,
                __FILE__,
                "--receive",
                $serverIp,
                $username
            ],
            [
                0 => ["file", "NUL", "r"],
                1 => ["file", "php://stdout", "w"],
                2 => ["file", "php://stderr", "w"]
            ],
            $pipes
        );

        if (!is_resource($this->receiverProcess)) {
            echo "Could not start the receiver process.\n";
            return;
        }

        // Connect this main process for sending messages.
        $this->socket = @stream_socket_client(
            "tcp://" . $serverIp . ":8000",
            $errorCode,
            $errorMessage,
            10
        );

        if ($this->socket === false) {
            echo "Connection failed: $errorMessage\n";
            $this->stopReceiver();
            return;
        }

        fwrite($this->socket, "SEND|" . $username . "\n");

        echo "\nConnected as $username.\n";
        echo "Type a message and press Enter.\n";
        echo "Type /quit to exit.\n\n";

        while (true) {
            $message = readline("You: ");

            if ($message === false) {
                break;
            }

            $message = trim($message);

            if ($message === "") {
                continue;
            }

            $written = @fwrite(
                $this->socket,
                $message . "\n"
            );

            if ($written === false) {
                echo "Failed to send message.\n";
                break;
            }

            if ($message === "/quit") {
                break;
            }
        }

        fclose($this->socket);
        $this->stopReceiver();

        echo "Disconnected from chat.\n";
    }

    private function runReceiver(
        string $serverIp,
        string $username
    ): void {
        $socket = @stream_socket_client(
            "tcp://" . $serverIp . ":8000",
            $errorCode,
            $errorMessage,
            10
        );

        if ($socket === false) {
            echo "Receiver connection failed: "
                . $errorMessage . "\n";
            exit(1);
        }

        fwrite(
            $socket,
            "RECEIVE|" . $username . "\n"
        );

        while (($line = fgets($socket)) !== false) {
            echo "\r" . $line;
        }

        fclose($socket);
        exit(0);
    }

    private function stopReceiver(): void
    {
        if (is_resource($this->receiverProcess)) {
            proc_terminate($this->receiverProcess);
            proc_close($this->receiverProcess);
            $this->receiverProcess = null;
        }
    }
}

$client = new ChatClient();
$client->start();