<?php

namespace App\Domain\Common\Entity\Weather;

use JsonSerializable;

class windInfoData implements JsonSerializable
{
    private int $windCode;
    private int $minWind;
    private int $maxWind;
    private int $changeTime;
    private string $createDate;

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
            'windCode' => $this->windCode,
            'minWind' => $this->minWind,
            'maxWind' => $this->maxWind,
            'changeTime' => $this->changeTime,
            'createDate' => $this->createDate,
        ];
    }
}