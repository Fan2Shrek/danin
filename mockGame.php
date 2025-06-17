<?php

$host = $argv[1] ?? '0.0.0.0';
$port = $argv[2] ?? 12345;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    die("Failed to create socket: " . socket_strerror(socket_last_error()) . "\n");
}

if (!socket_bind($socket, $host, $port)) {
    die("Failed to bind: " . socket_strerror(socket_last_error($socket)) . "\n");
}

if (!socket_listen($socket)) {
    die("Failed to listen: " . socket_strerror(socket_last_error($socket)) . "\n");
}

echo "Server started on $host:$port\n";

while (true) {
    $client = socket_accept($socket);
    if ($client === false) {
        echo "Failed to accept connection: " . socket_strerror(socket_last_error($socket)) . "\n";
        continue;
    }

    $input = socket_read($client, 1024);
    echo "Received: $input\n";
}

socket_close($socket);
