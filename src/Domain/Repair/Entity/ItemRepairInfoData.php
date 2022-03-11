<?php

namespace App\Domain\Repair\Entity;

use JsonSerializable;

class ItemRepairInfoData implements JsonSerializable
{
    private int $repairCode;
    private int $itemCode;
    private int $itemType; //낚시대: 1, 낚시줄: 2, 릴: 3, 낚시배: 4
    private int $moneyCode;
    private int $repairPrice;
    private string $createDate;

    /**
     * @return int
     */
    public function getRepairCode(): int
    {
        return $this->repairCode;
    }

    /**
     * @param int $repairCode
     */
    public function setRepairCode(int $repairCode): void
    {
        $this->repairCode = $repairCode;
    }

    /**
     * @return int
     */
    public function getItemCode(): int
    {
        return $this->itemCode;
    }

    /**
     * @param int $itemCode
     */
    public function setItemCode(int $itemCode): void
    {
        $this->itemCode = $itemCode;
    }

    /**
     * @return int
     */
    public function getItemType(): int
    {
        return $this->itemType;
    }

    /**
     * @param int $itemType
     */
    public function setItemType(int $itemType): void
    {
        $this->itemType = $itemType;
    }

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
     * @return int
     */
    public function getRepairPrice(): int
    {
        return $this->repairPrice;
    }

    /**
     * @param int $repairPrice
     */
    public function setRepairPrice(int $repairPrice): void
    {
        $this->repairPrice = $repairPrice;
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
            'repairCode' => $this->repairCode,
            'itemCode' => $this->itemCode,
            'itemType' => $this->itemType,
            'moneyCode' => $this->moneyCode,
            'repairPrice' => $this->repairPrice,
            'createDate' => $this->createDate,
        ];
    }
}