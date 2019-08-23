<?php

namespace App\Helper;

use PHPMailer\PHPMailer;

class ActionHelpers
{
    public static function sendEmail($email, $subject, $body, $prior)
    {
        $user = 'USERNAME';
        $subject = str_replace('%NAME%', $user, $subject);
        $subject = core::getSetting('replacement_chars_subject') == 'yes' ? Pnl::encodeString($subject) : $subject;

        $m = new PHPMailer\PHPMailer();

        if (core::getSetting('how_to_send') == 2){
            $m->IsSMTP();

            $m->SMTPAuth = true;
            $m->SMTPKeepAlive = true;
            $m->Host = core::getSetting('smtp_host');
            $m->Port = core::getSetting('smtp_port');
            $m->Username = core::getSetting('smtp_username');
            $m->Password = core::getSetting('smtp_password');

            if (core::getSetting('smtp_secure') == 'ssl')
                $m->SMTPSecure  = 'ssl';
            elseif (core::getSetting('smtp_secure') == 'tls')
                $m->SMTPSecure  = 'tls';

            if (core::getSetting('smtp_aut') == 'plain')
                $m->AuthType = 'PLAIN';
            elseif (core::getSetting('smtp_aut') == 'cram-md5')
                $m->AuthType = 'CRAM-MD5';

            $m->Timeout =  core::getSetting('smtp_timeout');
        } elseif (core::getSetting('how_to_send') == 3 && core::getSetting('sendmail') != ''){
            $m->IsSendmail();
            $m->Sendmail = core::getSetting('sendmail');
        } else {
            $m->IsMail();
        }

        $query = "SELECT * FROM " . core::database()->getTableName('charset') . " WHERE id_charset=" . core::getSetting('id_charset');
        $result = core::database()->querySQL($query);
        $char = core::database()->getRow($result);
        $charset = $char['charset'];

        $m->CharSet = $charset;

        if (core::getSetting('email_name') == '')
            $from = $_SERVER["SERVER_NAME"];
        else
            $from = core::getSetting('email_name');

        $organization = core::getSetting('organization');

        if ($charset != 'utf-8') {
            $from = iconv('utf-8', $charset, $from);
            $subject = iconv('utf-8', $charset, $subject);
            if (!empty($organization)) $organization = iconv('utf-8', $charset, $organization);
        }

        $m->Subject = $subject;
        if (core::getSetting('organization') != '') $m->addCustomHeader("Organization: " . core::getSetting('organization'));

        if ($prior == 1)
            $m->Priority = 1;
        elseif ($prior == 2)
            $m->Priority = 2;
        else
            $m->Priority = 3;

        if (core::getSetting('show_email') == "no")
            $m->From = "noreply@" . $_SERVER['SERVER_NAME'];
        else
            $m->From = core::getSetting('email');

        $m->FromName = $from;

        if (core::getSetting('content_type') == 2)
            $m->isHTML(true);
        else
            $m->isHTML(false);

        $m->AddAddress($email);

        if (core::getSetting('request_reply') == "yes" && core::getSetting('email_reply') != ''){
            $m->addCustomHeader("Disposition-Notification-To: " . core::getSetting('email_reply'));
            $m->ConfirmReadingTo = core::getSetting('email_reply');
        }

        foreach($this->getCustomHeaders() as $row) {
            if (!empty($row['name']) && !empty($row['value'])) $m->addCustomHeader($row['name'] . ":" . $row['value']);
        }

        if (core::getSetting('precedence') == 'bulk')
            $m->addCustomHeader("Precedence: bulk");
        elseif (core::getSetting('precedence') == 'junk')
            $m->addCustomHeader("Precedence: junk");
        elseif (core::getSetting('precedence') == 'list')
            $m->addCustomHeader("Precedence: list");
        if (core::getSetting('list_owner') != '') $m->addCustomHeader("List-Owner: <" . core::getSetting('list_owner') . ">");
        if (core::getSetting('return_path') != '') $m->addCustomHeader("Return-Path: <" . core::getSetting('return_path') . ">");

        $UNSUB = "http://" . $_SERVER["SERVER_NAME"] . Pnl::root() . "?t=unsubscribe&id=test&token=test";
        $unsublink = str_replace('%UNSUB%', $UNSUB, core::getSetting('unsublink'));

        if (core::getSetting('show_unsubscribe_link') == "yes" && core::getSetting('unsublink') != '') {
            $msg = "" . $body . "<br><br>" . $unsublink;
            $m->addCustomHeader("List-Unsubscribe: " . $UNSUB);
        } else
            $msg = $body;

        $msg = preg_replace_callback("/%REFERRAL\:(.+)%/isU", function($matches) { return "http://%URL_PATH%?t=referral&ref=" . base64_encode($matches[1]) . "&id=%USERID%"; }, $msg);
        $msg = str_replace('%NAME%', $user, $msg);
        $msg = str_replace('%EMAIL%', $email, $msg);
        $msg = str_replace('%UNSUB%', $UNSUB, $msg);
        $msg = str_replace('%SERVER_NAME%', $_SERVER['SERVER_NAME'], $msg);
        $msg = str_replace('%USERID%', 0, $msg);
        $msg = str_replace('%URL_PATH%', $_SERVER["SERVER_NAME"] . Pnl::root(), $msg);
        $msg = core::getSetting('replacement_chars_body') == 'yes' ? Pnl::encodeString($msg) : $msg;

        if ($charset != 'utf-8') $msg = iconv('utf-8', $charset, $msg);
        if (core::getSetting('content_type') == 1) {
            $msg = preg_replace('/<br(\s\/)?>/i', "\n", $msg);
            $msg = Pnl::remove_html_tags($msg);
        }

        $m->Body = $msg;

        if (!$m->Send()){
            if (core::getSetting('how_to_send') == 2) $m->SmtpClose();
            return false;
        } else {
            if (core::getSetting('how_to_send') == 2) $m->SmtpClose();
            return true;
        }
    }
}