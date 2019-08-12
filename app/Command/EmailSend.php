<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Helper\{SettingsHelpers,StringHelpers};
use App\Models\{ReadySent, Schedule, Subscribers, Smtp, Attach};
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
                $m = new PHPMailer\PHPMailer();

                if (SettingsHelpers::getSetting('ADD_DKIM') == 1 && file_exists('' . SettingsHelpers::getSetting('DKIM_PRIVATE'))) {
                    $m->DKIM_domain = SettingsHelpers::getSetting('DKIM_DOMAIN');
                    $m->DKIM_private = SettingsHelpers::getSetting('DKIM_PRIVATE');
                    $m->DKIM_selector = SettingsHelpers::getSetting('DKIM_SELECTOR');
                    $m->DKIM_passphrase = SettingsHelpers::getSetting('DKIM_PASSPHRASE');
                    $m->DKIM_identity = SettingsHelpers::getSetting('DKIM_IDENTITY');
                }

                if (SettingsHelpers::getSetting('HOW_TO_SEND') == 'smtp') {
                    $m->IsSMTP();
                    $m->SMTPAuth = true;
                    $m->SMTPKeepAlive = true;

                    $smtp_q = Smtp::query();

                    if ($smtp_q->count() > 1) {
                        $smtp_r = $smtp_q->orderBy()->limit(1)->get();
                    } else {
                        $smtp_r = $smtp_q->limit(1)->get();
                    }

                    if ($smtp_r) $smtp = $smtp_r->toArray();

                    if (isset($smtp[0]['host']) && isset($smtp[0]['port']) && isset($smtp[0]['port']) && isset($smtp[0]['username']) && isset($smtp[0]['password'])) {
                        $m->Host = $smtp[0]['host'];
                        $m->Port = $smtp[0]['port'];
                        $m->Username = $smtp[0]['username'];
                        $m->Password = $smtp[0]['password'];

                        if ($smtp[0]['secure'] == 'ssl')
                            $m->SMTPSecure = 'ssl';
                        elseif ($smtp[0]['secure'] == 'tls')
                            $m->SMTPSecure = 'tls';

                        if ($smtp[0]['authentication'] == 'plain')
                            $m->AuthType = 'PLAIN';
                        elseif ($smtp[0]['authentication'] == 'cram-md5')
                            $m->AuthType = 'CRAM-MD5';

                        $m->Timeout = $smtp[0]['timeout'];
                    }
                } elseif (SettingsHelpers::getSetting('HOW_TO_SEND') == 'sendmail' && SettingsHelpers::getSetting('SENDMAIL_PATH') != '') {
                    $m->IsSendmail();
                    $m->Sendmail = SettingsHelpers::getSetting('SENDMAIL_PATH');
                } else {
                    $m->IsMail();
                }

                $m->CharSet = SettingsHelpers::getSetting('CHARSET');

                if ($row->template->prior == 1)
                    $m->Priority = 1;
                elseif ($row->template->prior == 2)
                    $m->Priority = 5;
                else $m->Priority = 3;

                if (SettingsHelpers::getSetting('SHOW_EMAIL') == 0)
                    $m->From = "noreply@" . StringHelpers::getDomain(SettingsHelpers::getSetting('URL'));
                else
                    $m->From = SettingsHelpers::getSetting('EMAIL');

                $m->FromName = SettingsHelpers::getSetting('FROM');

                if (SettingsHelpers::getSetting('LIST_OWNER') == '') $m->addCustomHeader("List-Owner: <" . SettingsHelpers::getSetting('LIST_OWNER') . ">");
                if (SettingsHelpers::getSetting('RETURN_PATH') != '') $m->addCustomHeader("Return-Path: <" . SettingsHelpers::getSetting('RETURN_PATH') . ">");
                if (SettingsHelpers::getSetting('CONTENT_TYPE') == 'html')
                    $m->isHTML(true);
                else
                    $m->isHTML(false);

                $subject = $row->template->name;
                $subject = str_replace('%NAME%', $subscriber->name, $subject);
                $subject = SettingsHelpers::getSetting('RENDOM_REPLACEMENT_SUBJECT') == 1 ? StringHelpers::encodeString($subject) : $subject;

                if (SettingsHelpers::getSetting('CHARSET') != 'utf-8'){
                    $subject = iconv('utf-8', SettingsHelpers::getSetting('CHARSET'), $subject);
                }

                $m->Subject = $subject;

                if (SettingsHelpers::getSetting('SLEEP') > 0) sleep(SettingsHelpers::getSetting('SLEEP'));
                if (SettingsHelpers::getSetting('ORGANIZATION') != '') $m->addCustomHeader("Organization: " . SettingsHelpers::getSetting('ORGANIZATION'));
                if (SettingsHelpers::getSetting('URL') != '') $IMG = '<img border="0" src="' . SettingsHelpers::getSetting('URL') . '/pic/' . $subscriber->id . '/' . $row->templateId . '" width="1" height="1">';

                $m->AddAddress($subscriber->email);

                if (SettingsHelpers::getSetting('REQUEST_REPLY') == 1 && SettingsHelpers::getSetting('EMAIL') != ''){
                    $m->addCustomHeader("Disposition-Notification-To: " . SettingsHelpers::getSetting('EMAIL'));
                    $m->ConfirmReadingTo = SettingsHelpers::getSetting('EMAIL');
                }

                if (SettingsHelpers::getSetting('PRECEDENCE') == 'bulk')
                    $m->addCustomHeader("Precedence: bulk");
                elseif (SettingsHelpers::getSetting('PRECEDENCE') == 'junk')
                    $m->addCustomHeader("Precedence: junk");
                elseif (SettingsHelpers::getSetting('PRECEDENCE') == 'list')
                    $m->addCustomHeader("Precedence: list");

                if (SettingsHelpers::getSetting('URL') != '') $UNSUB = SettingsHelpers::getSetting('URL') . "/unsubscribe/" . $subscriber->id . "/token_" . $subscriber->token;
                $unsublink = str_replace('%UNSUB%', $UNSUB, SettingsHelpers::getSetting('UNSUBLINK'));

                if (SettingsHelpers::getSetting('SHOW_UNSUBSCRIBE_LINK') == 1 && SettingsHelpers::getSetting('UNSUBLINK') != '') {
                    $msg = $row->template->body . "<br><br>" . $unsublink;
                    $m->addCustomHeader("List-Unsubscribe: " . $UNSUB);
                } else
                    $msg = $row->template->body;

                $url_info = parse_url(SettingsHelpers::getSetting('URL'));

                $msg = preg_replace_callback("/%REFERRAL\:(.+)%/isU", function($matches) { return "http://%URL_PATH%?t=referral&ref=" . base64_encode($matches[1]) . "&id=%USERID%"; }, $msg);
                $msg = str_replace('%NAME%', $subscriber->name, $msg);
                $msg = str_replace('%UNSUB%', $UNSUB, $msg);
                $msg = str_replace('%SERVER_NAME%', $url_info['host'], $msg);
                $msg = str_replace('%USERID%',$subscriber->id, $msg);

                $msg = SettingsHelpers::getSetting('RANDOM_REPLACEMENT_BODY') == 1 ? StringHelpers::encodeString($msg) : $msg;

                if (isset($row->template->attach)) {
                    foreach ($row->template->attach as $f) {

                        $path = 'attach/' . $f->name;

                        if (file_exists($path)) {
                            if (SettingsHelpers::getSetting('CHARSET') != 'utf-8') $row['name'] = iconv('utf-8', SettingsHelpers::getSetting('CHARSET'), $f->name);

                            $ext = strrchr($path, ".");
                            $mime_type = StringHelpers::getMimeType($ext);

                            $m->AddAttachment($path, $f->name, 'base64', $mime_type);
                        }
                    }
                }

                if (SettingsHelpers::getSetting('CHARSET') != 'utf-8') $msg = iconv('utf-8', SettingsHelpers::getSetting('CHARSET'), $msg);

                if (SettingsHelpers::getSetting('CONTENT_TYPE') == 2){
                    $msg .= $IMG;
                } else {
                    $msg = preg_replace('/<br(\s\/)?>/i', "\n", $msg);
                    $msg = StringHelpers::removeHtmlTags($msg);
                }

                $m->Body = $msg;

                if (!$m->Send()){
                    $data['subscriberId'] = $subscriber->id;
                    $data['email'] = $subscriber->email;
                    $data['templateId'] = $row->templateId;
                    $data['success'] = 0;
                    $data['errorMsg'] = $m->ErrorInfo;
                    $data['date'] = date('Y-m-d H:i:s');
                    $data['scheduleId'] = $row->id;

                    ReadySent::create($data);

                    $mailcountno = $mailcountno + 1;

                } else {

                    $data['subscriberId'] = $subscriber->id;
                    $data['email'] = $subscriber->email;
                    $data['templateId'] = $row->templateId;
                    $data['success'] = 1;
                    $data['date'] = date('Y-m-d H:i:s');
                    $data['scheduleId'] = $row->id;

                    ReadySent::create($data);

                    Subscribers::where('id', $subscriber->id)->update(['timeSent' => date('Y-m-d H:i:s')]);

                    $mailcount = $mailcount + 1;
                }

                $m->ClearCustomHeaders();
                $m->ClearAllRecipients();
                $m->ClearAttachments();

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