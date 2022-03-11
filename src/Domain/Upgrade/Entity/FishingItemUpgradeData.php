<?php

namespace App\Domain\Upgrade\Entity;

use JsonSerializable;

class FishingItemUpgradeData implements JsonSerializable
{
    private int $upgradeCode;
    private int $itemGradeCode;
    private int $itemType;  //낚시대: 1, 낚시줄: 2, 릴: 3
    private int $upgradeLevel;
    private int $upgradeItemCode;
    private int $upgradeItemCount;
    private int $moneyCode;
    private int $upgradePrice;
    private int $addProbability;
    private string $createDate;

    /**
     * @return int
     */
    public function getUpgradeCode(): int
    {
        return $this->upgradeCode;
    }

    /**
     * @param int $upgradeCode
     */
    public function setUpgradeCode(int $upgradeCode): void
    {
        $this->upgradeCode = $upgradeCode;
    }

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
    public function getItemType(): int
    {
        return $this->itemType;
    }

    /**
     * @param int $itemType
     */
    public function setItemType(int $itemType): void
    {
        $this->itemType = $itemType;
    }

    /**
     * @return int
     */
    public function getUpgradeLevel(): int
    {
        return $this->upgradeLevel;
    }

    /**
     * @param int $upgradeLevel
     */
    public function setUpgradeLevel(int $upgradeLevel): void
    {
        $this->upgradeLevel = $upgradeLevel;
    }

    /**
     * @return int
     */
    public function getUpgradeItemCode(): int
    {
        return $this->upgradeItemCode;
    }

    /**
     * @param int $upgradeItemCode
     */
    public function setUpgradeItemCode(int $upgradeItemCode): void
    {
        $this->upgradeItemCode = $upgradeItemCode;
    }

    /**
     * @return int
     */
    public function getUpgradeItemCount(): int
    {
        return $this->upgradeItemCount;
    }

    /**
     * @param int $upgradeItemCount
     */
    public function setUpgradeItemCount(int $upgradeItemCount): void
    {
        $this->upgradeItemCount = $upgradeItemCount;
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
    public function getUpgradePrice(): int
    {
        return $this->upgradePrice;
    }

    /**
     * @param int $upgradePrice
     */
    public function setUpgradePrice(int $upgradePrice): void
    {
        $this->upgradePrice = $upgradePrice;
    }

    /**
     * @return int
     */
    public function getAddProbability(): int
    {
        return $this->addProbability;
    }

    /**
     * @param int $addProbability
     */
    public function setAddProbability(int $addProbability): void
    {
        $this->addProbability = $addProbability;
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
            'upgradeCode' => $this->upgradeCode,
            'itemGradeCode' => $this->itemGradeCode,
            'itemType' => $this->itemType,
            'upgradeLevel' => $this->upgradeLevel,
            'upgradeItemCode' => $this->upgradeItemCode,
            'upgradeItemCount' => $this->upgradeItemCount,
            'moneyCode' => $this->moneyCode,
            'upgradePrice' => $this->upgradePrice,
            'addProbability' => $this->addProbability,
            'createDate' => $this->createDate,
        ];
    }
}