<?php

namespace App\Domain\Shop\Entity;

use JetBrains\PhpStorm\Internal\TentativeType;
use JsonSerializable;

class ShopInfoData implements JsonSerializable
{
    private int $shopCode;
    private int $itemCode;
    private int $itemType;
    private int $salePercent;
    private int $moneyCode;
    private int $itemPrice;
    private string $createDate;

    /**
     * @return int
     */
    public function getShopCode(): int
    {
        return $this->shopCode;
    }

    /**
     * @param int $shopCode
     */
    public function setShopCode(int $shopCode): void
    {
        $this->shopCode = $shopCode;
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
    public function getSalePercent(): int
    {
        return $this->salePercent;
    }

    /**
     * @param int $salePercent
     */
    public function setSalePercent(int $salePercent): void
    {
        $this->salePercent = $salePercent;
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
    public function getItemPrice(): int
    {
        return $this->itemPrice;
    }

    /**
     * @param int $itemPrice
     */
    public function setItemPrice(int $itemPrice): void
    {
        $this->itemPrice = $itemPrice;
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
            'shopCode' => $this->shopCode,
            'itemCode' => $this->itemCode,
            'itemType' => $this->itemType,
            'salePercent' => $this->salePercent,
            'moneyCode' => $this->moneyCode,
            'itemPrice' => $this->itemPrice,
            'createDate' => $this->createDate,
        ];
    }
}