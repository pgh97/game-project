<?php

namespace App\Domain\Common\Entity\fish;

use JsonSerializable;

class FishGradeData implements JsonSerializable
{
    private int $fishGradeCode;
    private int $fishCode;
    private int $gradeCode;
    private string $gradeName;
    private int $minValue;
    private int $maxValues;
    private int $addExperience;
    private int $moneyCode;
    private int $minPrice;
    private int $maxPrice;
    private string $createDate;

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
    public function getFishCode(): int
    {
        return $this->fishCode;
    }

    /**
     * @param int $fishCode
     */
    public function setFishCode(int $fishCode): void
    {
        $this->fishCode = $fishCode;
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
    public function getGradeName(): int
    {
        return $this->gradeName;
    }

    /**
     * @param int $gradeName
     */
    public function setGradeName(int $gradeName): void
    {
        $this->gradeName = $gradeName;
    }

    /**
     * @return int
     */
    public function getMinValue(): int
    {
        return $this->minValue;
    }

    /**
     * @param int $minValue
     */
    public function setMinValue(int $minValue): void
    {
        $this->minValue = $minValue;
    }

    /**
     * @return int
     */
    public function getMaxValues(): int
    {
        return $this->maxValues;
    }

    /**
     * @param int $maxValues
     */
    public function setMaxValues(int $maxValues): void
    {
        $this->maxValues = $maxValues;
    }

    /**
     * @return int
     */
    public function getAddExperience(): int
    {
        return $this->addExperience;
    }

    /**
     * @param int $addExperience
     */
    public function setAddExperience(int $addExperience): void
    {
        $this->addExperience = $addExperience;
    }

    /**
     * @return int
     */
    public function getMoneyCode(): int
    {
        return $this->moneyCode;
    }

    /**
     * @param int $moneyCode
     */
    public function setMoneyCode(int $moneyCode): void
    {
        $this->moneyCode = $moneyCode;
    }

    /**
     * @return int
     */
    public function getMinPrice(): int
    {
        return $this->minPrice;
    }

    /**
     * @param int $minPrice
     */
    public function setMinPrice(int $minPrice): void
    {
        $this->minPrice = $minPrice;
    }

    /**
     * @return int
     */
    public function getMaxPrice(): int
    {
        return $this->maxPrice;
    }

    /**
     * @param int $maxPrice
     */
    public function setMaxPrice(int $maxPrice): void
    {
        $this->maxPrice = $maxPrice;
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
            'fishGradeCode' => $this->fishGradeCode,
            'fishCode' => $this->fishCode,
            'gradeCode' => $this->gradeCode,
            'gradeName' => $this->gradeName,
            'minValue' => $this->minValue,
            'maxValue' => $this->maxValues,
            'addExperience' => $this->addExperience,
            'moneyCode' => $this->moneyCode,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'createDate' => $this->createDate,
        ];
    }
}