<?php

namespace App\Domain\Map\Entity;

use JsonSerializable;

class MapInfoData implements JsonSerializable
{
    private int $mapCode;
    private string $mapName;
    private int $maxDepth;
    private int $minLevel;
    private int $distance;
    private int $moneyCode;
    private int $departurePrice;
    private int $departureTime;
    private int $perDurability;
    private int $mapFishCount;
    private int $fishSizeProbability;
    private string $createDate;

    /**
     * @return int
     */
    public function getMapCode(): int
    {
        return $this->mapCode;
    }

    /**
     * @param int $mapCode
     */
    public function setMapCode(int $mapCode): void
    {
        $this->mapCode = $mapCode;
    }

    /**
     * @return string
     */
    public function getMapName(): string
    {
        return $this->mapName;
    }

    /**
     * @param string $mapName
     */
    public function setMapName(string $mapName): void
    {
        $this->mapName = $mapName;
    }

    /**
     * @return int
     */
    public function getMaxDepth(): int
    {
        return $this->maxDepth;
    }

    /**
     * @param int $maxDepth
     */
    public function setMaxDepth(int $maxDepth): void
    {
        $this->maxDepth = $maxDepth;
    }

    /**
     * @return int
     */
    public function getMinLevel(): int
    {
        return $this->minLevel;
    }

    /**
     * @param int $minLevel
     */
    public function setMinLevel(int $minLevel): void
    {
        $this->minLevel = $minLevel;
    }

    /**
     * @return int
     */
    public function getDistance(): int
    {
        return $this->distance;
    }

    /**
     * @param int $distance
     */
    public function setDistance(int $distance): void
    {
        $this->distance = $distance;
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
    public function getDeparturePrice(): int
    {
        return $this->departurePrice;
    }

    /**
     * @param int $departurePrice
     */
    public function setDeparturePrice(int $departurePrice): void
    {
        $this->departurePrice = $departurePrice;
    }

    /**
     * @return int
     */
    public function getDepartureTime(): int
    {
        return $this->departureTime;
    }

    /**
     * @param int $departureTime
     */
    public function setDepartureTime(int $departureTime): void
    {
        $this->departureTime = $departureTime;
    }

    /**
     * @return int
     */
    public function getPerDurability(): int
    {
        return $this->perDurability;
    }

    /**
     * @param int $perDurability
     */
    public function setPerDurability(int $perDurability): void
    {
        $this->perDurability = $perDurability;
    }

    /**
     * @return int
     */
    public function getMapFishCount(): int
    {
        return $this->mapFishCount;
    }

    /**
     * @param int $mapFishCount
     */
    public function setMapFishCount(int $mapFishCount): void
    {
        $this->mapFishCount = $mapFishCount;
    }

    /**
     * @return int
     */
    public function getFishSizeProbability(): int
    {
        return $this->fishSizeProbability;
    }

    /**
     * @param int $fishSizeProbability
     */
    public function setFishSizeProbability(int $fishSizeProbability): void
    {
        $this->fishSizeProbability = $fishSizeProbability;
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
            'mapCode' => $this->mapCode,
            'mapName' => $this->mapName,
            'maxDepth' => $this->maxDepth,
            'minLevel' => $this->minLevel,
            'distance' => $this->distance,
            'moneyCode' => $this->moneyCode,
            'departurePrice' => $this->departurePrice,
            'departureTime' => $this->departureTime,
            'perDurability' => $this->perDurability,
            'mapFishCount' => $this->mapFishCount,
            'fishSizeProbability' => $this->fishSizeProbability,
            'createDate' => $this->createDate,
        ];
    }
}