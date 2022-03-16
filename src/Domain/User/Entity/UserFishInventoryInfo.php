<?php

namespace App\Domain\User\Entity;

use JsonSerializable;

class UserFishInventoryInfo implements JsonSerializable
{
    private int $fishInventoryCode=0;
    private int $userCode;
    private int $mapCode;
    private int $fishGradeCode;
    private string $createDate;

    /**
     * @return int
     */
    public function getFishInventoryCode(): int
    {
        return $this->fishInventoryCode;
    }

    /**
     * @param int $fishInventoryCode
     */
    public function setFishInventoryCode(int $fishInventoryCode): void
    {
        $this->fishInventoryCode = $fishInventoryCode;
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
     * @return int
     */
    public function getMapCode(): int
    {
        return $this->mapCode;
    }

    /**
     * @param int $mapCode
     */
    public function setMapCode(int $mapCode): void
    {
        $this->mapCode = $mapCode;
    }

    /**
     * @return int
     */
    public function getFishGradeCode(): int
    {
        return $this->fishGradeCode;
    }

    /**
     * @param int $fishGradeCode
     */
    public function setFishGradeCode(int $fishGradeCode): void
    {
        $this->fishGradeCode = $fishGradeCode;
    }

    /**
     * @return int
     */
    public function getCreateDate(): int
    {
        return $this->createDate;
    }

    /**
     * @param int $createDate
     */
    public function setCreateDate(int $createDate): void
    {
        $this->createDate = $createDate;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'fishInventoryCode' => $this->fishInventoryCode,
            'userCode' => $this->userCode,
            'mapCode' => $this->mapCode,
            'fishGradeCode' => $this->fishGradeCode,
            'createDate' => $this->createDate,
        ];
    }
}