<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\RefreshIgLatestPost;
use Symfony\Component\Console\Application;

$command = new RefreshIgLatestPost();

$application = new Application();
$application->add($command);

$application->run();