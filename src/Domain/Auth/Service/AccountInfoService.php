<?php

namespace App\Domain\Auth\Service;

use App\Domain\Auth\Entity\AccountDeleteInfo;
use App\Domain\Auth\Entity\AccountInfo;
use App\Domain\Auth\Repository\AccountInfoRepository;
use App\Exception\AccountInfoException;
use Psr\Log\LoggerInterface;
use Firebase\JWT\JWT;

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

        // 1. 비밀번호 암호화 방법
        //$myAccountInfo->setAccountPw(password_hash($data->accountPw,PASSWORD_BCRYPT));

        // 2. 비밀번호 암호화 방법
        $myAccountInfo->setAccountPw(hash('sha256', $data->accountPw));
        $myAccountInfo->setCountryCode($data->countryCode);
        $myAccountInfo->setLanguageCode($data->languageCode);

        /** @var TYPE_NAME $accountInfo */
        $accountInfo = $this->accountInfoRepository->createAccountInfo($myAccountInfo);
        $this->logger->info("create account service");
        return $accountInfo;
    }

    /**
     * @throws \Exception
     */
    public function loginAccountInfo(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myAccountInfo = new AccountInfo();

        if (! isset($data->accountId)) {
            throw new AccountInfoException('The field "ID" is required.', 400);
        }
        if (! isset($data->accountPw)) {
            throw new AccountInfoException('The field "PASSWORD" is required.', 400);
        }

        $myAccountInfo->setAccountId($data->accountId);
        // 2. 비밀번호 암호화 방법
        $myAccountInfo->setAccountPw(hash('sha256', $data->accountPw));

        /** @var TYPE_NAME $accountInfo */
        $accountInfo = $this->accountInfoRepository->loginAccountInfo($myAccountInfo);

        // 1. 비밀번호 암호화 방법 (복호화가 안되기 때문에 비교를 해야함)
        /*if(password_verify($data->accountPw, $accountInfo->getAccountPw())){
            $this->logger->info("success login account service");
        }else{
            $this->logger->info("fail login account service");
        }*/

        if(!$accountInfo->getIsSuccess()){
            return array();
        }else{
            //마지막 로그인날짜 업로드
            $this->accountInfoRepository->modifyLastLoginDate($accountInfo);
            $token = [
                'iss' => "http://localhost:8888",
                'iat' => time(),
                'nbf' => time(),
                'exp' => time() + (7 * 24 * 60 * 60),
                'data' => [
                    'accountCode' => $accountInfo->getAccountCode(),
                    'accountId' => $accountInfo->getAccountId(),
                ]
            ];

            return [
                'Authorization' => 'Bearer ' .JWT::encode($token, $_SERVER['SECRET_KEY'], 'HS256'),
                'accountInfo' => $accountInfo,
            ];
        }
    }

    public function getAccountInfo(array $input): AccountInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myAccountInfo = new AccountInfo();
        $myAccountInfo->setAccountCode($data->decoded->data->accountCode);

        $accountInfo = $this->accountInfoRepository->getAccountInfo($myAccountInfo);
        $this->logger->info("delete account info service");
        return $accountInfo;
    }

    public function modifyAccountInfo(array $input): AccountInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myAccountInfo = new AccountInfo();
        $myAccountInfo->setAccountCode($data->decoded->data->accountCode);

        if(!empty($data->accountPw)){
            $myAccountInfo->setAccountPw(hash('sha256', $data->accountPw));
        }
        if(!empty($data->languageCode)){
            $myAccountInfo->setLanguageCode($data->languageCode);
        }
        if(!empty($data->countryCode)){
            $myAccountInfo->setCountryCode($data->countryCode);
        }

        $this->accountInfoRepository->modifyAccountInfo($myAccountInfo);
        $accountInfo = $this->accountInfoRepository->getAccountInfo($myAccountInfo);
        $this->logger->info("update account service");
        return $accountInfo;
    }

    public function removeAccountInfo(array $input): int
    {
        $data = json_decode((string) json_encode($input), false);
        $myAccountInfo = new AccountInfo();
        $myAccountInfo->setAccountCode($data->decoded->data->accountCode);

        $myAccountDeleteInfo = new AccountDeleteInfo();
        $myAccountDeleteInfo->setAccountCode($data->decoded->data->accountCode);
        $myAccountDeleteInfo->setDeleteType($data->deleteType);

        $resultCode = $this->accountInfoRepository->createAccountDeleteInfo($myAccountDeleteInfo);
        if($resultCode > 0){
            $this->accountInfoRepository->deleteAccountInfo($myAccountInfo);
        }
        $this->logger->info("delete Account info service");
        return $resultCode;
    }
}