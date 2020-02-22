<?php

declare(strict_types=1);

namespace App\Providers;

use App\Factory\LoggerFactory;
use App\Models\Email;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class StoreMail.
 */
class StoreMail
{
    protected LoggerInterface   $logger;
    protected Email             $email;
    protected int               $user_id;
    protected string            $template;
    protected string            $subject;

    /**
     * SendMail constructor.
     *
     * @param LoggerFactory $loggerFactory
     * @param Email         $email
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

    /**
     * @param int $user_id
     */
    public function setUserID(int $user_id)
    {
        $this->email->user_id = $user_id;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        $this->email->subject = $subject;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body)
    {
        $this->email->body = $body;
    }
}
