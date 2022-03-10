<?php

namespace App\Domain\Fishing\Entity;

use JsonSerializable;

class FishingNeedleGradeData implements JsonSerializable
{
    private int $itemGradeCode;
    private int $itemCode;
    private int $gradeCode;
    private int $suppressProbability;
    private int $hookingProbability;
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
    public function getSuppressProbability(): int
    {
        return $this->suppressProbability;
    }

    /**
     * @param int $suppressProbability
     */
    public function setSuppressProbability(int $suppressProbability): void
    {
        $this->suppressProbability = $suppressProbability;
    }

    /**
     * @return int
     */
    public function getHookingProbability(): int
    {
        return $this->hookingProbability;
    }

    /**
     * @param int $hookingProbability
     */
    public function setHookingProbability(int $hookingProbability): void
    {
        $this->hookingProbability = $hookingProbability;
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
            'suppressProbability' => $this->suppressProbability,
            'hookingProbability' => $this->hookingProbability,
            'createDate' => $this->createDate,
        ];
    }
}