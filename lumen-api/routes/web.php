<?php

$router->get('', function () {
    return '🟢 Lumen está funcionando';
});

$router->get('/register', function () {
    require __DIR__ . '/../src/register.php';
});

$router->get('/ping', function () {
    return 'pong';
});
