<?php

namespace App\Exceptions;

class WebhookException extends \Exception
{
    private ?string $eventType;
    private int $statusCode;

    public function __construct(string $message, int $statusCode = 400, ?\Throwable $previous = null, ?string $eventType = null)
    {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->eventType = $eventType;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }
}