<?php

namespace App\Domain\Common\Entity;

use JsonSerializable;

class FunctionInfoData implements JsonSerializable
{
    private int $functionCode;
    private int $functionName;
    private int $functionProbability;
    private string $createDate;

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
     * @return int
     */
    public function getFunctionName(): int
    {
        return $this->functionName;
    }

    /**
     * @param int $functionName
     */
    public function setFunctionName(int $functionName): void
    {
        $this->functionName = $functionName;
    }

    /**
     * @return int
     */
    public function getFunctionProbability(): int
    {
        return $this->functionProbability;
    }

    /**
     * @param int $functionProbability
     */
    public function setFunctionProbability(int $functionProbability): void
    {
        $this->functionProbability = $functionProbability;
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
            'functionCode' => $this->functionCode,
            'functionName' => $this->functionName,
            'functionProbability' => $this->functionProbability,
            'createDate' => $this->createDate,
        ];
    }
}