<?php

namespace App\Domain\User\Entity;

use JsonSerializable;

class UserFishDictionary implements JsonSerializable
{
    private int $mapFishCode;
    private int $userCode;
    private string $createDate;

    /**
     * @return int
     */
    public function getMapFishCode(): int
    {
        return $this->mapFishCode;
    }

    /**
     * @param int $mapFishCode
     */
    public function setMapFishCode(int $mapFishCode): void
    {
        $this->mapFishCode = $mapFishCode;
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
        return [
            'mapFishCode' => $this->mapFishCode,
            'userCode'=> $this->userCode,
            'createDate' => $this->createDate,
        ];
    }
}