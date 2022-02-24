<?php
declare(strict_types=1);

namespace App\Domain\Auth\Repository;

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
}