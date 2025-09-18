<?php

// Custom router untuk PHP built-in server agar melayani file statis dari public/

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');

// Jika file statis ada di public/, biarkan server menyajikannya langsung
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Selain itu, teruskan ke front controller Laravel
require __DIR__ . '/public/index.php';