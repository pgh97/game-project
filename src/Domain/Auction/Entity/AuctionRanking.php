<?php

namespace App\Domain\Auction\Entity;

use JsonSerializable;

class AuctionRanking implements JsonSerializable
{
    private string $weekDate;
    private int $userCode;
    private int $moneyCode;
    private int $priceSum;
    private int $auctionRank;
    private string $createDate;

    /**
     * @return string
     */
    public function getWeekDate(): string
    {
        return $this->weekDate;
    }

    /**
     * @param string $weekDate
     */
    public function setWeekDate(string $weekDate): void
    {
        $this->weekDate = $weekDate;
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
    public function getPriceSum(): int
    {
        return $this->priceSum;
    }

    /**
     * @param int $priceSum
     */
    public function setPriceSum(int $priceSum): void
    {
        $this->priceSum = $priceSum;
    }

    /**
     * @return int
     */
    public function getAuctionRank(): int
    {
        return $this->auctionRank;
    }

    /**
     * @param int $auctionRank
     */
    public function setAuctionRank(int $auctionRank): void
    {
        $this->auctionRank = $auctionRank;
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
            'weekDate' => $this->weekDate,
            'userCode' => $this->userCode,
            'moneyCode' => $this->moneyCode,
            'priceSum' => $this->priceSum,
            'auctionRank' => $this->auctionRank,
            'createDate' => $this->createDate,
        ];
    }
}