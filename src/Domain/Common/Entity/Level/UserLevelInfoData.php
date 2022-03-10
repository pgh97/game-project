<?php

namespace App\Domain\Common\Entity\Level;

use JsonSerializable;

class UserLevelInfoData implements JsonSerializable
{
    private int $levelCode;
    private int $levelExperience;
    private int $maxFatigue;
    private int $auctionProfit;
    private int $inventoryCount;
    private string $createDt;

    /**
     * @return int
     */
    public function getLevelCode(): int
    {
        return $this->levelCode;
    }

    /**
     * @param int $levelCode
     */
    public function setLevelCode(int $levelCode): void
    {
        $this->levelCode = $levelCode;
    }

    /**
     * @return int
     */
    public function getLevelExperience(): int
    {
        return $this->levelExperience;
    }

    /**
     * @param int $levelExperience
     */
    public function setLevelExperience(int $levelExperience): void
    {
        $this->levelExperience = $levelExperience;
    }

    /**
     * @return int
     */
    public function getMaxFatigue(): int
    {
        return $this->maxFatigue;
    }

    /**
     * @param int $maxFatigue
     */
    public function setMaxFatigue(int $maxFatigue): void
    {
        $this->maxFatigue = $maxFatigue;
    }

    /**
     * @return int
     */
    public function getAuctionProfit(): int
    {
        return $this->auctionProfit;
    }

    /**
     * @param int $auctionProfit
     */
    public function setAuctionProfit(int $auctionProfit): void
    {
        $this->auctionProfit = $auctionProfit;
    }

    /**
     * @return int
     */
    public function getInventoryCount(): int
    {
        return $this->inventoryCount;
    }

    /**
     * @param int $inventoryCount
     */
    public function setInventoryCount(int $inventoryCount): void
    {
        $this->inventoryCount = $inventoryCount;
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
        // TODO: Implement jsonSerialize() method.
    }

    public function toJson(): object
    {
        return json_decode((string) json_encode(get_object_vars($this)), false);
    }
}