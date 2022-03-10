<?php

namespace App\Domain\Fishing\Entity;

use JsonSerializable;

class FishingLineGradeData implements JsonSerializable
{
    private int $itemGradeCode;
    private int $itemCode;
    private int $gradeCode;
    private int $durability;
    private int $suppressProbability;
    private int $hookingProbability;
    private int $maxWeight;
    private int $minWeight;
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
     * @return int
     */
    public function getMaxWeight(): int
    {
        return $this->maxWeight;
    }

    /**
     * @param int $maxWeight
     */
    public function setMaxWeight(int $maxWeight): void
    {
        $this->maxWeight = $maxWeight;
    }

    /**
     * @return int
     */
    public function getMinWeight(): int
    {
        return $this->minWeight;
    }

    /**
     * @param int $minWeight
     */
    public function setMinWeight(int $minWeight): void
    {
        $this->minWeight = $minWeight;
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
            'suppressProbability' => $this->suppressProbability,
            'hookingProbability' => $this->hookingProbability,
            'maxWeight' => $this->maxWeight,
            'minWeight' => $this->minWeight,
            'maxUpgrade' => $this->maxUpgrade,
            'createDate' => $this->createDate,
        ];
    }
}