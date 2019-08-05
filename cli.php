<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';


use App\Command\EmailSend;
use Symfony\Component\Console\Application;

$command = new EmailSend();

$application = new Application('echo', getenv('VERSION', null));
$application->add($command);

$application->run();