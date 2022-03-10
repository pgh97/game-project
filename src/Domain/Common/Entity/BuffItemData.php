<?php

namespace App\Domain\Common\Entity;

use JsonSerializable;

class BuffItemData implements JsonSerializable
{
    private int $buffCode;
    private string $buffName;
    private int $addBuff;
    private string $createDate;

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'buffCode' => $this->buffCode,
            'buffName' => $this->buffName,
            'addBuff' => $this->addBuff,
            'createDate' => $this->createDate,
        ];
    }
}