<?php

namespace App\Infrastructure\Persistence\User;

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
                , `money_gold`,`money_pearl`, `fatigue`, `use_inventory_count`, `use_save_item_count`, `create_dt`)
            VALUES
                (:accountCode, :userNickNm, :levelCode, :userExperience
                , :moneyGold, :moneyPearl, :fatigue, :useInventoryCount, :useSaveItemCount, NOW())
            ON DUPLICATE KEY UPDATE 
                last_login_date = NOW()
        ';
        $statement = $this->database->prepare($query);

        $accountCode = $userInfo->getAccountCode();
        $userNickNm = $userInfo->getUserNickNm();
        $levelCode= $userInfo->getLevelCode();
        $userExperience = $userInfo->getUserExperience();
        $moneyGold= $userInfo->getMoneyGold();
        $moneyPearl = $userInfo->getMoneyPearl();
        $fatigue = $userInfo->getFatigue();
        $useInventoryCount = $userInfo->getUserInventoryCount();
        $useSaveItemCount = $userInfo->getUserSaveItemCount();

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
        return 0;
    }
}