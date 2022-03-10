<?php

namespace App\Domain\User\Entity;

use JsonSerializable;

class UserChoiceItemInfo implements JsonSerializable
{
    private int $choiceCode;
    private int $userCode;
    private int $fishingRodCode;
    private int $fishingLineCode;
    private int $fishingNeedleCode;
    private int $fishingBaitCode;
    private int $fishingReelCode;
    private int $fishingItemCode1;
    private int $fishingItemCode2;
    private int $fishingItemCode3;
    private int $fishingItemCode4;
    private string $createDate;

    /**
     * @return int
     */
    public function getChoiceCode(): int
    {
        return $this->choiceCode;
    }

    /**
     * @param int $choiceCode
     */
    public function setChoiceCode(int $choiceCode): void
    {
        $this->choiceCode = $choiceCode;
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
    public function getFishingRodCode(): int
    {
        return $this->fishingRodCode;
    }

    /**
     * @param int $fishingRodCode
     */
    public function setFishingRodCode(int $fishingRodCode): void
    {
        $this->fishingRodCode = $fishingRodCode;
    }

    /**
     * @return int
     */
    public function getFishingLineCode(): int
    {
        return $this->fishingLineCode;
    }

    /**
     * @param int $fishingLineCode
     */
    public function setFishingLineCode(int $fishingLineCode): void
    {
        $this->fishingLineCode = $fishingLineCode;
    }

    /**
     * @return int
     */
    public function getFishingNeedleCode(): int
    {
        return $this->fishingNeedleCode;
    }

    /**
     * @param int $fishingNeedleCode
     */
    public function setFishingNeedleCode(int $fishingNeedleCode): void
    {
        $this->fishingNeedleCode = $fishingNeedleCode;
    }

    /**
     * @return int
     */
    public function getFishingBaitCode(): int
    {
        return $this->fishingBaitCode;
    }

    /**
     * @param int $fishingBaitCode
     */
    public function setFishingBaitCode(int $fishingBaitCode): void
    {
        $this->fishingBaitCode = $fishingBaitCode;
    }

    /**
     * @return int
     */
    public function getFishingReelCode(): int
    {
        return $this->fishingReelCode;
    }

    /**
     * @param int $fishingReelCode
     */
    public function setFishingReelCode(int $fishingReelCode): void
    {
        $this->fishingReelCode = $fishingReelCode;
    }

    /**
     * @return int
     */
    public function getFishingItemCode1(): int
    {
        return $this->fishingItemCode1;
    }

    /**
     * @param int $fishingItemCode1
     */
    public function setFishingItemCode1(int $fishingItemCode1): void
    {
        $this->fishingItemCode1 = $fishingItemCode1;
    }

    /**
     * @return int
     */
    public function getFishingItemCode2(): int
    {
        return $this->fishingItemCode2;
    }

    /**
     * @param int $fishingItemCode2
     */
    public function setFishingItemCode2(int $fishingItemCode2): void
    {
        $this->fishingItemCode2 = $fishingItemCode2;
    }

    /**
     * @return int
     */
    public function getFishingItemCode3(): int
    {
        return $this->fishingItemCode3;
    }

    /**
     * @param int $fishingItemCode3
     */
    public function setFishingItemCode3(int $fishingItemCode3): void
    {
        $this->fishingItemCode3 = $fishingItemCode3;
    }

    /**
     * @return int
     */
    public function getFishingItemCode4(): int
    {
        return $this->fishingItemCode4;
    }

    /**
     * @param int $fishingItemCode4
     */
    public function setFishingItemCode4(int $fishingItemCode4): void
    {
        $this->fishingItemCode4 = $fishingItemCode4;
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
            'choiceCode' => $this->choiceCode,
            'userCode' => $this->userCode,
            'fishingRodCode' => $this->fishingRodCode,
            'fishingLineCode' => $this->fishingLineCode,
            'fishingNeedleCode' => $this->fishingNeedleCode,
            'fishingBaitCode' => $this->fishingBaitCode,
            'fishingReelCode' => $this->fishingReelCode,
            'fishingItemCode1' => $this->fishingItemCode1,
            'fishingItemCode2' => $this->fishingItemCode2,
            'fishingItemCode3' => $this->fishingItemCode3,
            'fishingItemCode4' => $this->fishingItemCode4,
            'createDate' => $this->createDate,
        ];
    }
}