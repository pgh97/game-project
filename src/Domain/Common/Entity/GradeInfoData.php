<?php

namespace App\Domain\Common\Entity;

use JsonSerializable;

class GradeInfoData implements JsonSerializable
{
    private int $gradeCode;
    private string $gradeName;
    private string $createDate;

    /**
     * @return int
     */
    public function getGradeCode(): int
    {
        return $this->gradeCode;
    }

    /**
     * @param int $gradeCode
     */
    public function setGradeCode(int $gradeCode): void
    {
        $this->gradeCode = $gradeCode;
    }

    /**
     * @return string
     */
    public function getGradeName(): string
    {
        return $this->gradeName;
    }

    /**
     * @param string $gradeName
     */
    public function setGradeName(string $gradeName): void
    {
        $this->gradeName = $gradeName;
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
            'gradeCode' => $this->gradeCode,
            'gradeName' => $this->gradeName,
            'createDate' => $this->createDate,
        ];
    }
}