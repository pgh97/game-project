<?php
declare(strict_types=1);

namespace App\Domain\Auth\Repository;

use App\Domain\Auth\Entity\AccountDeleteInfo;
use App\Domain\Auth\Entity\AccountInfo;

interface AccountInfoRepository
{
    /**
     * @param AccountInfo $accountInfo
     * @return int
     */
    public function createAccountInfo(AccountInfo $accountInfo): int;

    /**
     * @param AccountInfo $accountInfo
     * @return AccountInfo
     */
    public function loginAccountInfo(AccountInfo $accountInfo): AccountInfo;

    /**
     * @param AccountInfo $accountInfo
     * @return int
     */
    public function modifyLastLoginDate(AccountInfo $accountInfo): int;

    /**
     * @param AccountInfo $accountInfo
     * @return int
     */
    public function deleteAccountInfo(AccountInfo $accountInfo): int;

    /**
     * @param AccountDeleteInfo $accountInfo
     * @return int
     */
    public function createAccountDeleteInfo(AccountDeleteInfo $accountInfo): int;

    /**
     * @param AccountInfo $accountInfo
     * @return AccountInfo
     */
    public function getAccountInfo(AccountInfo $accountInfo): AccountInfo;

    /**
     * @param AccountInfo $accountInfo
     * @return int
     */
    public function modifyAccountInfo(AccountInfo $accountInfo): int;

    /**
     * @param AccountInfo $accountInfo
     * @return int
     */
    public function getAccountIdCount(AccountInfo $accountInfo): int;

    /**
     * @param AccountInfo $accountInfo
     * @return int
     */
    public function getUserInfoMaxLevel(AccountInfo $accountInfo): int;
}