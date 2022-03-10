<?php

namespace App\Domain\Common\Entity\Weather;

use JsonSerializable;

class WeatherInfoData implements JsonSerializable
{
    private int $weatherCode;
    private int $temperatureCode;
    private int $windCode;
    private string $createDate;

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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'weatherCode' => $this->weatherCode,
            'temperature' => $this->temperatureCode,
            'windCode' => $this->windCode,
            'createDate' => $this->createDate,
        ];
    }
}