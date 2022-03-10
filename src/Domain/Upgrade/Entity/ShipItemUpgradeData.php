<?php

namespace App\Domain\Upgrade\Entity;

use JsonSerializable;

class ShipItemUpgradeData implements JsonSerializable
{
    private int $upgradeCode;
    private int $shipCode;
    private int $upgradeLevel;
    private int $moneyCode;
    private int $upgradePrice;
    private int $addFuel;
    private int $addProbability;
    private int $upgradeProbability;
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
    public function getShipCode(): int
    {
        return $this->shipCode;
    }

    /**
     * @param int $shipCode
     */
    public function setShipCode(int $shipCode): void
    {
        $this->shipCode = $shipCode;
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
    public function getAddFuel(): int
    {
        return $this->addFuel;
    }

    /**
     * @param int $addFuel
     */
    public function setAddFuel(int $addFuel): void
    {
        $this->addFuel = $addFuel;
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
     * @return int
     */
    public function getUpgradeProbability(): int
    {
        return $this->upgradeProbability;
    }

    /**
     * @param int $upgradeProbability
     */
    public function setUpgradeProbability(int $upgradeProbability): void
    {
        $this->upgradeProbability = $upgradeProbability;
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
            'shipCode' => $this->shipCode,
            'upgradeLevel' => $this->upgradeLevel,
            'moneyCode' => $this->moneyCode,
            'upgradePrice' => $this->upgradePrice,
            'addFuel' => $this->addFuel,
            'addProbability' => $this->addProbability,
            'upgradeProbability' => $this->upgradeProbability,
            'createDate' => $this->createDate,
        ];
    }
}