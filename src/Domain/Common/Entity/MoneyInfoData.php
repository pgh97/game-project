<?php

namespace App\Domain\Common\Entity;

use JsonSerializable;

class MoneyInfoData implements JsonSerializable
{
    private int $moneyCode;
    private string $moneyName;
    private string $createDate;

    /**
     * @return int
     */
    public function getMoneyCode(): int
    {
        return $this->moneyCode;
    }

    /**
     * @param int $moneyCode
     */
    public function setMoneyCode(int $moneyCode): void
    {
        $this->moneyCode = $moneyCode;
    }

    /**
     * @return string
     */
    public function getMoneyName(): string
    {
        return $this->moneyName;
    }

    /**
     * @param string $moneyName
     */
    public function setMoneyName(string $moneyName): void
    {
        $this->moneyName = $moneyName;
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
            'moneyCode' => $this->moneyCode,
            'moneyName' => $this->moneyName,
            'createDate' => $this->createDate,
        ];
    }
}