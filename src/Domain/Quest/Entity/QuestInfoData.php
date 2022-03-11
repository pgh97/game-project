<?php

namespace App\Domain\Quest\Entity;

use JetBrains\PhpStorm\Internal\TentativeType;
use JsonSerializable;

class QuestInfoData implements JsonSerializable
{
    private int $questCode;
    private int $questType; //레벨업: 1, 도감: 2
    private int $questGoal;
    private string $createDate;

    /**
     * @return int
     */
    public function getQuestCode(): int
    {
        return $this->questCode;
    }

    /**
     * @param int $questCode
     */
    public function setQuestCode(int $questCode): void
    {
        $this->questCode = $questCode;
    }

    /**
     * @return int
     */
    public function getQuestType(): int
    {
        return $this->questType;
    }

    /**
     * @param int $questType
     */
    public function setQuestType(int $questType): void
    {
        $this->questType = $questType;
    }

    /**
     * @return int
     */
    public function getQuestGoal(): int
    {
        return $this->questGoal;
    }

    /**
     * @param int $questGoal
     */
    public function setQuestGoal(int $questGoal): void
    {
        $this->questGoal = $questGoal;
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
            'questCode' => $this->questCode,
            'questType' => $this->questType,
            'questGoal' => $this->questGoal,
            'createDate' => $this->createDate,
        ];
    }
}