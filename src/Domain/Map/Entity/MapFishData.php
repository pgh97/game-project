<?php

namespace App\Domain\Map\Entity;

use JsonSerializable;

class MapFishData implements JsonSerializable
{
    private int $mapFishCode;
    private int $mapCode;
    private int $fishCode;
    private string $createDate;

    /**
     * @return int
     */
    public function getMapFishCode(): int
    {
        return $this->mapFishCode;
    }

    /**
     * @param int $mapFishCode
     */
    public function setMapFishCode(int $mapFishCode): void
    {
        $this->mapFishCode = $mapFishCode;
    }

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
            'mapFishCode' => $this->mapFishCode,
            'mapCode' => $this->mapCode,
            'fishCode' => $this->fishCode,
            'createDate' => $this->createDate,
        ];
    }
}