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

    public function getQuestInfoGoal(QuestInfoData $questInfoData): QuestInfoData
    {
        $query = '
           SELECT 
                quest_code               AS questCode
                ,quest_type              AS questType
                ,quest_goal              AS questGoal
                ,create_date             AS createDate
            FROM `quest_info_data`
            WHERE quest_type = :questType AND quest_goal = :questGoal
        ';

        $statement = $this->database->prepare($query);
        $questType = $questInfoData->getQuestType();
        $questGoal = $questInfoData->getQuestGoal();

        $statement->bindParam(':questType', $questType);
        $statement->bindParam(':questGoal', $questGoal);
        $statement->execute();
        return $statement->fetchObject(QuestInfoData::class);
    }

    public function getUserQuestInfoList(SearchInfo $searchInfo): array
    {
        $query = '
            SELECT 
                QI.quest_code               AS questCode
                ,QI.quest_type              AS questType
                ,QI.quest_goal              AS questGoal
                ,UQ.create_date             AS createDate
            FROM user_quest_info UQ
            JOIN quest_info_data QI ON UQ.quest_code = QI.quest_code
            WHERE UQ.user_code=:userCode
            ORDER BY UQ.quest_code
            LIMIT :offset , :limit
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();
        $offset = $searchInfo->getOffset();
        $limit = $searchInfo->getLimit();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':offset', $offset);
        $statement->bindParam(':limit', $limit);
        $statement->execute();
        return (array) $statement->fetchAll();
    }

    public function getUserQuestInfoListCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `user_quest_info`
            WHERE user_code = :userCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();
        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getUserQuestInfo(QuestInfoData $questInfoData): QuestInfoData
    {
        $query = '
            SELECT 
                QI.quest_code               AS questCode
                ,QI.quest_type              AS questType
                ,QI.quest_goal              AS questGoal
                ,UQ.create_date             AS createDate
            FROM user_quest_info UQ
            JOIN quest_info_data QI ON UQ.quest_code = QI.quest_code
            WHERE UQ.user_code=:userCode AND UQ.quest_code=:questCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $questInfoData->getUserCode();
        $questCode = $questInfoData->getQuestCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':questCode', $questCode);
        $statement->execute();
        return $statement->fetchObject(QuestInfoData::class);
    }
}