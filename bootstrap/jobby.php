<?php

declare(strict_types=1);

use App\Models\Email;
use App\Providers\SendMail;
use Carbon\Carbon;
use Jobby\Jobby;

$app = require_once __DIR__ . '/app.php';
$jobby = $app->getContainer()->get(Jobby::class);

$jobby->add('Send Email', [
    'runAs' => 'www-data',
    'command' => function () {
        $container = (require_once __DIR__ . '/app.php')->getContainer();
        $email = $container->get(Email::class);
        $emails = $email->with('user')->where('sent', 0)->orderBy('priority')->orderBy('created_at')->take(10)->get();
        $sendmail = $container->get(Sendmail::class);
        foreach ($emails as $item) {
            $sendmail->addRecipient($item->user->email, $item->user->username);
            $sendmail->setEmailSubject($item->subject);
            $sendmail->setMessage($item->body);

            try {
                // TODO clean this up
                $sendmail->send();
                $update = $email->find($item->id);
                $update->sent = 1;
                $update->send_count = $item->send_count + 1;
                $update->date_sent = Carbon::now();
                $update->save();
            } catch (Exception $e) {
                $email->find($item->id)->increment('error_count');
                echo 'SendMail Failed: ' . $e->getMessage();
            }
        }

        return true;
    },
    'schedule' => '* * * * *',
    'output' => __DIR__ . '/../var/logs/sendmail.log',
    'enabled' => true,
]);

$jobby->run();
