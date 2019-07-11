<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshIgLatestPost extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:refresh-ig-latest-post')

            // the short description shown while running "php bin/console list"
            ->setDescription('Refresh latest post on Algolia dataset.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you refresh Algolia dataset with latest post.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Example code
        $output->writeLn("Fetched records from Algolia");

    }
}