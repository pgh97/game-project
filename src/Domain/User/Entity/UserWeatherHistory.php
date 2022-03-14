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
    private int $mapCode=0;
    private string $createDate;
    private string $updateDate='';

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
    public function getUpdateDate(): string
    {
        return $this->updateDate;
    }

    /**
     * @param string $updateDate
     */
    public function setUpdateDate(string $updateDate): void
    {
        $this->updateDate = $updateDate;
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
            'mapCode' => $this->mapCode,
            'createDate' => $this->createDate,
            'updateDate' => $this->updateDate,
        ];
    }
}