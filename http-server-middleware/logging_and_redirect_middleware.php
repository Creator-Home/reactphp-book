<?php

require '../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();

$redirectMiddleware = function(ServerRequestInterface $request, callable $next) {
    if($request->getUri()->getPath() === '/admin') {
        return new Response(301, ['Location' => '/']);
    }
    return $next($request);
};

$clientIpMiddleware = function(ServerRequestInterface $request, callable $next) {
    $clientIp = $request->getServerParams()['REMOTE_ADDR'];

    return $next($request);
};

$loggingMiddleware = function(ServerRequestInterface $request, callable $next) {
    echo date('Y-m-d H:i:s') . ' ' . $request->getMethod() . ' ' . $request->getUri()->getPath() . PHP_EOL;

    return $next($request);
};

$server = new Server([
    $loggingMiddleware,
    $redirectMiddleware,
    function (ServerRequestInterface $request) {
        return new Response(200, ['Content-Type' => 'text/plain'],  "Hello world\n");
    }
]);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
