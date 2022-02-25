<?php

declare(strict_types=1);

namespace App\Provider;

use App\Factory\LoggerFactory;
use Exception;
use PHPMailer\PHPMailer\Exception as ExceptionAlias;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

/**
 * Class SendMail.
 */
class SendMail
{
    /**
     * @var PHPMailer
     */
    protected PHPMailer $mailer;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var int
     */
    protected int $user_id;

    /**
     * @var string
     */
    protected string $template;

    /**
     * @var string
     */
    protected string $subject;

    /**
     * @param PHPMailer     $mailer        The mailer
     * @param LoggerFactory $loggerFactory The loggerFactory
     *
     * @throws Exception
     */
    public function __construct(PHPMailer $mailer, LoggerFactory $loggerFactory)
    {
        $this->mailer = $mailer;
        $this->logger = $loggerFactory->addFileHandler('sendmail_class.log')
            ->createInstance('sendmail');
    }

    public function send(): void
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
    public function addRecipient(string $email, string $username): void
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
    public function setMessage(string $body): void
    {
        $this->mailer->msgHTML($body);
    }

    /**
     * @param string $subject The subject
     */
    public function setSubject(string $subject): void
    {
        $this->mailer->Subject = $subject;
    }
}
