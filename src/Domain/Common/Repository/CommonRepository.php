<?php

namespace App\Domain\Common\Repository;


use App\Domain\Common\Entity\fish\FishGradeData;
use App\Domain\Common\Entity\fish\FishInfoData;
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

    /**
     * @param FishInfoData $fishInfoData
     * @return FishInfoData
     */
    public function getFishInfo(FishInfoData $fishInfoData): FishInfoData;

    /**
     * @param FishGradeData $fishGradeData
     * @return FishGradeData
     */
    public function getFishGradeData(FishGradeData $fishGradeData): FishGradeData;
}