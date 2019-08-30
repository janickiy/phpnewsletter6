<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Helper\{SettingsHelpers,SendEmailHelpers};
use App\Models\{ReadySent, Schedule, Subscribers};
use PHPMailer\PHPMailer;


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
            ->setHelp('This command allows you send out emails to subscribers.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws PHPMailer\Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mailcountno = 0;
        $mailcount = 0;

        $name = SettingsHelpers::getSetting('EMAIL');
        $schedule = Schedule::where('date', '<=', date('Y-m-d H:i:s'))->get();

        foreach ($schedule as $row) {

            $order = SettingsHelpers::getSetting('RANDOM_SEND') == 1 ? 'ORDER BY RAND()' : 'subscribers.id';
            $limit = SettingsHelpers::getSetting('LIMIT_SEND') == 1 ? "LIMIT " . SettingsHelpers::getSetting('LIMIT_NUMBER') : null;

            switch (SettingsHelpers::getSetting('INTERVAL_TYPE')) {
                case "minute":
                    $interval = "timeSent < NOW() - INTERVAL '" . SettingsHelpers::getSetting('INTERVAL_NUMBER') . "' MINUTE";
                    break;
                case "hour":
                    $interval = "timeSent < NOW() - INTERVAL '" . SettingsHelpers::getSetting('INTERVAL_NUMBER') . "' HOUR";
                    break;
                case "day":
                    $interval = "timeSent < NOW() - INTERVAL '" . SettingsHelpers::getSetting('INTERVAL_NUMBER') . "' DAY";
                    break;
                default:
                    $interval = null;
            }

            if ($interval) {
                $subscribers = Subscribers::select('subscribers.email')
                    ->join('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriberId')
                    ->join('schedule_category', 'subscriptions.categoryId', '=', 'schedule_category.categoryId')
                    ->leftJoin('ready_sent', function ($join) use ($row) {
                        $join->on('subscribers.id', '=', 'ready_sent.subscriberId')
                            ->where('ready_sent.templateId', '=', $row->templateId)
                            ->where('ready_sent.success', '=', 1);
                    })
                    ->whereNull('ready_sent.subscriberId')
                    ->where('subscribers.active', 1)
                    ->whereRaw($interval)
                    ->groupBy('subscribers.id')
                    ->orderByRaw($order)
                    ->limit($limit)
                    ->get();
            } else {
                $subscribers = Subscribers::select('subscribers.email')
                    ->join('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriberId')
                    ->join('schedule_category', 'subscriptions.categoryId', '=', 'schedule_category.categoryId')
                    ->leftJoin('ready_sent', function ($join) use ($row) {
                        $join->on('subscribers.id', '=', 'ready_sent.subscriberId')
                            ->where('ready_sent.templateId', '=', $row->templateId)
                            ->where('ready_sent.success', '=', 1);
                    })
                    ->whereNull('ready_sent.subscriberId')
                    ->where('subscribers.active', 1)
                    ->groupBy('subscribers.id')
                    ->orderByRaw($order)
                    ->limit($limit)
                    ->get();
            }

            foreach ($subscribers as $subscriber) {
                SendEmailHelpers::setBody($row->template->body);
                SendEmailHelpers::setSubject($row->template->subject);
                SendEmailHelpers::setPrior($row->template->prior);
                SendEmailHelpers::setEmail($subscriber->email);
                SendEmailHelpers::setAttach($this->attach);
                SendEmailHelpers::setKeyprivater($this->keyprivate);
                SendEmailHelpers::setToken($subscriber->token);
                $result = SendEmailHelpers::sendEmail($row->template->attach);

                $data = [];

                if ($result['result'] === true) {

                    $data['subscriberId'] = $subscriber->id;
                    $data['email'] = $subscriber->email;
                    $data['templateId'] = $row->templateId;
                    $data['success'] = 1;
                    $data['date'] = date('Y-m-d H:i:s');
                    $data['scheduleId'] = $row->id;

                    Subscribers::where('id', $subscriber->id)->update(['timeSent' => date('Y-m-d H:i:s')]);

                    $mailcount++;
                } else {
                    $data['subscriberId'] = $subscriber->id;
                    $data['email'] = $subscriber->email;
                    $data['templateId'] = $row->templateId;
                    $data['success'] = 0;
                    $data['errorMsg'] = $result['error'];
                    $data['date'] = date('Y-m-d H:i:s');
                    $data['scheduleId'] = $row->id;

                    $mailcountno++;
                }

                ReadySent::create($data);

                unset($data);

                if (SettingsHelpers::getSetting('LIMIT_SEND') == 1 && SettingsHelpers::getSetting('LIMIT_NUMBER') == $mailcount){
                    if (SettingsHelpers::getSetting('HOW_TO_SEND') == 2) $m->SmtpClose();
                    break;
                }
            }

            if (SettingsHelpers::getSetting('LIMIT_SEND') == 1 && SettingsHelpers::getSetting('LIMIT_NUMBER') == $mailcount){
                break;
            }
        }

        // $text = 'Hi '.$input->getArgument('name');
        // Example code
        $output->writeLn("Fetched records from Algolia 3" . $name);
    }
}