<?php
declare(strict_types=1);

namespace App\Application\Actions;

use JsonSerializable;

class ActionPayload implements JsonSerializable
{
    private int $statusCode;

    /**
     * @var array|object|null
     */
    private $data;

    private ?string $message;

    private ?ActionError $error;

    public function __construct(
        int $statusCode = 200,
            $data = null,
        ?string $message = null,
        ?ActionError $error = null
    ) {
        $this->statusCode = $statusCode;
        $this->data = $data;
        $this->error = $error;
        $this->message = $message;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array|null|object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getError(): ?ActionError
    {
        return $this->error;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        $payload = [];

        if ($this->data !== null) {
            $payload = [
                'statusCode' => $this->statusCode,
                'message' => $this->message,
            ];
            $payload['result'] = $this->data;
        } elseif ($this->error !== null) {
            $payload['error'] = $this->error;
        }

        return $payload;
    }
}
