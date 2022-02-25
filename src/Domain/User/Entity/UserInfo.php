<?php

namespace App\Domain\User\Entity;

use JsonSerializable;

class UserInfo implements JsonSerializable
{
    private int $userCode;
    private int $accountCode;
    private string $userNickNm;
    private int $levelCode;
    private int $userExperience;
    private int $moneyGold;
    private int $moneyPearl;
    private int $fatigue;
    private string $userInventoryCount;
    private string $userSaveItemCount;
    private string $createDate;

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
    public function getAccountCode(): int
    {
        return $this->accountCode;
    }

    /**
     * @param int $accountCode
     */
    public function setAccountCode(int $accountCode): void
    {
        $this->accountCode = $accountCode;
    }

    /**
     * @return string
     */
    public function getUserNickNm(): string
    {
        return $this->userNickNm;
    }

    /**
     * @param string $userNickNm
     */
    public function setUserNickNm(string $userNickNm): void
    {
        $this->userNickNm = $userNickNm;
    }

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
    public function getUserExperience(): int
    {
        return $this->userExperience;
    }

    /**
     * @param int $userExperience
     */
    public function setUserExperience(int $userExperience): void
    {
        $this->userExperience = $userExperience;
    }

    /**
     * @return int
     */
    public function getMoneyGold(): int
    {
        return $this->moneyGold;
    }

    /**
     * @param int $moneyGold
     */
    public function setMoneyGold(int $moneyGold): void
    {
        $this->moneyGold = $moneyGold;
    }

    /**
     * @return int
     */
    public function getMoneyPearl(): int
    {
        return $this->moneyPearl;
    }

    /**
     * @param int $moneyPearl
     */
    public function setMoneyPearl(int $moneyPearl): void
    {
        $this->moneyPearl = $moneyPearl;
    }

    /**
     * @return int
     */
    public function getFatigue(): int
    {
        return $this->fatigue;
    }

    /**
     * @param int $fatigue
     */
    public function setFatigue(int $fatigue): void
    {
        $this->fatigue = $fatigue;
    }

    /**
     * @return string
     */
    public function getUserInventoryCount(): string
    {
        return $this->userInventoryCount;
    }

    /**
     * @param string $userInventoryCount
     */
    public function setUserInventoryCount(string $userInventoryCount): void
    {
        $this->userInventoryCount = $userInventoryCount;
    }

    /**
     * @return string
     */
    public function getUserSaveItemCount(): string
    {
        return $this->userSaveItemCount;
    }

    /**
     * @param string $userSaveItemCount
     */
    public function setUserSaveItemCount(string $userSaveItemCount): void
    {
        $this->userSaveItemCount = $userSaveItemCount;
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
        // TODO: Implement jsonSerialize() method.
    }

    public function toJson(): object
    {
        return json_decode((string) json_encode(get_object_vars($this)), false);
    }
}