<?php

namespace App\Domain\Upgrade\Entity;

use JsonSerializable;

class UpgradeItemData implements JsonSerializable
{
    private int $upgradeItemCode;
    private string $upgradeItemName;
    private string $createDate;

    /**
     * @return int
     */
    public function getUpgradeItemCode(): int
    {
        return $this->upgradeItemCode;
    }

    /**
     * @param int $upgradeItemCode
     */
    public function setUpgradeItemCode(int $upgradeItemCode): void
    {
        $this->upgradeItemCode = $upgradeItemCode;
    }

    /**
     * @return string
     */
    public function getUpgradeItemName(): string
    {
        return $this->upgradeItemName;
    }

    /**
     * @param string $upgradeItemName
     */
    public function setUpgradeItemName(string $upgradeItemName): void
    {
        $this->upgradeItemName = $upgradeItemName;
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
            'upgradeItemCode' => $this->upgradeItemCode,
            'upgradeItemName' => $this->upgradeItemName,
            'createDate' => $this->createDate,
        ];
    }
}