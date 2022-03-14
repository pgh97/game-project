<?php

namespace App\Domain\Common\Entity;

use JsonSerializable;

class SearchInfo implements JsonSerializable
{
    private int $accountCode;
    private int $userCode;
    private int $sort;
    private int $limit=10;
    private int $offset=0;

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
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'accountCode' => $this->accountCode,
            'userCode' => $this->userCode,
            'limit' => $this->limit,
            'offset' => $this->offset,
            'sort' => $this->sort,
        ];
    }

    public function toJson(): object
    {
        return json_decode((string) json_encode(get_object_vars($this)), false);
    }
}