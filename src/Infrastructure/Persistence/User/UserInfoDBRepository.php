<?php

namespace App\Infrastructure\Persistence\User;

use App\Domain\Common\Entity\UserLevelInfoData;
use App\Domain\Common\SearchInfo;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Repository\UserInfoRepository;
use App\Infrastructure\Persistence\BaseRepository;

class UserInfoDBRepository extends BaseRepository implements UserInfoRepository
{

    public function createUserInfo(UserInfo $userInfo): int
    {
        $query = '
            INSERT INTO `user_info`
                (`account_code`, `user_nicknm`, `level_code`, `user_experience`
                , `money_gold`,`money_pearl`, `fatigue`, `use_inventory_count`, `use_save_item_count`, `create_date`)
            VALUES
                (:accountCode, :userNickNm, :levelCode, :userExperience
                , :moneyGold, :moneyPearl, :fatigue, :useInventoryCount, :useSaveItemCount, NOW())
            ON DUPLICATE KEY UPDATE 
                user_nicknm=Values(user_nicknm)
                ,level_code=Values(level_code)
                ,user_experience=Values(user_experience)
                ,money_gold=Values(money_gold)
                ,money_pearl=Values(money_pearl)
                ,fatigue=Values(fatigue)
                ,use_inventory_count=Values(use_inventory_count)
        ';
        $statement = $this->database->prepare($query);

        $accountCode = $userInfo->getAccountCode();
        $userNickNm = $userInfo->getUserNickNm();
        $levelCode= $userInfo->getLevelCode();
        $userExperience = $userInfo->getUserExperience();
        $moneyGold= $userInfo->getMoneyGold();
        $moneyPearl = $userInfo->getMoneyPearl();
        $fatigue = $userInfo->getFatigue();
        $useInventoryCount = $userInfo->getUseInventoryCount();
        $useSaveItemCount = $userInfo->getUseSaveItemCount();

        $statement->bindParam(':accountCode', $accountCode);
        $statement->bindParam(':userNickNm', $userNickNm);
        $statement->bindParam(':levelCode', $levelCode);
        $statement->bindParam(':userExperience', $userExperience);
        $statement->bindParam(':moneyGold', $moneyGold);
        $statement->bindParam(':moneyPearl', $moneyPearl);
        $statement->bindParam(':fatigue', $fatigue);
        $statement->bindParam(':useInventoryCount', $useInventoryCount);
        $statement->bindParam(':useSaveItemCount', $useSaveItemCount);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getUserInfo(UserInfo $userInfo): UserInfo
    {
        $query = '
            SELECT 
                user_code               AS userCode
                ,account_code           AS accountCode
                ,user_nicknm            AS userNickNm
                ,level_code             AS levelCode
                ,user_experience        AS userExperience
                ,money_gold             AS moneyGold
                ,money_pearl            AS moneyPearl
                ,fatigue                AS fatigue
                ,use_inventory_count    AS useInventoryCount
                ,use_save_item_count    AS useSaveItemCount
                ,create_date            AS createDate
            FROM `user_info`
            WHERE user_code = :userCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $userInfo->getUserCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return $statement->fetchObject(UserInfo::class);
    }

    public function getUserInfoList(SearchInfo $searchInfo): array
    {
        $query = '
            SELECT 
                user_code               AS userCode
                ,account_code           AS accountCode
                ,user_nicknm             AS userNickNm
                ,level_code             AS levelCode
                ,user_experience        AS userExperience
                ,money_gold             AS moneyGold
                ,money_pearl            AS moneyPearl
                ,fatigue                AS fatigue
                ,use_inventory_count    AS useInventoryCount
                ,use_save_item_count    AS useSaveItemCount
                ,create_date            AS createDate
            FROM `user_info`
            WHERE account_code = :accountCode
            LIMIT :offset , :limit
        ';

        $statement = $this->database->prepare($query);
        $accountCode = $searchInfo->getAccountCode();
        $offset = $searchInfo->getOffset();
        $limit = $searchInfo->getLimit();

        $statement->bindParam(':accountCode', $accountCode);
        $statement->bindParam(':offset', $offset);
        $statement->bindParam(':limit', $limit);
        $statement->execute();

        return (array) $statement->fetchAll();
    }

    public function getUserInfoListCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `user_info`
            WHERE account_code = :accountCode
        ';

        $statement = $this->database->prepare($query);
        $accountCode = $searchInfo->getAccountCode();

        $statement->bindParam(':accountCode', $accountCode);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getUserLevelInfo(UserLevelInfoData $levelInfo): UserLevelInfoData
    {
        $query = '
            SELECT 
                level_code              AS levelCode
                ,level_experience       AS levelExperience
                ,max_fatigue            AS maxFatigue
                ,auction_profit         AS auctionProfit
                ,inventory_count        AS inventoryCount
                ,create_date            AS createDate
            FROM `user_level_info_data`
            WHERE level_code = :levelCode
        ';

        $statement = $this->database->prepare($query);
        $levelCode = $levelInfo->getLevelCode();

        $statement->bindParam(':levelCode', $levelCode);
        $statement->execute();

        return $statement->fetchObject(UserLevelInfoData::class);
    }

    public function modifyUserInfo(UserInfo $userInfo): int
    {
        $userCode = $userInfo->getUserCode();
        $levelCode = $userInfo->getLevelCode();
        $userNickNm = $userInfo->getUserNickNm();
        $userExperience = $userInfo->getUserExperience();
        $moneyGold = $userInfo->getMoneyGold();
        $moneyPearl = $userInfo->getMoneyPearl();
        $fatigue = $userInfo->getFatigue();

        $queryFront = 'UPDATE `user_info` SET ';
        $queryBody = '';
        $queryEnd = ',update_date = NOW() WHERE user_code = :userCode';

        if(!empty($userNickNm)){
            $queryBody .= 'user_nicknm = :userNickNm ';
        }
        if(!empty($levelCode)){
            if(empty($queryBody)){
                $queryBody .= 'level_code = :levelCode ';
            }else{
                $queryBody .= ',level_code = :levelCode ';
            }
        }
        if(!empty($userExperience)){
            if(empty($queryBody)){
                $queryBody .= 'user_experience = :userExperience ';
            }else{
                $queryBody .= ',user_experience = :userExperience ';
            }
        }
        if(!empty($moneyGold)){
            if(empty($queryBody)){
                $queryBody .= 'money_gold = :moneyGold ';
            }else{
                $queryBody .= ',money_gold = :moneyGold ';
            }
        }
        if(!empty($moneyPearl)){
            if(empty($queryBody)){
                $queryBody .= 'money_pearl = :moneyPearl ';
            }else{
                $queryBody .= ',money_pearl = :moneyPearl ';
            }
        }
        if(!empty($fatigue)){
            if(empty($queryBody)){
                $queryBody .= 'fatigue = :fatigue ';
            }else{
                $queryBody .= ',fatigue = :fatigue ';
            }
        }

        $query = $queryFront.$queryBody.$queryEnd;
        $statement = $this->database->prepare($query);

        if(!empty($userNickNm)){
            $statement->bindParam(':userNickNm', $userNickNm);
        }
        if(!empty($levelCode)){
            $statement->bindParam(':levelCode', $levelCode);
        }
        if(!empty($userExperience)){
            $statement->bindParam(':userExperience', $userExperience);
        }
        if(!empty($moneyGold)){
            $statement->bindParam(':moneyGold', $moneyGold);
        }
        if(!empty($moneyPearl)){
            $statement->bindParam(':moneyPearl', $moneyPearl);
        }
        if(!empty($fatigue)){
            $statement->bindParam(':fatigue', $fatigue);
        }
        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }
}