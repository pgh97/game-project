<?php

namespace App\Domain\Fishing\Entity;

use JsonSerializable;

class FishingInfoData implements JsonSerializable
{
    private int $itemCode;
    private string $itemName;
    private int $weight;
    private string $createDate;

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
     * @return string
     */
    public function getItemName(): string
    {
        return $this->itemName;
    }

    /**
     * @param string $itemName
     */
    public function setItemName(string $itemName): void
    {
        $this->itemName = $itemName;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
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
            'itemCode' => $this->itemCode,
            'itemName' => $this->itemName,
            'weight' => $this->weight,
            'createDate' => $this->createDate,
        ];
    }
}