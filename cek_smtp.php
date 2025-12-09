<?php
$host = 'mail.x-change.pro';
$port = 465;
$timeout = 10;

echo "Mencoba connect ke $host:$port ...\n";

$context = stream_context_create([
  'ssl' => [
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true
  ]
]);

// Kita coba buka jalur komunikasi langsung tanpa lewat Laravel
$socket = stream_socket_client(
  "ssl://{$host}:{$port}",
  $errno,
  $errstr,
  $timeout,
  STREAM_CLIENT_CONNECT,
  $context
);

if (!$socket) {
  echo "GAGAL TOTAL! ❌\n";
  echo "Error: $errstr ($errno)\n";
} else {
  echo "BERHASIL CONNECT! ✅\n";
  echo "Server menjawab: " . fgets($socket);
  fclose($socket);
}
