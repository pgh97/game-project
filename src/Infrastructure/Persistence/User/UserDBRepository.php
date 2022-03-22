<?php

namespace App\Infrastructure\Persistence\User;

use App\Domain\Common\Entity\Level\UserLevelInfoData;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\User\Entity\UserChoiceItemInfo;
use App\Domain\User\Entity\UserFishDictionary;
use App\Domain\User\Entity\UserFishInventoryInfo;
use App\Domain\User\Entity\UserGitfBoxInfo;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserInventoryInfo;
use App\Domain\User\Entity\UserQuestInfo;
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
        //AND account_code = :accountCode
        $statement = $this->database->prepare($query);
        $userCode = $userInfo->getUserCode();
        //$accountCode = $userInfo->getAccountCode();

        $statement->bindParam(':userCode', $userCode);
        //$statement->bindParam(':accountCode', $accountCode);
        $statement->execute();

        if($statement->rowCount() > 0){
            return $statement->fetchObject(UserInfo::class);
        }else{
            $failUser = new UserInfo();
            $failUser->setUserCode(0);
            return $failUser;
        }
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
        $queryBody = 'update_date = NOW() ';
        $queryEnd = 'WHERE user_code = :userCode';

        if(!empty($userNickNm)){
            $queryBody .= ',user_nicknm = :userNickNm ';
        }
        if(!empty($levelCode)){
            $queryBody .= ',level_code = :levelCode ';
        }
        if(!empty($userExperience)){
            $queryBody .= ',user_experience = :userExperience ';
        }else{
            if($userExperience == 0){
                $queryBody .= ',user_experience = 0 ';
            }
        }
        if(!empty($moneyGold)){
            $queryBody .= ',money_gold = :moneyGold ';
        }else{
            if($moneyGold == 0){
                $queryBody .= ',money_gold = 0 ';
            }
        }
        if(!empty($moneyPearl)){
            $queryBody .= ',money_pearl = :moneyPearl ';

        }else{
            if($moneyPearl == 0){
                $queryBody .= ',money_pearl = 0 ';
            }
        }
        if(!empty($fatigue)){
            $queryBody .= ',fatigue = :fatigue ';
        }else{
            if($fatigue == 0){
                $queryBody .= ',fatigue = 0 ';
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
                , `wind`, `map_code`,`create_date`, `map_update_date`
                , `wind_update_date`, `temperature_update_date`)
            VALUES
                (:userCode, :weatherCode, :temperature
                , :wind, :mapCode, NOW(), NULL, NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
                temperature=Values(temperature)
                ,wind=Values(wind)
                ,map_code=Values(map_code)
        ';
        if(!empty($userWeatherHistory->getMapCode())){
            $query .=',map_update_date=NOW() ';
        }
        if(!empty($userWeatherHistory->getWindUpdateDate())){
            $query .=',wind_update_date=NOW() ';
        }
        if(!empty($userWeatherHistory->getTemperatureUpdateDate())){
            $query .=',temperature_update_date=NOW() ';
        }

        $statement = $this->database->prepare($query);

        $userCode = $userWeatherHistory->getUserCode();
        $weatherCode = $userWeatherHistory->getWeatherCode();
        $temperature = $userWeatherHistory->getTemperature();
        $wind = $userWeatherHistory->getWind();

        if(!empty($userWeatherHistory->getMapCode())){
            $mapCode = $userWeatherHistory->getMapCode();
        }else{
            $mapCode = 0;
        }

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':weatherCode', $weatherCode);
        $statement->bindParam(':temperature', $temperature);
        $statement->bindParam(':wind', $wind);
        $statement->bindParam(':mapCode', $mapCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getUserWeatherHistory(UserWeatherHistory $userWeatherHistory): UserWeatherHistory
    {
        $query = '
            SELECT 
                weather_history_code                        AS weatherHistoryCode
                ,user_code                                  AS userCode
                ,weather_code                               AS weatherCode
                ,temperature                                AS temperature
                ,wind                                       AS wind
                ,IF(map_code IS NULL, 0, map_code)          AS mapCode
                ,create_date                                AS createDate
                ,IF(map_update_date IS NULL, "", map_update_date)   AS mapUpdateDate
                ,IF(wind_update_date IS NULL, "", wind_update_date)   AS windUpdateDate
                ,IF(temperature_update_date IS NULL, "", temperature_update_date)   AS temperatureUpdateDate
            FROM `user_weather_history`
            WHERE user_code = :userCode
        ';
        //WHERE weather_history_code = :weatherHistoryCode AND date(create_date) = date(NOW())

        $statement = $this->database->prepare($query);
        $userCode = $userWeatherHistory->getUserCode();
        //$weatherHistoryCode = $userWeatherHistory->getWeatherHistoryCode();

        $statement->bindParam(':userCode', $userCode);
        //$statement->bindParam(':weatherHistoryCode', $weatherHistoryCode);
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
                    (inventory_code, user_code, item_code, item_type, upgrade_code, upgrade_level
                    , item_count, item_durability, create_date)
                VALUES
                    (:inventoryCode, :userCode, :itemCode, :itemType, :upgradeCode, :upgradeLevel
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
        $queryFront = '
            UPDATE `user_weather_history` 
            SET     temperature = :temperature
                    ,wind       = :wind ';
        $queryBody ='';
        $queryEnd = 'WHERE weather_history_code = :weatherHistoryCode';

        if(!empty($userWeatherHistory->getMapCode())){
            $queryBody .= ',map_update_date=NOW() ';
        }
        if(!empty($userWeatherHistory->getWindUpdateDate())){
            $queryBody .= ',wind_update_date=NOW() ';
        }
        if(!empty($userWeatherHistory->getTemperatureUpdateDate())){
            $queryBody .= ',temperature_update_date=NOW() ';
        }

        $statement = $this->database->prepare($queryFront.$queryBody.$queryEnd);

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
                ,upgrade_code           AS upgradeCode
                ,upgrade_level          AS upgradeLevel
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

        if(!empty($searchInfo->getItemCode())){
            $query .= 'AND item_code=:itemCode ';
        }
        if (!empty($searchInfo->getItemType())){
            $query .= 'AND item_type=:itemType AND item_durability !=0 ';
        }

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();

        $statement->bindParam(':userCode', $userCode);
        if(!empty($searchInfo->getItemCode())){
            $itemCode = $searchInfo->getItemCode();
            $statement->bindParam(':itemCode', $itemCode);
        }
        if (!empty($searchInfo->getItemType())){
            $itemType = $searchInfo->getItemType();
            $statement->bindParam(':itemType', $itemType);
        }

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
                ,upgrade_code           AS upgradeCode
                ,upgrade_level          AS upgradeLevel
                ,item_count             AS itemCount
                ,item_durability         AS itemDurability
                ,create_date            AS createDate
            FROM `user_inventory_info`
            WHERE user_code = :userCode AND inventory_code = :InventoryCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $inventoryInfo->getUserCode();
        $InventoryCode = $inventoryInfo->getInventoryCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':InventoryCode', $InventoryCode);
        $statement->execute();

        if($statement->rowCount()>0){
            return $statement->fetchObject(UserInventoryInfo::class);
        }else{
            $failInventory = new UserInventoryInfo();
            $failInventory->setInventoryCode(0);
            return $failInventory;
        }
    }

    public function getUserInventoryUpgradeItem(SearchInfo $searchInfo): UserInventoryInfo
    {
        $query = '
            SELECT 
                inventory_code          AS inventoryCode
                ,user_code              AS userCode
                ,item_code              AS itemCode
                ,item_type              AS itemType
                ,upgrade_code           AS upgradeCode
                ,upgrade_level          AS upgradeLevel
                ,item_count             AS itemCount
                ,item_durability         AS itemDurability
                ,create_date            AS createDate
            FROM `user_inventory_info`
            WHERE user_code = :userCode AND item_code=:itemCode AND item_type=:itemType
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();
        $itemCode = $searchInfo->getItemCode();
        $itemType = $searchInfo->getItemType();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':itemCode', $itemCode);
        $statement->bindParam(':itemType', $itemType);
        $statement->execute();

        return $statement->fetchObject(UserInventoryInfo::class);
    }

    public function getUserInventoryUpgradeItems(SearchInfo $searchInfo): array
    {
        $query = '
            SELECT 
                inventory_code          AS inventoryCode
                ,user_code              AS userCode
                ,item_code              AS itemCode
                ,item_type              AS itemType
                ,upgrade_code           AS upgradeCode
                ,upgrade_level          AS upgradeLevel
                ,item_count             AS itemCount
                ,item_durability         AS itemDurability
                ,create_date            AS createDate
            FROM `user_inventory_info`
            WHERE user_code = :userCode AND item_code=:itemCode AND item_type=:itemType
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();
        $itemCode = $searchInfo->getItemCode();
        $itemType = $searchInfo->getItemType();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':itemCode', $itemCode);
        $statement->bindParam(':itemType', $itemType);
        $statement->execute();

        return (array) $statement->fetchAll();
    }

    public function getUserInventoryUpgradeItemListCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                IF(sum(item_count) IS NULL, 0, sum(item_count)) AS itemCount
            FROM `user_inventory_info`
            WHERE user_code = :userCode AND item_code=:itemCode AND item_type=:itemType
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();
        $itemCode = $searchInfo->getItemCode();
        $itemType = $searchInfo->getItemType();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':itemCode', $itemCode);
        $statement->bindParam(':itemType', $itemType);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getUserInventoryCode(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                inventory_code          AS inventoryCode
            FROM `user_inventory_info`
            WHERE user_code = :userCode AND item_code=:itemCode AND item_type=:itemType
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();
        $itemCode = $searchInfo->getItemCode();
        $itemType = $searchInfo->getItemType();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':itemCode', $itemCode);
        $statement->bindParam(':itemType', $itemType);
        $statement->execute();

        return $statement->fetchColumn();
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
            $choiceCode = $choiceItemInfo->getChoiceCode();
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
        $query = '
            SELECT 
                choice_code              AS choiceCode
                ,user_code               AS userCode
                ,fishing_rod_code        AS fishingRodCode
                ,fishing_line_code       AS fishingLineCode
                ,fishing_needle_code     AS fishingNeedleCode
                ,fishing_bait_code       AS fishingBaitCode
                ,fishing_reel_code       AS fishingReelCode
                ,fishing_item_code1      AS fishingItemCode1
                ,fishing_item_code2      AS fishingItemCode2
                ,fishing_item_code3      AS fishingItemCode3
                ,fishing_item_code4      AS fishingItemCode4
                ,create_date             AS createDate
            FROM `user_choice_item_info`
            WHERE user_code = :userCode
            ORDER BY choice_code
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

    public function modifyUserShip(UserShipInfo $userShipInfo): int
    {
        $userCode = $userShipInfo->getUserCode();
        $durability = $userShipInfo->getDurability();
        $fuel = $userShipInfo->getFuel();
        $upgradeCode = $userShipInfo->getUpgradeCode();
        $upgradeLevel = $userShipInfo->getUpgradeLevel();

        $queryFront = 'UPDATE `user_ship_info` SET ';
        $queryBody = '';
        $queryEnd = 'WHERE user_code = :userCode';

        if(!empty($durability)){
            if(empty($queryBody)){
                $queryBody .= 'durability = :durability ';
            }else{
                $queryBody .= ',durability = :durability ';
            }
        }else{
            if($durability == 0){
                if(empty($queryBody)){
                    $queryBody .= 'durability = 0 ';
                }else{
                    $queryBody .= ',durability = 0 ';
                }
            }
        }

        if(!empty($fuel)){
            if(empty($queryBody)){
                $queryBody .= 'fuel = :fuel ';
            }else{
                $queryBody .= ',fuel = :fuel ';
            }
        }else{
            if($fuel ==  0){

            }
        }

        if(!empty($upgradeCode)){
            if(empty($queryBody)){
                $queryBody .= 'upgrade_code = :upgradeCode ';
            }else{
                $queryBody .= ',upgrade_code = :upgradeCode ';
            }
        }

        if(!empty($upgradeLevel)){
            if(empty($queryBody)){
                $queryBody .= 'upgrade_level = :upgradeLevel ';
            }else{
                $queryBody .= ',upgrade_level = :upgradeLevel ';
            }
        }

        $query = $queryFront.$queryBody.$queryEnd;
        $statement = $this->database->prepare($query);

        if(!empty($durability)){
            $statement->bindParam(':durability', $durability);
        }
        if(!empty($fuel)){
            $statement->bindParam(':fuel', $fuel);
        }
        if(!empty($upgradeCode)){
            $statement->bindParam(':upgradeCode', $upgradeCode);
        }
        if(!empty($upgradeLevel)){
            $statement->bindParam(':upgradeLevel', $upgradeLevel);
        }

        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getUserFishingItem(UserChoiceItemInfo $choiceItemInfo): UserChoiceItemInfo
    {
        $query = '
           SELECT 
                choice_code              AS choiceCode
                ,user_code               AS userCode
                ,fishing_rod_code        AS fishingRodCode
                ,fishing_line_code       AS fishingLineCode
                ,fishing_needle_code     AS fishingNeedleCode
                ,fishing_bait_code       AS fishingBaitCode
                ,fishing_reel_code       AS fishingReelCode
                ,fishing_item_code1      AS fishingItemCode1
                ,fishing_item_code2      AS fishingItemCode2
                ,fishing_item_code3      AS fishingItemCode3
                ,fishing_item_code4      AS fishingItemCode4
                ,create_date             AS createDate
            FROM `user_choice_item_info`
            WHERE choice_code = :choiceCode
        ';

        $statement = $this->database->prepare($query);
        $choiceCode = $choiceItemInfo->getChoiceCode();

        $statement->bindParam(':choiceCode', $choiceCode);
        $statement->execute();
        return $statement->fetchObject(UserChoiceItemInfo::class);
    }

    public function modifyUserLevel(UserInfo $userInfo): int
    {
        $query = ' 
            UPDATE `user_info` SET
                user_experience = :userExperience
                ,level_code = :levelCode
                ,fatigue = :fatigue
                ,use_inventory_count = :useInventoryCount
            WHERE user_code = :userCode
        ';
        $statement = $this->database->prepare($query);
        $userExperience = $userInfo->getUserExperience();
        $levelCode = $userInfo->getLevelCode();
        $fatigue = $userInfo->getFatigue();
        $useInventoryCount = $userInfo->getUseInventoryCount();
        $userCode = $userInfo->getUserCode();

        $statement->bindParam(':userExperience', $userExperience);
        $statement->bindParam(':levelCode', $levelCode);
        $statement->bindParam(':fatigue', $fatigue);
        $statement->bindParam(':useInventoryCount', $useInventoryCount);
        $statement->bindParam(':userCode', $userCode);
        $statement->execute();
        return (int) $this->database->lastInsertId();
    }

    public function createUserGiftBox(UserGitfBoxInfo $boxInfo): int
    {
        $query = '
            INSERT INTO `user_gift_box_info`
                (`user_code`, `item_code`, `item_type`, `item_count`
                ,`read_status` ,`create_date`)
            SELECT
            :userCode, CI.item_code, CI.item_type, CI.compensation_value, 0, NOW()
            FROM quest_info_data QI
            JOIN quest_compensation_data  QC ON QI.quest_code = QC.quest_code
            JOIN compensation_info_data CI ON QC.compensation_code = CI.compensation_code
            WHERE quest_type=:questType AND quest_goal=:questGoal
        ';
        $statement = $this->database->prepare($query);
        $userCode = $boxInfo->getUserCode();
        $questType = $boxInfo->getQuestType();
        $questGoal = $boxInfo->getQuestGoal();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':questType', $questType);
        $statement->bindParam(':questGoal', $questGoal);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function createUserInventoryFish(UserFishInventoryInfo $userFishInventoryInfo): int
    {
        $query = '
                INSERT INTO `user_inventory_info`
                    (`user_code`, `item_code`, `item_type`, `upgrade_code`, `upgrade_level`
                    , `item_count`, `item_durability`,`create_date`)
                SELECT 
                       user_code, fish_grade_code, 8,  0, 0, 1, 3, now()
                FROM user_fish_inventory_info 
                WHERE user_code=:userCode and map_code=:mapCode
            ';
        $statement = $this->database->prepare($query);
        $userCode = $userFishInventoryInfo->getUserCode();
        $mapCode = $userFishInventoryInfo->getMapCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':mapCode', $mapCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function createUserFishDictionary(UserFishInventoryInfo $userFishInventoryInfo): int
    {
        $query = '
                INSERT INTO `user_fish_dictionary`
                    (`map_fish_code`, `user_code`, `create_date`)
                SELECT 
                       distinct(MF.map_fish_code), UI.user_code, NOW()
                FROM user_fish_inventory_info  UI
                JOIN fish_grade_data FG ON UI.fish_grade_code = FG.fish_grade_code
                JOIN map_fish_data MF ON FG.fish_code = MF.fish_code AND UI.map_code = MF.map_code
                WHERE UI.user_code=:userCode AND UI.map_code=:mapCode
                ON DUPLICATE KEY UPDATE 
                map_fish_code=Values(map_fish_code)
                ,user_code=Values(user_code)
            ';
        $statement = $this->database->prepare($query);
        $userCode = $userFishInventoryInfo->getUserCode();
        $mapCode = $userFishInventoryInfo->getMapCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':mapCode', $mapCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getUserFishDictionaryCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM user_fish_dictionary UFD
            JOIN map_fish_data MF ON UFD.map_fish_code = MF.map_fish_code
            WHERE UFD.user_code=:userCode AND MF.map_code=:mapCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();
        $mapCode = $searchInfo->getItemCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':mapCode', $mapCode);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getUserGiftBoxs(UserGitfBoxInfo $boxInfo): array
    {
        $query = '
            SELECT 
                box_code                AS boxCode
                ,user_code              AS userCode
                ,item_code              AS itemCode
                ,item_type              AS itemType
                ,item_count             AS itemCount
                ,read_status            AS readStatus
                ,create_date            AS createDate
            FROM `user_gift_box_info`
            WHERE user_code = :userCode AND item_type != 99 AND read_status=0
        ';

        $statement = $this->database->prepare($query);
        $userCode = $boxInfo->getUserCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return (array) $statement->fetchAll();
    }

    public function getUserGiftBoxList(SearchInfo $searchInfo): array
    {
        $query = '
            SELECT 
                box_code                AS boxCode
                ,user_code              AS userCode
                ,item_code              AS itemCode
                ,item_type              AS itemType
                ,item_count             AS itemCount
                ,read_status            AS readStatus
                ,create_date            AS createDate
            FROM `user_gift_box_info`
            WHERE user_code = :userCode
            ORDER BY box_code
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

    public function getUserGiftBoxListCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `user_gift_box_info`
            WHERE user_code = :userCode
        ';

        if (!empty($searchInfo->getItemType())){
            $query .= 'AND item_type!=:itemType AND read_status = 0';
        }

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();
        $statement->bindParam(':userCode', $userCode);
        if (!empty($searchInfo->getItemType())){
            $itemType = $searchInfo->getItemType();
            $statement->bindParam(':itemType', $itemType);
        }

        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getUserGiftBoxFishingItemSum(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                IF(sum(item_count) IS NULL, 0, sum(item_count))    AS count
            FROM `user_gift_box_info`
            WHERE user_code = :userCode AND (item_type=1 OR item_type=2 OR item_type=5) AND read_status=0
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();
        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getUserGiftBoxInfo(UserGitfBoxInfo $boxInfo): UserGitfBoxInfo
    {
        $query = '
            SELECT 
                box_code                AS boxCode
                ,user_code              AS userCode
                ,item_code              AS itemCode
                ,item_type              AS itemType
                ,item_count             AS itemCount
                ,read_status            AS readStatus
                ,create_date            AS createDate
            FROM `user_gift_box_info`
            WHERE user_code = :userCode AND box_code = :boxCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $boxInfo->getUserCode();
        $boxCode = $boxInfo->getBoxCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':boxCode', $boxCode);
        $statement->execute();

        if($statement->rowCount() > 0){
            return $statement->fetchObject(UserGitfBoxInfo::class);
        }else{
            $failBox = new UserGitfBoxInfo();
            $failBox->setUserCode(0);
            return $failBox;
        }
    }

    public function modifyUserInventoryFishDurability(UserInventoryInfo $inventoryInfo): int
    {
        $query = ' 
            UPDATE `user_inventory_info` SET
               item_durability = item_durability - datediff(date(now()), date(create_date))
                ,create_date = NOW()
            WHERE user_code = :userCode AND item_type = :itemType 
            AND date(create_date) < date(now())
        ';
        $statement = $this->database->prepare($query);
        $userCode = $inventoryInfo->getUserCode();
        $itemType = $inventoryInfo->getItemType();

        $statement->bindParam(':itemType', $itemType);
        $statement->bindParam(':userCode', $userCode);
        $statement->execute();
        return (int) $this->database->lastInsertId();
    }

    public function deleteUserInventoryFish(SearchInfo $searchInfo): int
    {
        $query = '
            DELETE 
            FROM `user_inventory_info`
            WHERE inventory_code IN (
                SELECT a.inventory_code
                FROM (
                    SELECT inventory_code
                    FROM user_inventory_info 
                    WHERE user_code =:userCode AND item_code=:itemCode AND item_type=:itemType
                    ORDER BY inventory_code
                ) AS a
            )
            LIMIT :limit
        ';
        $statement = $this->database->prepare($query);
        $userCode= $searchInfo->getUserCode();
        $itemCode = $searchInfo->getItemCode();
        $itemType = $searchInfo->getItemType();
        $limit = $searchInfo->getLimit();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':itemCode', $itemCode);
        $statement->bindParam(':itemType', $itemType);
        $statement->bindParam(':limit', $limit);
        $statement->execute();
        return $statement->rowCount();
    }

    public function createUserQuestInfo(UserQuestInfo $userQuestInfo): int
    {
        $query = '
            INSERT INTO `user_quest_info`
                (`user_code`, `quest_code`,`create_date`)
            VALUES
                (:userCode, :questCode, NOW())
        ';
        $statement = $this->database->prepare($query);
        $userCode = $userQuestInfo->getUserCode();
        $questCode = $userQuestInfo->getQuestCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':questCode', $questCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getUserQuestInfoCnt(UserQuestInfo $userQuestInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `user_quest_info`
            WHERE user_code = :userCode AND quest_code = :questCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $userQuestInfo->getUserCode();
        $questCode = $userQuestInfo->getQuestCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':questCode', $questCode);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function createUserGiftBoxToInventory(UserGitfBoxInfo $boxInfo): int
    {
        $query = '
                INSERT INTO `user_inventory_info`
                    (`user_code`, `item_code`, `item_type`, `upgrade_code`, `upgrade_level`
                    , `item_count`, `item_durability`,`create_date`)
                SELECT 
                       user_code, item_code, item_type, 0, 0 , item_count, 1 , NOW()
                FROM user_gift_box_info 
                WHERE user_code=:userCode AND item_type != 99 AND read_status = 0
            ';

        $statement = $this->database->prepare($query);
        $userCode = $boxInfo->getUserCode();
        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function modifyUserGiftBoxStatus(UserGitfBoxInfo $boxInfo): int
    {
        $query = ' 
            UPDATE `user_gift_box_info` SET
               read_status = 1
            WHERE user_code = :userCode
        ';

        if(!empty($boxInfo->getBoxCode())){
            $query .= 'AND box_code = :boxCode';
        }

        $statement = $this->database->prepare($query);
        $userCode = $boxInfo->getUserCode();
        $statement->bindParam(':userCode', $userCode);

        if(!empty($boxInfo->getBoxCode())){
            $boxCode = $boxInfo->getBoxCode();
            $statement->bindParam(':boxCode', $boxCode);
        }
        $statement->execute();
        return (int) $this->database->lastInsertId();
    }

    public function modifyUserInfoGiftBox(UserGitfBoxInfo $boxInfo): int
    {
        $query = ' 
            UPDATE `user_info` SET
               money_gold = money_gold + (select if(sum(item_count) is null, 0,  sum(item_count)) from user_gift_box_info where user_code=:userCode1 and item_type=99 and item_code=1 and read_status = 0)
                ,money_pearl = money_pearl + (select if(sum(item_count) is null, 0,  sum(item_count)) from user_gift_box_info where user_code=:userCode2 and item_type=99 and item_code=2 and read_status = 0)
                ,fatigue = fatigue + (select if(sum(item_count) is null, 0,  sum(item_count)) from user_gift_box_info where user_code=:userCode3 and item_type=99 and item_code=3 and read_status = 0)
            WHERE user_code = :userCode4
        ';

        $statement = $this->database->prepare($query);
        $userCode = $boxInfo->getUserCode();

        $statement->bindParam(':userCode1', $userCode);
        $statement->bindParam(':userCode2', $userCode);
        $statement->bindParam(':userCode3', $userCode);
        $statement->bindParam(':userCode4', $userCode);
        $statement->execute();
        return (int) $this->database->lastInsertId();
    }

    public function createUserGiftBoxShop(UserGitfBoxInfo $boxInfo): int
    {
        $query = '
            INSERT INTO `user_gift_box_info`
                (`user_code`, `item_code`, `item_type`, `item_count`
                ,`read_status` ,`create_date`)
            VALUES
                (:userCode, :itemCode, :itemType, :itemCount, 0, NOW())
        ';
        $statement = $this->database->prepare($query);
        $userCode = $boxInfo->getUserCode();
        $itemCode = $boxInfo->getItemCode();
        $itemType = $boxInfo->getItemType();
        $itemCount = $boxInfo->getItemCount();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':itemCode', $itemCode);
        $statement->bindParam(':itemType', $itemType);
        $statement->bindParam(':itemCount', $itemCount);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function deleteUserFishingItem(UserChoiceItemInfo $choiceItemInfo): int
    {
        $query = '
            DELETE 
            FROM `user_choice_item_info`
            WHERE choice_code =:choiceCode
        ';

        $statement = $this->database->prepare($query);
        $choiceCode= $choiceItemInfo->getChoiceCode();

        $statement->bindParam(':choiceCode', $choiceCode);
        $statement->execute();
        return $statement->rowCount();
    }

    public function deleteUserGiftBox(UserGitfBoxInfo $boxInfo): int
    {
        $query = '
            DELETE 
            FROM `user_gift_box_info`
            WHERE user_code =:userCode AND read_status=1 
        ';

        if (!empty($boxInfo->getBoxCode())){
            $query .= 'AND box_code=:boxCode';
        }

        $statement = $this->database->prepare($query);
        $userCode = $boxInfo->getUserCode();
        $statement->bindParam(':userCode', $userCode);

        if (!empty($boxInfo->getBoxCode())){
            $boxCode = $boxInfo->getBoxCode();
            $statement->bindParam(':boxCode', $boxCode);
        }

        $statement->execute();
        return $statement->rowCount();
    }

    public function getUserFishDictionaryList(SearchInfo $searchInfo): array
    {
        $query = '
            SELECT 
                UFD.map_fish_code      AS mapFishCode 
                ,FI.fish_code          AS fishCode
                ,FI.fish_name          AS fishName
                ,FI.min_depth          AS minDepth
                ,FI.max_depth          AS maxDepth
                ,FI.min_size           AS minSize
                ,FI.max_size           AS maxSize
                ,FI.fish_probability   AS fishProbability
                ,FI.fish_durability    AS fishDurability
                ,UFD.create_date        AS createDate
            FROM user_fish_dictionary UFD
            JOIN map_fish_data MF ON UFD.map_fish_code = MF.map_fish_code
            JOIN fish_info_data FI ON MF.fish_code = FI.fish_code
            WHERE UFD.user_code = :userCode
            ORDER BY FI.fish_code
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

    public function getUserFishDictionaryListCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `user_fish_dictionary`
            WHERE user_code = :userCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getUserFishDictionaryInfo(UserFishDictionary $dictionary): array
    {
        $query = '
            SELECT 
                UFD.map_fish_code      AS mapFishCode 
                ,FI.fish_code          AS fishCode
                ,FI.fish_name          AS fishName
                ,FI.min_depth          AS minDepth
                ,FI.max_depth          AS maxDepth
                ,FI.min_size           AS minSize
                ,FI.max_size           AS maxSize
                ,FI.fish_probability   AS fishProbability
                ,FI.fish_durability    AS fishDurability
                ,UFD.create_date        AS createDate
            FROM user_fish_dictionary UFD
            JOIN map_fish_data MF ON UFD.map_fish_code = MF.map_fish_code
            JOIN fish_info_data FI ON MF.fish_code = FI.fish_code
            WHERE UFD.user_code = :userCode AND UFD.map_fish_code = :mapFishCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $dictionary->getUserCode();
        $mapFishCode = $dictionary->getMapFishCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':mapFishCode', $mapFishCode);
        $statement->execute();

        return (array) $statement->fetchAll();
    }
}