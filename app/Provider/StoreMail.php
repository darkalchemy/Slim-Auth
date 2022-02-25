<?php

declare(strict_types=1);

namespace App\Provider;

use App\Factory\LoggerFactory;
use App\Model\Email;
use Exception;
use Psr\Log\LoggerInterface;
use Spatie\Url\Url;

/**
 * Class StoreMail.
 */
class StoreMail
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var Email
     */
    protected Email $email;

    /**
     * @var int
     */
    protected int $user_id;

    /**
     * @var string
     */
    protected string $subject;

    /**
     * @var string
     */
    protected string $uri;

    /**
     * @param LoggerFactory $loggerFactory The loggerFactory
     * @param Email         $email         The email
     *
     * @throws Exception
     */
    public function __construct(LoggerFactory $loggerFactory, Email $email)
    {
        $this->logger = $loggerFactory->addFileHandler('storemail_class.log')
            ->createInstance('storemail');
        $this->email = $email;
    }

    public function store(): void
    {
        try {
            $this->email->save();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param int $user_id The user_id
     */
    public function setUserID(int $user_id): void
    {
        $this->email->user_id = $user_id;
    }

    /**
     * @param string $subject The subject
     */
    public function setSubject(string $subject): void
    {
        $this->email->subject = $subject;
    }

    /**
     * @param string $uri  The uri
     * @param string $path The path
     */
    public function setUri(string $uri, string $path): void
    {
        $url              = Url::fromString($uri);
        $this->email->uri = $url->getScheme() . '://' . $url->getHost() . $path;
    }
}
