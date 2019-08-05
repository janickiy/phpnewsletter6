<?php

namespace App\Command;





use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Helper\SettingsHelpers;
use App\Models\{Schedule, Subscribers};




class EmailSend extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:email-send')

            // the short description shown while running "php bin/console list"
            ->setDescription('Command on sending emails')

         //   ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')





            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you send out emails to subscribers.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       $name = SettingsHelpers::getSetting('EMAIL');


        $order = SettingsHelpers::getSetting('RANDOM_SEND') == 1 ? 'ORDER BY RAND()' : '';
        $limit = SettingsHelpers::getSetting('LIMIT_SEND') == 1 ? "LIMIT " . SettingsHelpers::getSetting('LIMIT_NUMBER') : "";

         $shedule = Schedule::get();

         foreach ($shedule as $row) {

             $subscribers = Subscribers::select('')
                 ->join('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriberId')
                 ->join('schedule_category', 'subscriptions.categoryId', '=', 'schedule_category.categoryId')
                 ->leftJoin('ready_sent', function($join) use ($row)
                 {
                     $join->on('subscribers.id', '=', 'ready_sent.subscriberId')
                         ->where('ready_sent.templateId', '=', $row->templateId)
                         ->where('ready_sent.success', '=', 1);
                 })
                 ->whereNull('ready_sent.subscriberId')
                 ->where('Subscribers.active','=',1)
                 ->get();




         }







       // $text = 'Hi '.$input->getArgument('name');
        // Example code
        $output->writeLn("Fetched records from Algolia 3" . $name);

    }
}