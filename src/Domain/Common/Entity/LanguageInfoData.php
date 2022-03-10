<?php

namespace App\Domain\Common\Entity;

use JsonSerializable;

class LanguageInfoData implements JsonSerializable
{
    private int $languageCode;
    private int $languageName;
    private string $createDate;

    /**
     * @return int
     */
    public function getLanguageCode(): int
    {
        return $this->languageCode;
    }

    /**
     * @param int $languageCode
     */
    public function setLanguageCode(int $languageCode): void
    {
        $this->languageCode = $languageCode;
    }

    /**
     * @return int
     */
    public function getLanguageName(): int
    {
        return $this->languageName;
    }

    /**
     * @param int $languageName
     */
    public function setLanguageName(int $languageName): void
    {
        $this->languageName = $languageName;
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
            'languageCode' => $this->languageCode,
            'languageName' => $this->languageName,
            'createDate' => $this->createDate,
        ];
    }
}