<?php

namespace App\Domain\User\Entity;

use JsonSerializable;

class UserWeatherHistory implements JsonSerializable
{
    private int $weatherHistoryCode;
    private int $userCode;
    private int $weatherCode;
    private int $temperature;
    private int $wind;
    private string $createDate;

    /**
     * @return int
     */
    public function getWeatherHistoryCode(): int
    {
        return $this->weatherHistoryCode;
    }

    /**
     * @param int $weatherHistoryCode
     */
    public function setWeatherHistoryCode(int $weatherHistoryCode): void
    {
        $this->weatherHistoryCode = $weatherHistoryCode;
    }

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
    public function getTemperature(): int
    {
        return $this->temperature;
    }

    /**
     * @param int $temperature
     */
    public function setTemperature(int $temperature): void
    {
        $this->temperature = $temperature;
    }

    /**
     * @return int
     */
    public function getWind(): int
    {
        return $this->wind;
    }

    /**
     * @param int $wind
     */
    public function setWind(int $wind): void
    {
        $this->wind = $wind;
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
            'weatherHistoryCode' => $this->weatherHistoryCode,
            'userCode' => $this->userCode,
            'weatherCode' => $this->weatherCode,
            'temperature' => $this->temperature,
            'wind' => $this->wind,
            'createDate' => $this->createDate,
        ];
    }
}