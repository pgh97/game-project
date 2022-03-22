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

    private ?array $message;

    private ?ActionError $error;

    public function __construct(
        int $statusCode = 200,
            $data = null,
        ?array $message = null,
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
     * @return array|string|null
     */
    public function getMessage()
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
            if(!empty($this->message)){
                $payload = [
                    'httpStatusCode' => $this->statusCode,
                    'statusCode' => $this->message['statusCode'],
                    'message' => $this->message['message'],
                ];
            }else{
                $payload = [
                    'httpStatusCode' => $this->statusCode,
                    'statusCode' => null,
                    'message' => null,
                ];
            }
            $payload['result'] = $this->data;
        } elseif ($this->error !== null) {
            $payload = [
                'httpStatusCode' => $this->statusCode,
            ];
            $payload['error'] = $this->error;
        }

        return $payload;
    }
}
