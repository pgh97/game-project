<?php

namespace App\Domain\Quest\Entity;

use JsonSerializable;

class CompensationInfoData implements JsonSerializable
{
    private int $compensationCode;
    private int $itemCode;
    private int $itemType;
    private int $compensationValue;
    private string $createDate;

    /**
     * @return int
     */
    public function getCompensationCode(): int
    {
        return $this->compensationCode;
    }

    /**
     * @param int $compensationCode
     */
    public function setCompensationCode(int $compensationCode): void
    {
        $this->compensationCode = $compensationCode;
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
    public function getCompensationValue(): int
    {
        return $this->compensationValue;
    }

    /**
     * @param int $compensationValue
     */
    public function setCompensationValue(int $compensationValue): void
    {
        $this->compensationValue = $compensationValue;
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
            'compensationCode' => $this->compensationCode,
            'itemCode' => $this->itemCode,
            'itemType' => $this->itemType,
            'compensationValue' => $this->compensationValue,
            'createDate' => $this->createDate,
        ];
    }
}