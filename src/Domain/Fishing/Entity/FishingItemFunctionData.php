<?php

namespace App\Domain\Fishing\Entity;

use JsonSerializable;

class FishingItemFunctionData implements JsonSerializable
{
    private int $itemGradeCode;
    private int $itemType;
    private int $functionCode;
    private string $createDate;

    /**
     * @return int
     */
    public function getItemGradeCode(): int
    {
        return $this->itemGradeCode;
    }

    /**
     * @param int $itemGradeCode
     */
    public function setItemGradeCode(int $itemGradeCode): void
    {
        $this->itemGradeCode = $itemGradeCode;
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
    public function getFunctionCode(): int
    {
        return $this->functionCode;
    }

    /**
     * @param int $functionCode
     */
    public function setFunctionCode(int $functionCode): void
    {
        $this->functionCode = $functionCode;
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
            'itemGradeCode' => $this->itemGradeCode,
            'itemType' => $this->itemType,
            'functionCode' => $this->functionCode,
            'createDate' => $this->createDate,
        ];
    }
}