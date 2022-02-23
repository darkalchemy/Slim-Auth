<?php

declare(strict_types=1);

use App\Factory\LoggerFactory;
use Jobby\Jobby;

$container        = (require __DIR__ . '/../bootstrap/app.php')->getContainer();
$jobby            = $container->get(Jobby::class);
$loggerFactory    = $container->get(LoggerFactory::class);
$logger           = $loggerFactory->addFileHandler('jobby.log')->createInstance('jobby');
$sendmail_enabled = false;

try {
    $sendmail_enabled = (bool) $container->get('settings')['mail']['smtp_enable'];
} catch (Exception $e) {
    $logger->error($e->getMessage());
}

if ($sendmail_enabled) {
    $jobby->add('Send Email', [
        'runAs'    => 'www-data',
        'command'  => sendEmail($container),
        'schedule' => '* * * * *',
        'output'   => LOGS_DIR . 'sendmail_jobby.log',
        'enabled'  => true,
    ]);
} else {
    $logger->error('SendMail disabled.');
}

$jobby->run();
