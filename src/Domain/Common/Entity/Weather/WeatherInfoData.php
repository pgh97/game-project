<?php

namespace App\Domain\Common\Entity\Weather;

use JsonSerializable;

class WeatherInfoData implements JsonSerializable
{
    private int $weatherCode;
    private int $temperatureCode;
    private int $windCode;
    private string $createDate;

    // wind_info_data
    private int $minWind;
    private int $maxWind;
    private int $windChangeTime;

    //temperature_info_data
    private int $minTemperature;
    private int $maxTemperature;
    private int $temperatureChangeTime;
    private int $changeValue;

    /**
     * @return int
     */
    public function getWeatherCode(): int
    {
        return $this->weatherCode;
    }

    /**
     * @param int $weatherCode
     */
    public function setWeatherCode(int $weatherCode): void
    {
        $this->weatherCode = $weatherCode;
    }

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
    public function getWindCode(): int
    {
        return $this->windCode;
    }

    /**
     * @param int $windCode
     */
    public function setWindCode(int $windCode): void
    {
        $this->windCode = $windCode;
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

    /**
     * @return int
     */
    public function getMinWind(): int
    {
        return $this->minWind;
    }

    /**
     * @param int $minWind
     */
    public function setMinWind(int $minWind): void
    {
        $this->minWind = $minWind;
    }

    /**
     * @return int
     */
    public function getMaxWind(): int
    {
        return $this->maxWind;
    }

    /**
     * @param int $maxWind
     */
    public function setMaxWind(int $maxWind): void
    {
        $this->maxWind = $maxWind;
    }

    /**
     * @return int
     */
    public function getWindChangeTime(): int
    {
        return $this->windChangeTime;
    }

    /**
     * @param int $windChangeTime
     */
    public function setWindChangeTime(int $windChangeTime): void
    {
        $this->windChangeTime = $windChangeTime;
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
    public function getTemperatureChangeTime(): int
    {
        return $this->temperatureChangeTime;
    }

    /**
     * @param int $temperatureChangeTime
     */
    public function setTemperatureChangeTime(int $temperatureChangeTime): void
    {
        $this->temperatureChangeTime = $temperatureChangeTime;
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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'weatherCode' => $this->weatherCode,
            'temperature' => $this->temperatureCode,
            'windCode' => $this->windCode,
            'minWind' => $this->minWind,
            'maxWind' => $this->maxWind,
            'windChangeTime' => $this->windChangeTime,
            'minTemperature' => $this->minTemperature,
            'maxTemperature' => $this->maxTemperature,
            'temperatureChangeTime' => $this->temperatureChangeTime,
            'changeValue' => $this->changeValue,
            'createDate' => $this->createDate,
        ];
    }
}