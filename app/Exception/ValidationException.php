<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

/**
 * Class ValidationException.
 */
class ValidationException extends Exception
{
    protected array $errors;
    protected string $path;

    /**
     * ValidationException constructor.
     *
     * @param array  $errors The errors
     * @param string $path   The path
     */
    public function __construct(array $errors, string $path)
    {
        parent::__construct();
        $this->errors = $errors;
        $this->path   = $path;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
