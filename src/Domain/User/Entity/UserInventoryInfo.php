<?php

namespace App\Domain\User\Entity;

use JsonSerializable;

class UserInventoryInfo implements JsonSerializable
{
    private int $inventoryCode=0;
    private int $userCode;
    private int $itemCode;
    private int $itemType; //낚시대: 1, 낚시줄: 2, 바늘: 3, 미끼: 4, 릴: 5, 업그레이트 부품: 6, 버프/회복아이템: 7, 물고기: 8
    private int $upgradeCode=0;
    private int $upgradeLevel=0;
    private int $itemCount;
    private int $itemDurability;
    private string $createDate;

    /**
     * @return int
     */
    public function getInventoryCode(): int
    {
        return $this->inventoryCode;
    }

    /**
     * @param int $inventoryCode
     */
    public function setInventoryCode(int $inventoryCode): void
    {
        $this->inventoryCode = $inventoryCode;
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
    public function getUpgradeCode(): int
    {
        return $this->upgradeCode;
    }

    /**
     * @param int $upgradeCode
     */
    public function setUpgradeCode(int $upgradeCode): void
    {
        $this->upgradeCode = $upgradeCode;
    }

    /**
     * @return int
     */
    public function getUpgradeLevel(): int
    {
        return $this->upgradeLevel;
    }

    /**
     * @param int $upgradeLevel
     */
    public function setUpgradeLevel(int $upgradeLevel): void
    {
        $this->upgradeLevel = $upgradeLevel;
    }

    /**
     * @return int
     */
    public function getItemCount(): int
    {
        return $this->itemCount;
    }

    /**
     * @param int $itemCount
     */
    public function setItemCount(int $itemCount): void
    {
        $this->itemCount = $itemCount;
    }

    /**
     * @return int
     */
    public function getItemDurability(): int
    {
        return $this->itemDurability;
    }

    /**
     * @param int $itemDurability
     */
    public function setItemDurability(int $itemDurability): void
    {
        $this->itemDurability = $itemDurability;
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
            'inventoryCode' => $this->inventoryCode,
            'userCode' => $this->userCode,
            'itemCode' => $this->itemCode,
            'itemType' => $this->itemType,
            'upgradeCode' => $this->upgradeCode,
            'upgradeLevel' => $this->upgradeLevel,
            'itemCount' => $this->itemCount,
            'itemDurability' => $this->itemDurability,
            'createDate' => $this->createDate,
        ];
    }
}