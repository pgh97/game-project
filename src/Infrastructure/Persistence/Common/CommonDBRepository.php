<?php

namespace App\Infrastructure\Persistence\Common;

use App\Domain\Common\Entity\Ship\ShipInfoData;
use App\Domain\Common\Entity\Weather\WeatherInfoData;
use App\Domain\Common\Repository\CommonRepository;
use App\Infrastructure\Persistence\BaseRepository;

class CommonDBRepository extends BaseRepository implements CommonRepository
{

    public function getWeatherInfo(WeatherInfoData $weatherInfoData): WeatherInfoData
    {
        $query = '
            SELECT 
                A.weather_code      AS weatherCode
                ,A.temperature_code AS temperatureCode
                ,A.wind_code        AS windCode
                ,B.min_wind         AS minWind      
                ,B.max_wind         AS maxWind
                ,B.change_time      AS windChangeTime
                ,C.min_temperature  AS minTemperature
                ,C.max_temperature  AS maxTemperature
                ,C.change_time      AS temperatureChangeTime 
                ,C.change_value     AS changeValue
                ,A.create_date      AS createDate
            FROM `weather_info_data` A
            JOIN `wind_info_data` B on A.wind_code = B.wind_code
            JOIN `temperature_info_data` C on A.temperature_code = C.temperature_code
            WHERE A.weather_code = :weatherCode
        ';

        $statement = $this->database->prepare($query);
        $weatherCode = $weatherInfoData->getWeatherCode();

        $statement->bindParam(':weatherCode', $weatherCode);
        $statement->execute();

        return $statement->fetchObject(WeatherInfoData::class);
    }

    public function getShipInfo(ShipInfoData $shipInfoData): ShipInfoData
    {
        $query = '
            SELECT 
                ship_code      AS shipCode
                ,ship_name     AS shipName
                ,durability    AS durability
                ,fuel          AS fuel      
                ,max_upgrade   AS maxUpgrade
                ,create_date   AS createDate
            FROM `ship_info_data` 
            WHERE ship_code = :shipCode
        ';

        $statement = $this->database->prepare($query);
        $shipCode = $shipInfoData->getShipCode();

        $statement->bindParam(':shipCode', $shipCode);
        $statement->execute();

        return $statement->fetchObject(ShipInfoData::class);
    }
}