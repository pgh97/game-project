<?php

namespace App\Domain\Fishing\Entity;

use JsonSerializable;

class FishingBaitGradeData implements JsonSerializable
{
    private int $itemGradeCode;
    private int $itemCode;
    private int $gradeCode;
    private int $fishProbability;
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
    public function getFishProbability(): int
    {
        return $this->fishProbability;
    }

    /**
     * @param int $fishProbability
     */
    public function setFishProbability(int $fishProbability): void
    {
        $this->fishProbability = $fishProbability;
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
            'fishProbability' => $this->fishProbability,
            'createDate' => $this->createDate,
        ];
    }
}