<?php

namespace App\Domain\Map\Entity;

use JsonSerializable;

class TideInfoData implements JsonSerializable
{
    private int $tideCode;
    private string $highTideTime1;
    private string $lowTideTime1;
    private string $highTideTime2;
    private string $lowTideTime2;
    private int $waterSplashTime;
    private int $appearProbability;
    private string $createDate;

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
     * @return string
     */
    public function getHighTideTime1(): string
    {
        return $this->highTideTime1;
    }

    /**
     * @param string $highTideTime1
     */
    public function setHighTideTime1(string $highTideTime1): void
    {
        $this->highTideTime1 = $highTideTime1;
    }

    /**
     * @return string
     */
    public function getLowTideTime1(): string
    {
        return $this->lowTideTime1;
    }

    /**
     * @param string $lowTideTime1
     */
    public function setLowTideTime1(string $lowTideTime1): void
    {
        $this->lowTideTime1 = $lowTideTime1;
    }

    /**
     * @return string
     */
    public function getHighTideTime2(): string
    {
        return $this->highTideTime2;
    }

    /**
     * @param string $highTideTime2
     */
    public function setHighTideTime2(string $highTideTime2): void
    {
        $this->highTideTime2 = $highTideTime2;
    }

    /**
     * @return string
     */
    public function getLowTideTime2(): string
    {
        return $this->lowTideTime2;
    }

    /**
     * @param string $lowTideTime2
     */
    public function setLowTideTime2(string $lowTideTime2): void
    {
        $this->lowTideTime2 = $lowTideTime2;
    }

    /**
     * @return int
     */
    public function getWaterSplashTime(): int
    {
        return $this->waterSplashTime;
    }

    /**
     * @param int $waterSplashTime
     */
    public function setWaterSplashTime(int $waterSplashTime): void
    {
        $this->waterSplashTime = $waterSplashTime;
    }

    /**
     * @return int
     */
    public function getAppearProbability(): int
    {
        return $this->appearProbability;
    }

    /**
     * @param int $appearProbability
     */
    public function setAppearProbability(int $appearProbability): void
    {
        $this->appearProbability = $appearProbability;
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
            'tideCode' => $this->tideCode,
            'highTideTime1' => $this->highTideTime1,
            'lowTideTime1' => $this->lowTideTime1,
            'highTideTime2' => $this->highTideTime2,
            'lowTideTime2' => $this->lowTideTime2,
            'waterSplashTime' => $this->waterSplashTime,
            'appearProbability' => $this->appearProbability,
            'createDate' => $this->createDate,
        ];
    }
}