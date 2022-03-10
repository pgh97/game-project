<?php

namespace App\Domain\Quest\Entity;

use JetBrains\PhpStorm\Internal\TentativeType;
use JsonSerializable;

class QuestCompensationData implements JsonSerializable
{
    private int $questCode;
    private int $compensationCode;
    private string $createDate;

    /**
     * @return int
     */
    public function getQuestCode(): int
    {
        return $this->questCode;
    }

    /**
     * @param int $questCode
     */
    public function setQuestCode(int $questCode): void
    {
        $this->questCode = $questCode;
    }

    /**
     * @return int
     */
    public function getCompensationCode(): int
    {
        return $this->compensationCode;
    }

    /**
     * @param int $compensationCode
     */
    public function setCompensationCode(int $compensationCode): void
    {
        $this->compensationCode = $compensationCode;
    }

    /**
     * @return string
     */
    public function getCreateDate(): string
    {
        return $this->createDate;
    }

    /**
     * @param string $createDate
     */
    public function setCreateDate(string $createDate): void
    {
        $this->createDate = $createDate;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'questCode' => $this->questCode,
            'compensationCode' => $this->compensationCode,
            'createDate' => $this->createDate,
        ];
    }
}