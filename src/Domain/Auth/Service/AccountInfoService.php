<?php

namespace App\Domain\Auth\Service;

use App\Domain\Auth\Entity\AccountInfo;
use App\Domain\Auth\Repository\AccountInfoRepository;
use Psr\Log\LoggerInterface;

class AccountInfoService
{
    protected AccountInfoRepository $accountInfoRepository;
    protected LoggerInterface $logger;

     public function __construct(LoggerInterface $logger, AccountInfoRepository $accountInfoRepository) {
         $this->logger = $logger;
         $this->accountInfoRepository = $accountInfoRepository;
     }

    public function createAccountInfo(array $input): int
    {
        $data = json_decode((string) json_encode($input), false);
        $myAccountInfo = new AccountInfo();

        $myAccountInfo->setAccountType($data->accountType);
        $myAccountInfo->setHiveCode($data->hiveCode);
        $myAccountInfo->setAccountId($data->accountId);
        $myAccountInfo->setAccountPw(password_hash($data->accountPw,PASSWORD_DEFAULT));
        $myAccountInfo->setCountryCode($data->countryCode);
        $myAccountInfo->setLanguageCode($data->languageCode);

        /** @var TYPE_NAME $accountInfo */
        $accountInfo = $this->accountInfoRepository->createAccountInfo($myAccountInfo);
        $this->logger->info("create account service");
        return $accountInfo;
    }

    public function loginAccountInfo(array $input): object
    {
        $data = json_decode((string) json_encode($input), false);
        $myAccountInfo = new AccountInfo();

        $myAccountInfo->setAccountId($data->accountId);

        /** @var TYPE_NAME $accountInfo */
        $accountInfo = $this->accountInfoRepository->loginAccountInfo($myAccountInfo);

        if(password_verify($data->accountPw, $accountInfo->getAccountPw())){
            $this->logger->info("success login account service");
            return $accountInfo;
        }else{
            $this->logger->info("fail login account service");
            return 0;
        }
    }
}