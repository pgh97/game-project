<?php

namespace App\Domain\User\Entity;

use JetBrains\PhpStorm\Internal\TentativeType;
use JsonSerializable;

class UserGitfBoxInfo implements JsonSerializable
{
    private int $boxCode;
    private int $userCode;
    private int $itemCode;
    private int $itemType;
    private int $itemCount;
    private int $readStatus;
    private string $createDate;

    /**
     * @return int
     */
    public function getBoxCode(): int
    {
        return $this->boxCode;
    }

    /**
     * @param int $boxCode
     */
    public function setBoxCode(int $boxCode): void
    {
        $this->boxCode = $boxCode;
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
    public function getReadStatus(): int
    {
        return $this->readStatus;
    }

    /**
     * @param int $readStatus
     */
    public function setReadStatus(int $readStatus): void
    {
        $this->readStatus = $readStatus;
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
            'boxCode' => $this->boxCode,
            'userCode' => $this->userCode,
            'itemCode' => $this->itemCode,
            'itemType' => $this->itemType,
            'itemCount' => $this->itemCount,
            'createDate' => $this->createDate,
        ];
    }
}