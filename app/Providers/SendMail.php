<?php

namespace App\Providers;

use App\Factory\LoggerFactory;
use App\Models\Email;
use Exception;
use PHPMailer\PHPMailer\Exception as ExceptionAlias;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

/**
 * Class SendMail
 *
 * @package App\Providers
 */
class SendMail
{
    protected PHPMailer         $mailer;
    protected LoggerInterface   $logger;
    protected Email             $email;
    protected int               $user_id;
    protected string            $template;
    protected string            $fillables;
    protected string            $subject;

    /**
     * SendMail constructor.
     *
     * @param PHPMailer     $mailer
     * @param LoggerFactory $loggerFactory
     * @param Email         $email
     */
    public function __construct(PHPMailer $mailer, LoggerFactory $loggerFactory, Email $email)
    {
        $this->mailer = $mailer;
        $this->logger = $loggerFactory->addFileHandler('sendmail_class.log')
            ->createInstance('sendmail');
        $this->email = $email;
    }

    /**
     *
     */
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
     * @return int
     */
    public function getUserID()
    {
        return $this->email->user_id;
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
    public function setEmailSubject(string $subject)
    {
        $this->mailer->Subject = $subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        $this->email->subject = $subject;
    }

    public function setBody(string $body)
    {
        $this->email->body = $body;
    }
}
