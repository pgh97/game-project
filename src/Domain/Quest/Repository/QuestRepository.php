<?php

namespace App\Domain\Quest\Repository;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Quest\Entity\QuestInfoData;

interface QuestRepository
{
    /**
     * @param QuestInfoData $questInfoData
     * @return QuestInfoData
     */
    public function getQuestInfo(QuestInfoData $questInfoData): QuestInfoData;

    /**
     * @param SearchInfo $searchInfo
     * @return array
     */
    public function getQuestInfoList(SearchInfo $searchInfo): array;

    /**
     * @param SearchInfo $searchInfo
     * @return int
     */
    public function getQuestInfoListCnt(SearchInfo $searchInfo): int;
}