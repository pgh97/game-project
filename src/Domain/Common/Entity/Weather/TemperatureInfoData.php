<?php

namespace App\Domain\Common\Entity\Weather;

use JsonSerializable;

class temperatureInfoData implements JsonSerializable
{
    private int $temperatureCode;
    private int $minTemperature;
    private int $maxTemperature;
    private int $changeTime;
    private int $changeValue;
    private string $createDate;

    /**
     * @return int
     */
    public function getTemperatureCode(): int
    {
        return $this->temperatureCode;
    }

    /**
     * @param int $temperatureCode
     */
    public function setTemperatureCode(int $temperatureCode): void
    {
        $this->temperatureCode = $temperatureCode;
    }

    /**
     * @return int
     */
    public function getMinTemperature(): int
    {
        return $this->minTemperature;
    }

    /**
     * @param int $minTemperature
     */
    public function setMinTemperature(int $minTemperature): void
    {
        $this->minTemperature = $minTemperature;
    }

    /**
     * @return int
     */
    public function getMaxTemperature(): int
    {
        return $this->maxTemperature;
    }

    /**
     * @param int $maxTemperature
     */
    public function setMaxTemperature(int $maxTemperature): void
    {
        $this->maxTemperature = $maxTemperature;
    }

    /**
     * @return int
     */
    public function getChangeTime(): int
    {
        return $this->changeTime;
    }

    /**
     * @param int $changeTime
     */
    public function setChangeTime(int $changeTime): void
    {
        $this->changeTime = $changeTime;
    }

    /**
     * @return int
     */
    public function getChangeValue(): int
    {
        return $this->changeValue;
    }

    /**
     * @param int $changeValue
     */
    public function setChangeValue(int $changeValue): void
    {
        $this->changeValue = $changeValue;
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
            'temperatureCode' => $this->temperatureCode,
            'minTemperatureCode' => $this->minTemperature,
            'maxTemperatureCode' => $this->maxTemperature,
            'changeTime' => $this->changeTime,
            'changeValue' => $this->changeValue,
            'createDate' => $this->createDate,
        ];
    }
}