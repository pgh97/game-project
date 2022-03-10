<?php

namespace App\Domain\Common\Entity\fish;

use JsonSerializable;

class FishInfoData implements JsonSerializable
{
    private int $fishCode;
    private string $fishName;
    private int $minDepth;
    private int $maxDepth;
    private int $minSize;
    private int $maxSize;
    private int $fishProbability;
    private int $fishDurability;
    private string $createDate;

    /**
     * @return int
     */
    public function getFishCode(): int
    {
        return $this->fishCode;
    }

    /**
     * @param int $fishCode
     */
    public function setFishCode(int $fishCode): void
    {
        $this->fishCode = $fishCode;
    }

    /**
     * @return string
     */
    public function getFishName(): string
    {
        return $this->fishName;
    }

    /**
     * @param string $fishName
     */
    public function setFishName(string $fishName): void
    {
        $this->fishName = $fishName;
    }

    /**
     * @return int
     */
    public function getMinDepth(): int
    {
        return $this->minDepth;
    }

    /**
     * @param int $minDepth
     */
    public function setMinDepth(int $minDepth): void
    {
        $this->minDepth = $minDepth;
    }

    /**
     * @return int
     */
    public function getMaxDepth(): int
    {
        return $this->maxDepth;
    }

    /**
     * @param int $maxDepth
     */
    public function setMaxDepth(int $maxDepth): void
    {
        $this->maxDepth = $maxDepth;
    }

    /**
     * @return int
     */
    public function getMinSize(): int
    {
        return $this->minSize;
    }

    /**
     * @param int $minSize
     */
    public function setMinSize(int $minSize): void
    {
        $this->minSize = $minSize;
    }

    /**
     * @return int
     */
    public function getMaxSize(): int
    {
        return $this->maxSize;
    }

    /**
     * @param int $maxSize
     */
    public function setMaxSize(int $maxSize): void
    {
        $this->maxSize = $maxSize;
    }

    /**
     * @return int
     */
    public function getFishProbability(): int
    {
        return $this->fishProbability;
    }

    /**
     * @param int $fishProbability
     */
    public function setFishProbability(int $fishProbability): void
    {
        $this->fishProbability = $fishProbability;
    }

    /**
     * @return int
     */
    public function getFishDurability(): int
    {
        return $this->fishDurability;
    }

    /**
     * @param int $fishDurability
     */
    public function setFishDurability(int $fishDurability): void
    {
        $this->fishDurability = $fishDurability;
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
            'fishCode' => $this->fishCode,
            'fishName' => $this->fishName,
            'minDepth' => $this->minDepth,
            'maxDepth' => $this->maxDepth,
            'minSize' => $this->minSize,
            'maxSize' => $this->maxSize,
            'fishProbability' => $this->fishProbability,
            'fishDurability' => $this->fishDurability,
            'createDate' => $this->createDate,
        ];
    }
}