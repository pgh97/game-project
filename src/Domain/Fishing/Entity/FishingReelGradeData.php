<?php

namespace App\Domain\Fishing\Entity;

use JsonSerializable;

class FishingReelGradeData implements JsonSerializable
{
    private int $itemGradeCode;
    private int $itemCode;
    private int $gradeCode;
    private int $durability;
    private int $reelNumber;
    private int $reelWindingAmount;
    private int $maxUpgrade;
    private string $createDate;

    /**
     * @return int
     */
    public function getItemGradeCode(): int
    {
        return $this->itemGradeCode;
    }

    /**
     * @param int $itemGradeCode
     */
    public function setItemGradeCode(int $itemGradeCode): void
    {
        $this->itemGradeCode = $itemGradeCode;
    }

    /**
     * @return int
     */
    public function getItemCode(): int
    {
        return $this->itemCode;
    }

    /**
     * @param int $itemCode
     */
    public function setItemCode(int $itemCode): void
    {
        $this->itemCode = $itemCode;
    }

    /**
     * @return int
     */
    public function getGradeCode(): int
    {
        return $this->gradeCode;
    }

    /**
     * @param int $gradeCode
     */
    public function setGradeCode(int $gradeCode): void
    {
        $this->gradeCode = $gradeCode;
    }

    /**
     * @return int
     */
    public function getDurability(): int
    {
        return $this->durability;
    }

    /**
     * @param int $durability
     */
    public function setDurability(int $durability): void
    {
        $this->durability = $durability;
    }

    /**
     * @return int
     */
    public function getReelNumber(): int
    {
        return $this->reelNumber;
    }

    /**
     * @param int $reelNumber
     */
    public function setReelNumber(int $reelNumber): void
    {
        $this->reelNumber = $reelNumber;
    }

    /**
     * @return int
     */
    public function getReelWindingAmount(): int
    {
        return $this->reelWindingAmount;
    }

    /**
     * @param int $reelWindingAmount
     */
    public function setReelWindingAmount(int $reelWindingAmount): void
    {
        $this->reelWindingAmount = $reelWindingAmount;
    }

    /**
     * @return int
     */
    public function getMaxUpgrade(): int
    {
        return $this->maxUpgrade;
    }

    /**
     * @param int $maxUpgrade
     */
    public function setMaxUpgrade(int $maxUpgrade): void
    {
        $this->maxUpgrade = $maxUpgrade;
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
            'itemGradeCode' => $this->itemGradeCode,
            'itemCode' => $this->itemCode,
            'gradeCode' => $this->gradeCode,
            'durability' => $this->durability,
            'reelNumber' => $this->reelNumber,
            'reelWindingAmount' => $this->reelWindingAmount,
            'maxUpgrade' => $this->maxUpgrade,
            'createDate' => $this->createDate,
        ];
    }
}