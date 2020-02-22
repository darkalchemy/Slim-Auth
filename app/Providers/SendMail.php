<?php

declare(strict_types=1);

namespace App\Providers;

use App\Factory\LoggerFactory;
use App\Models\Email;
use PHPMailer\PHPMailer\Exception as ExceptionAlias;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

/**
 * Class SendMail.
 */
class SendMail
{
    protected PHPMailer         $mailer;
    protected LoggerInterface   $logger;
    protected int               $user_id;
    protected string            $template;
    protected string            $subject;

    /**
     * SendMail constructor.
     *
     * @param PHPMailer     $mailer
     * @param LoggerFactory $loggerFactory
     */
    public function __construct(PHPMailer $mailer, LoggerFactory $loggerFactory)
    {
        $this->mailer = $mailer;
        $this->logger = $loggerFactory->addFileHandler('sendmail_class.log')->createInstance('sendmail');
    }

    public function send()
    {
        try {
            $this->mailer->send();
        } catch (ExceptionAlias $e) {
            $this->logger->error($e->getMessage());
        } finally {
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            $this->mailer->clearCustomHeaders();
        }
    }

    /**
     * @param string $email    The email address
     * @param string $username The username
     */
    public function addRecipient(string $email, string $username)
    {
        try {
            $this->mailer->addAddress($email, $username);
        } catch (ExceptionAlias $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param string $body The body
     *
     * @throws ExceptionAlias
     */
    public function setMessage(string $body)
    {
        $this->mailer->msgHTML($body);
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        $this->mailer->Subject = $subject;
    }
}
