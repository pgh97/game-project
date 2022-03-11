<?php

namespace App\Domain\Common\Repository;


use App\Domain\Common\Entity\Ship\ShipInfoData;
use App\Domain\Common\Entity\Weather\WeatherInfoData;

interface CommonRepository
{
    /**
     * @param WeatherInfoData $weatherInfoData
     * @return WeatherInfoData
     */
    public function getWeatherInfo(WeatherInfoData $weatherInfoData): WeatherInfoData;

    /**
     * @param ShipInfoData $shipInfoData
     * @return ShipInfoData
     */
    public function getShipInfo(ShipInfoData $shipInfoData): ShipInfoData;
}