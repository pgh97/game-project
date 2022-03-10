<?php

namespace App\Domain\Common\Entity\Weather;

use JsonSerializable;

class temperatureInfoData implements JsonSerializable
{
    private int $temperatureCode;
    private int $minTemperatureCode;
    private int $maxTemperatureCode;
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
    public function getMinTemperatureCode(): int
    {
        return $this->minTemperatureCode;
    }

    /**
     * @param int $minTemperatureCode
     */
    public function setMinTemperatureCode(int $minTemperatureCode): void
    {
        $this->minTemperatureCode = $minTemperatureCode;
    }

    /**
     * @return int
     */
    public function getMaxTemperatureCode(): int
    {
        return $this->maxTemperatureCode;
    }

    /**
     * @param int $maxTemperatureCode
     */
    public function setMaxTemperatureCode(int $maxTemperatureCode): void
    {
        $this->maxTemperatureCode = $maxTemperatureCode;
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
            'minTemperatureCode' => $this->minTemperatureCode,
            'maxTemperatureCode' => $this->maxTemperatureCode,
            'changeTime' => $this->changeTime,
            'changeValue' => $this->changeValue,
            'createDate' => $this->createDate,
        ];
    }
}