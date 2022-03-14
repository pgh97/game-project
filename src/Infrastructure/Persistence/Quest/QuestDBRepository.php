<?php

namespace App\Infrastructure\Persistence\Quest;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Quest\Entity\QuestInfoData;
use App\Domain\Quest\Repository\QuestRepository;
use App\Infrastructure\Persistence\BaseRepository;

class QuestDBRepository extends BaseRepository implements QuestRepository
{

    public function getQuestInfo(QuestInfoData $questInfoData): QuestInfoData
    {
        $query = '
           SELECT 
                quest_code               AS questCode
                ,quest_type              AS questType
                ,quest_goal              AS questGoal
                ,create_date             AS createDate
            FROM `quest_info_data`
            WHERE quest_code = :questCode
        ';

        $statement = $this->database->prepare($query);
        $questCode = $questInfoData->getQuestCode();

        $statement->bindParam(':questCode', $questCode);
        $statement->execute();
        return $statement->fetchObject(QuestInfoData::class);
    }

    public function getQuestInfoList(SearchInfo $searchInfo): array
    {
        $query = '
            SELECT 
                quest_code               AS questCode
                ,quest_type              AS questType
                ,quest_goal              AS questGoal
                ,create_date             AS createDate
            FROM `quest_info_data`
            ORDER BY quest_code
            LIMIT :offset , :limit
        ';

        $statement = $this->database->prepare($query);
        $offset = $searchInfo->getOffset();
        $limit = $searchInfo->getLimit();

        $statement->bindParam(':offset', $offset);
        $statement->bindParam(':limit', $limit);
        $statement->execute();
        return (array) $statement->fetchAll();
    }

    public function getQuestInfoListCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `quest_info_data`
        ';

        $statement = $this->database->prepare($query);
        $statement->execute();

        return $statement->fetchColumn();
    }
}