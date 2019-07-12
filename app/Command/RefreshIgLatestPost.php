<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class RefreshIgLatestPost extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:refresh-ig-latest-post6')

            // the short description shown while running "php bin/console list"
            ->setDescription('Refresh latest post on Algolia dataset.')

            ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')


            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you refresh Algolia dataset with latest post.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = 'Hi '.$input->getArgument('name');
        // Example code
        $output->writeLn("Fetched records from Algolia 3" . $text);

    }
}