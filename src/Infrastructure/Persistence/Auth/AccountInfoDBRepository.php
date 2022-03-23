<?php

namespace App\Infrastructure\Persistence\Auth;

use App\Domain\Auth\Entity\AccountDeleteInfo;
use App\Domain\Auth\Entity\AccountInfo;
use App\Domain\Auth\Repository\AccountInfoRepository;
use App\Infrastructure\Persistence\BaseRepository;

class AccountInfoDBRepository extends BaseRepository implements AccountInfoRepository
{
    public function createAccountInfo(AccountInfo $accountInfo): int
    {
        $query = '
            INSERT INTO `account_info`
                (`account_type`, `hive_code`, `account_id`, `account_pw`
                , `country_code`,`language_code`, `last_login_date`, create_date)
            VALUES
                (:accountType, :hiveCode, :accountId, :accountPw
                , :countryCode, :languageCode, NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
                last_login_date = NOW()
        ';
        $statement = $this->database->prepare($query);

        $accountType = $accountInfo->getAccountType();
        $hiveCode = $accountInfo->getHiveCode();
        $accountId = $accountInfo->getAccountId();
        $accountPw = $accountInfo->getAccountPw();
        $countryCode = $accountInfo->getCountryCode();
        $languageCode = $accountInfo->getLanguageCode();

        $statement->bindParam(':accountType', $accountType);
        $statement->bindParam(':hiveCode', $hiveCode);
        $statement->bindParam(':accountId', $accountId);
        $statement->bindParam(':accountPw', $accountPw);
        $statement->bindParam(':countryCode', $countryCode);
        $statement->bindParam(':languageCode', $languageCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function loginAccountInfo(AccountInfo $accountInfo): AccountInfo
    {
        $query = '
            SELECT 
                account_code        AS accountCode
                ,account_type       AS accountType
                ,hive_code          AS hiveCode
                ,account_id         AS accountId
                ,country_code       AS countryCode
                ,language_code      AS languageCode
                ,last_login_date    AS lastLoginDate
                ,create_date        AS createDate
            FROM `account_info`
            WHERE account_id = :accountId AND account_pw = :accountPw
        ';
        $statement = $this->database->prepare($query);
        $accountId = $accountInfo->getAccountId();
        $accountPw = $accountInfo->getAccountPw();

        $statement->bindParam(':accountId', $accountId);
        $statement->bindParam(':accountPw', $accountPw);
        $statement->execute();

        if($statement->rowCount() > 0){
            return $statement->fetchObject(AccountInfo::class);
        }else{
            $failAccountInfo = new AccountInfo();
            $failAccountInfo->setIsSuccess(false);
            return $failAccountInfo;
        }
    }

    public function modifyLastLoginDate(AccountInfo $accountInfo): int
    {
        $query = '
            UPDATE `account_info`
            SET last_login_date = NOW()
            WHERE account_code = :accountCode
        ';
        $statement = $this->database->prepare($query);
        $accountCode = $accountInfo->getAccountCode();

        $statement->bindParam(':accountCode', $accountCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function deleteAccountInfo(AccountInfo $accountInfo): int
    {
        $query = '
            DELETE A, U, UI, US, UW
            FROM account_info A 
            LEFT JOIN user_info U ON A.account_code = U.account_code
            LEFT JOIN user_inventory_info UI ON U.user_code = UI.user_code
            LEFT JOIN user_ship_info US ON U.user_code = US.user_code
            LEFT JOIN user_weather_history UW ON U.user_code = UW.user_code
            WHERE A.account_code =:accountCode
        ';

        $statement = $this->database->prepare($query);
        $accountCode= $accountInfo->getAccountCode();

        $statement->bindParam(':accountCode', $accountCode);
        $statement->execute();
        return $statement->rowCount();
    }

    public function createAccountDeleteInfo(AccountDeleteInfo $accountInfo): int
    {
        $query = '
            INSERT INTO `account_delete_info`
                (`delete_type`, `account_code`, `account_type`, `hive_code`, `account_id`
                , `country_code`,`language_code`, delete_date)
            SELECT :deleteType, account_code, account_type, hive_code, account_id
                 , country_code, language_code, NOW()
            FROM account_info 
            WHERE account_code = :accountCode
        ';
        $statement = $this->database->prepare($query);

        $deleteType = $accountInfo->getDeleteType();
        $accountCode = $accountInfo->getAccountCode();

        $statement->bindParam(':deleteType', $deleteType);
        $statement->bindParam(':accountCode', $accountCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }

    public function getAccountInfo(AccountInfo $accountInfo): AccountInfo
    {
        $query = '
            SELECT 
                account_code        AS accountCode
                ,account_type       AS accountType
                ,hive_code          AS hiveCode
                ,account_id         AS accountId
                ,country_code       AS countryCode
                ,language_code      AS languageCode
                ,last_login_date    AS lastLoginDate
                ,create_date        AS createDate
            FROM `account_info`
            WHERE account_code = :accountCode
        ';
        $statement = $this->database->prepare($query);
        $accountCode = $accountInfo->getAccountCode();

        $statement->bindParam(':accountCode', $accountCode);
        $statement->execute();

        return $statement->fetchObject(AccountInfo::class);
    }

    public function modifyAccountInfo(AccountInfo $accountInfo): int
    {
        $accountCode = $accountInfo->getAccountCode();
        $accountPw = $accountInfo->getAccountPw();
        $countryCode = $accountInfo->getCountryCode();
        $languageCode = $accountInfo->getLanguageCode();

        $queryFront = '
            UPDATE `account_info`
            SET last_login_date = NOW() ';
        $queryEnd = 'WHERE account_code = :accountCode';
        $queryBody = '';

        if (!empty($accountPw)){
            $queryBody .=',account_pw=:accountPw ';
        }
        if (!empty($countryCode)){
            $queryBody .=',country_code=:countryCode ';
        }
        if (!empty($languageCode)){
            $queryBody .=',language_code=:languageCode ';
        }

        $query = $queryFront.$queryBody.$queryEnd;
        $statement = $this->database->prepare($query);

        if (!empty($accountPw)){
            $statement->bindParam(':accountPw', $accountPw);
        }
        if (!empty($countryCode)){
            $statement->bindParam(':countryCode', $countryCode);
        }
        if (!empty($languageCode)){
            $statement->bindParam(':languageCode', $languageCode);
        }

        $statement->bindParam(':accountCode', $accountCode);
        $statement->execute();

        return $statement->rowCount();
    }

    public function getAccountIdCount(AccountInfo $accountInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `account_info`
            WHERE account_id = :accountId
        ';

        $statement = $this->database->prepare($query);
        $accountId = $accountInfo->getAccountId();

        $statement->bindParam(':accountId', $accountId);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function getUserInfoMaxLevel(AccountInfo $accountInfo): int
    {
        $query = '
            SELECT 
                IF(max(level_code) IS NULL, 0, max(level_code)) AS levelCode
            FROM `user_info`
            WHERE account_code = :accountCode
        ';

        $statement = $this->database->prepare($query);
        $accountCode = $accountInfo->getAccountCode();

        $statement->bindParam(':accountCode', $accountCode);
        $statement->execute();

        return $statement->fetchColumn();
    }
}