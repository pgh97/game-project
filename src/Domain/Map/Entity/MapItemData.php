<?php

namespace App\Domain\Map\Entity;

use JsonSerializable;

class MapItemData implements JsonSerializable
{
    private int $mapItemCode;
    private int $mapCode;
    private int $itemCode;
    private int $itemType; //낚시대1, 낚시줄2, 바늘3, 미끼4, 릴5, 업데이트 부품6
    private int $itemProbability;
    private string $createDate;

    /**
     * @return int
     */
    public function getMapItemCode(): int
    {
        return $this->mapItemCode;
    }

    /**
     * @param int $mapItemCode
     */
    public function setMapItemCode(int $mapItemCode): void
    {
        $this->mapItemCode = $mapItemCode;
    }

    /**
     * @return int
     */
    public function getMapCode(): int
    {
        return $this->mapCode;
    }

    /**
     * @param int $mapCode
     */
    public function setMapCode(int $mapCode): void
    {
        $this->mapCode = $mapCode;
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
    public function getItemProbability(): int
    {
        return $this->itemProbability;
    }

    /**
     * @param int $itemProbability
     */
    public function setItemProbability(int $itemProbability): void
    {
        $this->itemProbability = $itemProbability;
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
            'mapItemCode' => $this->mapItemCode,
            'mapCode' => $this->mapCode,
            'itemCode' => $this->itemCode,
            'itemType' => $this->itemType,
            'itemProbability' => $this->itemProbability,
            'createDate' => $this->createDate,
        ];
    }
}