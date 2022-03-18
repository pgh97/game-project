<?php

namespace App\Domain\User\Entity;

use JsonSerializable;

class UserShipInfo implements JsonSerializable
{
    private int $userCode;
    private int $shipCode;
    private int $durability;
    private int $fuel;
    private int $upgradeCode=0;
    private int $upgradeLevel=0;
    private string $createDate;

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
    public function getFuel(): int
    {
        return $this->fuel;
    }

    /**
     * @param int $fuel
     */
    public function setFuel(int $fuel): void
    {
        $this->fuel = $fuel;
    }

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
            'userCode' => $this->userCode,
            'shipCode' => $this->shipCode,
            'durability' => $this->durability,
            'fuel' => $this->fuel,
            'upgradeCode' => $this->upgradeCode,
            'upgradeLevel' => $this->upgradeLevel,
            'createDate' => $this->createDate,
        ];
    }
}