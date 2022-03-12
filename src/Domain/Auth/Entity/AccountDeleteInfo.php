<?php
declare(strict_types=1);

namespace App\Domain\Auth\Entity;

use JsonSerializable;

class AccountDeleteInfo implements JsonSerializable
{
    private int $deleteCode;
    private int $accountCode;
    private int $accountType;
    private int $deleteType; // 다시 가입: 1 , 더 이상 사용안함: 2, 기타: 99
    private ?int $hiveCode;
    private string $accountId;
    private int $countryCode;
    private int $languageCode;
    private string $deleteDate;
    private ?bool $isSuccess = true;

    /**
     * @return int
     */
    public function getDeleteCode(): int
    {
        return $this->deleteCode;
    }

    /**
     * @param int $deleteCode
     */
    public function setDeleteCode(int $deleteCode): void
    {
        $this->deleteCode = $deleteCode;
    }

    /**
     * @return int
     */
    public function getDeleteType(): int
    {
        return $this->deleteType;
    }

    /**
     * @param int $deleteType
     */
    public function setDeleteType(int $deleteType): void
    {
        $this->deleteType = $deleteType;
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
    public function getDeleteDate(): string
    {
        return $this->deleteDate;
    }

    /**
     * @param string $deleteDate
     */
    public function setDeleteDate(string $deleteDate): void
    {
        $this->deleteDate = $deleteDate;
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
            'deleteCode' => $this->deleteCode,
            'deleteType' => $this->deleteType,
            'accountCode' => $this->accountCode,
            'accountId' => $this->accountId,
            'accountType' => $this->accountType,
            'deleteDate' => $this->deleteDate,
        ];
    }

    public function toJson(): object
    {
        return json_decode((string) json_encode(get_object_vars($this)), false);
    }
}