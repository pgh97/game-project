<?php
declare(strict_types=1);

namespace App\Application\Actions;

use JsonSerializable;

class ActionError implements JsonSerializable
{
    public const BAD_REQUEST = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR = 'SERVER_ERROR';
    public const UNAUTHENTICATED = 'UNAUTHENTICATED';
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const VERIFICATION_ERROR = 'VERIFICATION_ERROR';

    private string $type;

    private ?string $message;

    private string $description;

    public function __construct(string $type, ?string $description, ?string $message = null)
    {
        $this->type = $type;
        $this->description = $description;
        $this->message = $message;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function setDescription(?string $description = null): self
    {
        $this->description = $description;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'errorCode' => $this->type,
            'message' => $this->message,
            'description' => $this->description,
        ];
    }
}
