<?php

declare(strict_types=1);

namespace App\Providers;

use App\Factory\LoggerFactory;
use App\Models\Email;
use Exception;
use Psr\Log\LoggerInterface;

class StoreMail
{
    protected LoggerInterface   $logger;
    protected Email             $email;
    protected int               $user_id;
    protected string            $template;
    protected string            $subject;

    /**
     * SendMail constructor.
     */
    public function __construct(LoggerFactory $loggerFactory, Email $email)
    {
        $this->logger = $loggerFactory->addFileHandler('storemail_class.log')->createInstance('storemail');
        $this->email  = $email;
    }

    public function store()
    {
        try {
            $this->email->save();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    public function setUserID(int $user_id)
    {
        $this->email->user_id = $user_id;
    }

    public function setSubject(string $subject)
    {
        $this->email->subject = $subject;
    }

    public function setBody(string $body)
    {
        $this->email->body = $body;
    }
}
