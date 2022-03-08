<?php

namespace App\Infrastructure\Persistence\Auth;

use App\Domain\Auth\Entity\AccountInfo;
use App\Domain\Auth\Repository\AccountInfoRepository;
use App\Infrastructure\Persistence\BaseRepository;

class AccountInfoDBRepository extends BaseRepository implements AccountInfoRepository
{
//    public function checkAndGetAccountInfo(int $accountCode): AccountInfo
//    {
//        $query = 'SELECT * FROM `account_info` WHERE `account` = :id';
//        $statement = $this->database->prepare($query);
//        $statement->bindParam(':id', $noteId);
//        $statement->execute();
//        $note = $statement->fetchObject(Note::class);
//        if (! $note) {
//            throw new \App\Exception\Note('Note not found.', 404);
//        }
//
//        return $note;
//    }

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
}