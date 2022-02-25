<?php
declare(strict_types=1);

namespace App\Domain\Auth\Entity;

use JsonSerializable;

class AccountInfo implements JsonSerializable
{
    private int $accountCode;
    private int $accountType;
    private ?int $hiveCode;
    private string $accountId;
    private ?string $accountPw = null;
    private int $countryCode;
    private int $languageCode;
    private string $lastLoginDate;
    private string $createDate;
    private ?bool $isSuccess = true;

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
     * @return int
     */
    public function getAccountType(): int
    {
        return $this->accountType;
    }

    /**
     * @param int $accountType
     */
    public function setAccountType(int $accountType): void
    {
        $this->accountType = $accountType;
    }

    /**
     * @return int|null
     */
    public function getHiveCode(): ?int
    {
        return $this->hiveCode;
    }

    /**
     * @param int|null $hiveCode
     */
    public function setHiveCode(?int $hiveCode): void
    {
        $this->hiveCode = $hiveCode;
    }

    /**
     * @return string
     */
    public function getAccountId(): string
    {
        return $this->accountId;
    }

    /**
     * @param string $accountId
     */
    public function setAccountId(string $accountId): void
    {
        $this->accountId = $accountId;
    }

    /**
     * @return string
     */
    public function getAccountPw(): string
    {
        return $this->accountPw;
    }

    /**
     * @param string $accountPw
     */
    public function setAccountPw(string $accountPw): void
    {
        $this->accountPw = $accountPw;
    }

    /**
     * @return int
     */
    public function getCountryCode(): int
    {
        return $this->countryCode;
    }

    /**
     * @param int $countryCode
     */
    public function setCountryCode(int $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return int
     */
    public function getLanguageCode(): int
    {
        return $this->languageCode;
    }

    /**
     * @param int $languageCode
     */
    public function setLanguageCode(int $languageCode): void
    {
        $this->languageCode = $languageCode;
    }

    /**
     * @return string
     */
    public function getLastLoginDate(): string
    {
        return $this->lastLoginDate;
    }

    /**
     * @param string $lastLoginDate
     */
    public function setLastLoginDate(string $lastLoginDate): void
    {
        $this->lastLoginDate = $lastLoginDate;
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

    /**
     * @return bool|null
     */
    public function getIsSuccess(): ?bool
    {
        return $this->isSuccess;
    }

    /**
     * @param bool|null $isSuccess
     */
    public function setIsSuccess(?bool $isSuccess): void
    {
        $this->isSuccess = $isSuccess;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'accountCode' => $this->accountCode,
            'accountId' => $this->accountId,
            'accountPw' => $this->accountPw,
            'accountType' => $this->accountType,
            'createDate' => $this->createDate,
            'lastLoginDate' => $this->lastLoginDate,
        ];
    }

    public function toJson(): object
    {
        return json_decode((string) json_encode(get_object_vars($this)), false);
    }
}