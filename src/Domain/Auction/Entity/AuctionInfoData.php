<?php

namespace App\Domain\Auction\Entity;

use JsonSerializable;

class AuctionInfoData implements JsonSerializable
{
    private int $auctionCode;
    private int $userCode;
    private int $fishGradeCode;
    private int $moneyCode;
    private int $auctionPrice;
    private int $changeTime;
    private string $createDt;

    /**
     * @return int
     */
    public function getAuctionCode(): int
    {
        return $this->auctionCode;
    }

    /**
     * @param int $auctionCode
     */
    public function setAuctionCode(int $auctionCode): void
    {
        $this->auctionCode = $auctionCode;
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
    public function getFishGradeCode(): int
    {
        return $this->fishGradeCode;
    }

    /**
     * @param int $fishGradeCode
     */
    public function setFishGradeCode(int $fishGradeCode): void
    {
        $this->fishGradeCode = $fishGradeCode;
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
    public function getAuctionPrice(): int
    {
        return $this->auctionPrice;
    }

    /**
     * @param int $auctionPrice
     */
    public function setAuctionPrice(int $auctionPrice): void
    {
        $this->auctionPrice = $auctionPrice;
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
    public function getCreateDt(): string
    {
        return $this->createDt;
    }

    /**
     * @param string $createDt
     */
    public function setCreateDt(string $createDt): void
    {
        $this->createDt = $createDt;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'auctionCode' => $this->auctionCode,
            'userCode' => $this->userCode,
            'fishGradeCode' => $this->fishGradeCode,
            'moneyCode' => $this->moneyCode,
            'auctionPrice' => $this->auctionPrice,
            'changeTime' => $this->changeTime,
            'createDate' => $this->createDt,
        ];
    }
}