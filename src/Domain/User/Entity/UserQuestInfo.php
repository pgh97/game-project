<?php

namespace App\Domain\User\Entity;

use JsonSerializable;

class UserQuestInfo implements JsonSerializable
{
    private int $questCode;
    private int $userCode;
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
    public function getUserCode(): int
    {
        return $this->userCode;
    }

    /**
     * @param int $userCode
     */
    public function setUserCode(int $userCode): void
    {
        $this->userCode = $userCode;
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
        return[
            'questCode' => $this->questCode,
            'userCode' => $this->userCode,
            'createDate' => $this->createDate,
        ];
    }
}