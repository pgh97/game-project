<?php

namespace App\Domain\Map\Entity;

use JsonSerializable;

class MapTideData implements JsonSerializable
{
    private int $mapTideCode;
    private int $mapCode;
    private int $tideCode;
    private int $tideSort;
    private string $createDate;

    /**
     * @return int
     */
    public function getMapTideCode(): int
    {
        return $this->mapTideCode;
    }

    /**
     * @param int $mapTideCode
     */
    public function setMapTideCode(int $mapTideCode): void
    {
        $this->mapTideCode = $mapTideCode;
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
    public function getTideCode(): int
    {
        return $this->tideCode;
    }

    /**
     * @param int $tideCode
     */
    public function setTideCode(int $tideCode): void
    {
        $this->tideCode = $tideCode;
    }

    /**
     * @return int
     */
    public function getTideSort(): int
    {
        return $this->tideSort;
    }

    /**
     * @param int $tideSort
     */
    public function setTideSort(int $tideSort): void
    {
        $this->tideSort = $tideSort;
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
            'mapTideCode' => $this->mapTideCode,
            'mapCode' => $this->mapCode,
            'tideCode' => $this->tideCode,
            'tideSort'=> $this->tideSort,
            'createDate' => $this->createDate,
        ];
    }
}