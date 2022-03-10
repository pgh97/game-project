<?php

namespace App\Domain\Common\Entity\Ship;

use JsonSerializable;

class shipInfoData implements JsonSerializable
{
    private int $shipCode;
    private string $shipName;
    private int $durability;
    private int $fuel;
    private int $maxUpgrade;
    private string $createDate;

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
     * @return string
     */
    public function getShipName(): string
    {
        return $this->shipName;
    }

    /**
     * @param string $shipName
     */
    public function setShipName(string $shipName): void
    {
        $this->shipName = $shipName;
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
            'shipCode' => $this->shipCode,
            'shipName' => $this->shipName,
            'durability' => $this->durability,
            'fuel' => $this->fuel,
            'maxUpgrade' => $this->maxUpgrade,
            'createDate' => $this->createDate,
        ];
    }
}