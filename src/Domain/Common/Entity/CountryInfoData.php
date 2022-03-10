<?php

namespace App\Domain\Common\Entity;

use JsonSerializable;

class CountryInfoData implements JsonSerializable
{
    private int $countryCode;
    private string $countryName;
    private string $createDate;

    /**
     * @return int
     */
    public function getCountryCode(): int
    {
        return $this->countryCode;
    }

    /**
     * @param int $countryCode
     */
    public function setCountryCode(int $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getCountryName(): string
    {
        return $this->countryName;
    }

    /**
     * @param string $countryName
     */
    public function setCountryName(string $countryName): void
    {
        $this->countryName = $countryName;
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
            'countryCode' => $this->countryCode,
            'countryName' => $this->countryName,
            'createDate' => $this->createDate,
        ];
    }
}