<?php

require '../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$writable = new \React\Stream\WritableResourceStream(STDOUT, $loop, 1);

var_dump($writable->write("Hello world\n"));

$writable->on('drain', function() {
    echo "The stream is drained\n";
});

$loop->run();
