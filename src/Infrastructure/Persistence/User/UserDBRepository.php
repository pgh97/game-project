<?php

namespace App\Infrastructure\Persistence\User;

use App\Domain\Common\Entity\Level\UserLevelInfoData;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\User\Entity\UserChoiceItemInfo;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserInventoryInfo;
use App\Domain\User\Entity\UserShipInfo;
use App\Domain\User\Entity\UserWeatherHistory;
use App\Domain\User\Repository\UserRepository;
use App\Infrastructure\Persistence\BaseRepository;

class UserDBRepository extends BaseRepository implements UserRepository
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
            ORDER BY user_code
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

    public function createUserWeather(UserWeatherHistory $userWeatherHistory): int
    {
        $query = '
            INSERT INTO `user_weather_history`
                (`user_code`, `weather_code`, `temperature`
                , `wind`,`create_date`)
            VALUES
                (:userCode, :weatherCode, :temperature
                , :wind, NOW())
            ON DUPLICATE KEY UPDATE 
                temperature=Values(temperature)
                ,wind=Values(wind)
                ,create_date=NOW()
        ';
        $statement = $this->database->prepare($query);

        $userCode = $userWeatherHistory->getUserCode();
        $weatherCode = $userWeatherHistory->getWeatherCode();
        $temperature = $userWeatherHistory->getTemperature();
        $wind = $userWeatherHistory->getWind();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':weatherCode', $weatherCode);
        $statement->bindParam(':temperature', $temperature);
        $statement->bindParam(':wind', $wind);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getUserWeatherHistory(UserWeatherHistory $userWeatherHistory): UserWeatherHistory
    {
        $query = '
            SELECT 
                weather_history_code    AS weatherHistoryCode
                ,user_code              AS userCode
                ,weather_code           AS weatherCode
                ,temperature            AS temperature
                ,wind                   AS wind
                ,create_date            AS createDate
            FROM `user_weather_history`
            WHERE user_code = :userCode
            AND date(create_date) = date(NOW())
        ';

        $statement = $this->database->prepare($query);
        $userCode = $userWeatherHistory->getUserCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        if($statement->rowCount() > 0){
            return $statement->fetchObject(UserWeatherHistory::class);
        }else{
            $failHistory = new UserWeatherHistory();
            $failHistory->setWeatherHistoryCode(0);
            return $failHistory;
        }
    }

    public function createUserInventoryInfo(UserInventoryInfo $inventoryInfo): int
    {
        if(!empty($inventoryInfo->getInventoryCode())){
            $query = '
                INSERT INTO `user_inventory_info`
                    (`inventory_code`, `user_code`, `item_code`, `item_type`, `upgrade_code`, `upgrade_level`
                    , `item_count`, `item_durability`,`create_date`)
                VALUES
                    (:inventoryCode, :userCode, :itemCode, :itemType, :upgradCode, :upgradLevel
                    , :itemCount, :itemDurability, NOW())
                ON DUPLICATE KEY UPDATE 
                    upgrade_code=Values(upgrade_code)
                    ,upgrade_level=Values(upgrade_level)
                    ,item_count=Values(item_count)
                    ,item_durability=Values(item_durability)
            ';
        }else{
            $query = '
                INSERT INTO `user_inventory_info`
                    (`user_code`, `item_code`, `item_type`, `upgrade_code`, `upgrade_level`
                    , `item_count`, `item_durability`,`create_date`)
                VALUES
                    (:userCode, :itemCode, :itemType, :upgradeCode, :upgradeLevel
                    , :itemCount, :itemDurability, NOW())
            ';
        }

        $statement = $this->database->prepare($query);

        $userCode = $inventoryInfo->getUserCode();
        $itemCode = $inventoryInfo->getItemCode();
        $itemType = $inventoryInfo->getItemType();
        $upgradeCode = $inventoryInfo->getUpgradeCode();
        $upgradeLevel = $inventoryInfo->getUpgradeLevel();
        $itemCount = $inventoryInfo->getItemCount();
        $itemDurability = $inventoryInfo->getItemDurability();

        if(!empty($inventoryInfo->getInventoryCode())){
            $inventoryCode = $inventoryInfo->getInventoryCode();
            $statement->bindParam(':inventoryCode', $inventoryCode);
        }

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':itemCode', $itemCode);
        $statement->bindParam(':itemType', $itemType);
        $statement->bindParam(':upgradeCode', $upgradeCode);
        $statement->bindParam(':upgradeLevel', $upgradeLevel);
        $statement->bindParam(':itemCount', $itemCount);
        $statement->bindParam(':itemDurability', $itemDurability);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function createUserShipInfo(UserShipInfo $shipInfo): int
    {
        $query = '
            INSERT INTO `user_ship_info`
                (`user_code`, `ship_code`, `durability`, `fuel`
                , `upgrade_code`, `upgrade_level`, `create_date`)
            VALUES
                (:userCode, :shipCode, :durability, :fuel
                , :upgradeCode, :upgradeLevel, NOW())
            ON DUPLICATE KEY UPDATE 
                durability=Values(durability)
                ,fuel=Values(fuel)
                ,upgrade_code=Values(upgrade_code)
                ,upgrade_level=Values(upgrade_level)
        ';

        $statement = $this->database->prepare($query);

        $userCode = $shipInfo->getUserCode();
        $shipCode = $shipInfo->getShipCode();
        $durability = $shipInfo->getDurability();
        $fuel = $shipInfo->getFuel();
        $upgradeCode = $shipInfo->getUpgradeCode();
        $upgradeLevel = $shipInfo->getUpgradeLevel();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':shipCode', $shipCode);
        $statement->bindParam(':durability', $durability);
        $statement->bindParam(':fuel', $fuel);
        $statement->bindParam(':upgradeCode', $upgradeCode);
        $statement->bindParam(':upgradeLevel', $upgradeLevel);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function modifyUserWeatherHistory(UserWeatherHistory $userWeatherHistory): int
    {
        $query = '
            UPDATE `user_weather_history` 
            SET     temperature = :temperature
                    ,wind       = :wind
                    ,create_date = NOW()
            WHERE weather_history_code = :weatherHistoryCode
        ';

        $statement = $this->database->prepare($query);

        $weatherHistoryCode= $userWeatherHistory->getWeatherHistoryCode();
        $temperature = $userWeatherHistory->getTemperature();
        $wind = $userWeatherHistory->getWind();

        $statement->bindParam(':temperature', $temperature);
        $statement->bindParam(':wind', $wind);
        $statement->bindParam(':weatherHistoryCode', $weatherHistoryCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getUserShipInfo(UserShipInfo $shipInfo): UserShipInfo
    {
        $query = '
            SELECT 
                user_code              AS userCode
                ,ship_code               AS shipCode
                ,durability             AS durability
                ,fuel                   AS fuel
                ,upgrade_code           AS upgradeCode
                ,upgrade_Level          AS upgradeLevel
                ,create_date            AS createDate
            FROM `user_ship_info`
            WHERE user_code = :userCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $shipInfo->getUserCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return $statement->fetchObject(UserShipInfo::class);
    }

    public function getUserInventoryList(SearchInfo $searchInfo): array
    {
        $query = '
            SELECT 
                inventory_code          AS inventoryCode
                ,user_code              AS userCode
                ,item_code              AS itemCode
                ,item_type              AS itemType
                ,upgrade_code           AS upgradCode
                ,upgrade_level          AS upgradLevel
                ,item_count             AS itemCount
                ,item_durability         AS itemDurability
                ,create_date            AS createDate
            FROM `user_inventory_info`
            WHERE user_code = :userCode
            ORDER BY inventory_code
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

    public function getUserInventoryListCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `user_inventory_info`
            WHERE user_code = :userCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getUserInventory(UserInventoryInfo $inventoryInfo): UserInventoryInfo
    {
        $query = '
            SELECT 
                inventory_code          AS inventoryCode
                ,user_code              AS userCode
                ,item_code              AS itemCode
                ,item_type              AS itemType
                ,upgrade_code           AS upgradCode
                ,upgrade_level          AS upgradLevel
                ,item_count             AS itemCount
                ,item_durability         AS itemDurability
                ,create_date            AS createDate
            FROM `user_inventory_info`
            WHERE user_code = :userCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $inventoryInfo->getUserCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return $statement->fetchObject(UserInventoryInfo::class);
    }

    public function deleteUserInfo(UserInfo $userInfo): int
    {
        $query = '
            DELETE U, UI, US, UW
            FROM user_info U
            JOIN user_inventory_info UI ON U.user_code = UI.user_code
            JOIN user_ship_info US ON U.user_code = US.user_code
            LEFT JOIN user_weather_history UW ON U.user_code = UW.user_code
            WHERE U.user_code =:userCode
        ';

        $statement = $this->database->prepare($query);
        $userCode= $userInfo->getUserCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->execute();
        return $statement->rowCount();
    }

    public function deleteUserInventory(UserInventoryInfo $inventoryInfo): int
    {
        $query = '
            DELETE 
            FROM `user_inventory_info`
            WHERE inventory_code =:inventoryCode
        ';

        $statement = $this->database->prepare($query);
        $inventoryCode= $inventoryInfo->getInventoryCode();

        $statement->bindParam(':inventoryCode', $inventoryCode);
        $statement->execute();
        return $statement->rowCount();
    }

    public function createUserFishingItem(UserChoiceItemInfo $choiceItemInfo): int
    {
        if(!empty($choiceItemInfo->getChoiceCode())){
            $query = '
            INSERT INTO `user_choice_item_info`
                (`choice_code`, `user_code`, `fishing_rod_code`, `fishing_line_code`
                , `fishing_needle_code`,`fishing_bait_code`, `fishing_reel_code`, `fishing_item_code1`
                , `fishing_item_code2`, `fishing_item_code3`, `fishing_item_code4`, `create_date`)
            VALUES
                (:choiceCode, :userCode, :fishingRodCode, :fishingLineCode
                , :fishingNeedleCode, :fishingBaitCode, :fishingReelCode, :fishingItemCode1
                , :fishingItemCode2, :fishingItemCode3, :fishingItemCode4, NOW())
            ON DUPLICATE KEY UPDATE 
                fishing_rod_code=Values(fishing_rod_code)
                ,fishing_line_code=Values(fishing_line_code)
                ,fishing_needle_code=Values(fishing_needle_code)
                ,fishing_bait_code=Values(fishing_bait_code)
                ,fishing_reel_code=Values(fishing_reel_code)
                ,fishing_item_code1=Values(fishing_item_code1)
                ,fishing_item_code2=Values(fishing_item_code2)
                ,fishing_item_code3=Values(fishing_item_code3)
                ,fishing_item_code4=Values(fishing_item_code4)
        ';
        }else{
            $query = '
            INSERT INTO `user_choice_item_info`
                (`user_code`, `fishing_rod_code`, `fishing_line_code`
                , `fishing_needle_code`,`fishing_bait_code`, `fishing_reel_code`, `fishing_item_code1`
                , `fishing_item_code2`, `fishing_item_code3`, `fishing_item_code4`, `create_date`)
            VALUES
                (:userCode, :fishingRodCode, :fishingLineCode
                , :fishingNeedleCode, :fishingBaitCode, :fishingReelCode, :fishingItemCode1
                , :fishingItemCode2, :fishingItemCode3, :fishingItemCode4, NOW())
        ';
        }

        $statement = $this->database->prepare($query);
        $userCode = $choiceItemInfo->getUserCode();
        $fishingRodCode = $choiceItemInfo->getFishingRodCode();
        $fishingLineCode = $choiceItemInfo->getFishingLineCode();
        $fishingNeedleCode = $choiceItemInfo->getFishingNeedleCode();
        $fishingBaitCode = $choiceItemInfo->getFishingBaitCode();
        $fishingReelCode = $choiceItemInfo->getFishingReelCode();
        $fishingItemCode1 = $choiceItemInfo->getFishingItemCode1();
        $fishingItemCode2 = $choiceItemInfo->getFishingItemCode2();
        $fishingItemCode3 = $choiceItemInfo->getFishingItemCode3();
        $fishingItemCode4 = $choiceItemInfo->getFishingItemCode4();

        if(!empty($choiceItemInfo->getChoiceCode())){
            $choiceCode = $choiceItemInfo->getAccountCode();
            $statement->bindParam(':choiceCode', $choiceCode);
        }

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':fishingRodCode', $fishingRodCode);
        $statement->bindParam(':fishingLineCode', $fishingLineCode);
        $statement->bindParam(':fishingNeedleCode', $fishingNeedleCode);
        $statement->bindParam(':fishingBaitCode', $fishingBaitCode);
        $statement->bindParam(':fishingReelCode', $fishingReelCode);
        $statement->bindParam(':fishingItemCode1', $fishingItemCode1);
        $statement->bindParam(':fishingItemCode2', $fishingItemCode2);
        $statement->bindParam(':fishingItemCode3', $fishingItemCode3);
        $statement->bindParam(':fishingItemCode4', $fishingItemCode4);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getUserFishingItemList(SearchInfo $searchInfo): array
    {
        return [];
    }

    public function getUserFishingItemListCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `user_choice_item_info`
            WHERE user_code = :userCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return $statement->fetchColumn();
    }
}