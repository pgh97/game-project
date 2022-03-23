<?php

namespace App\Domain\Auth\Service;

use App\Domain\Auth\Entity\AccountDeleteInfo;
use App\Domain\Auth\Entity\AccountInfo;
use App\Domain\Auth\Repository\AccountInfoRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Common\Service\ScribeService;
use App\Exception\AccountInfoException;
use App\Exception\ErrorCode;
use Psr\Log\LoggerInterface;
use Firebase\JWT\JWT;

class AccountInfoService
{
    protected AccountInfoRepository $accountInfoRepository;
    protected ScribeService $scribeService;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

     public function __construct(LoggerInterface $logger
         , AccountInfoRepository $accountInfoRepository
         , ScribeService $scribeService
         , RedisService $redisService) {
         $this->logger = $logger;
         $this->accountInfoRepository = $accountInfoRepository;
         $this->scribeService = $scribeService;
         $this->redisService = $redisService;
     }

    public function createAccountInfo(array $input): array
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

        //회원 있는지 확인
        $accountIdCount = $this->accountInfoRepository->getAccountIdCount($myAccountInfo);

        //회원 생성
        $accountCode = $this->accountInfoRepository->createAccountInfo($myAccountInfo);
        $code = new ErrorCode();
        if($accountCode > 0){
            if($accountIdCount==0){
                $myAccountInfo->setAccountCode($accountCode);
                $accountInfo = $this->accountInfoRepository->getAccountInfo($myAccountInfo);
                //scribe 로그 남기기
                date_default_timezone_set('Asia/Seoul');
                $currentDate = date("Ymd");
                $currentTime = date("Y-m-d H:i:s");

                $dataJson = json_encode([
                    "date" => $currentTime,
                    "last_login_date" => $currentTime,
                    "channel" => "C2S",
                    "user_id" => $accountCode,
                    "app_id" => ScribeService::PROJECT_NAME,
                    "client_ip" => $_SERVER['REMOTE_ADDR'],
                    "server_ip" => $_SERVER['SERVER_ADDR'],
                    "guid" => $_SERVER['GUID']
                ]);

                $msg[] = new \LogEntry(array(
                    'category' => 'new_user_log_'.$currentDate,
                    'message' => $dataJson
                ));
                $this->scribeService->Log($msg);
            }

            $this->logger->info("create account service");
            return [
                'accountCode' => $accountCode,
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS_CREATED),
            ];
        }else{
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::BAD_REQUEST),
            ];
        }
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

        //회원로그인
        $accountInfo = $this->accountInfoRepository->loginAccountInfo($myAccountInfo);

        // 1. 비밀번호 암호화 방법 (복호화가 안되기 때문에 비교를 해야함)
        /*if(password_verify($data->accountPw, $accountInfo->getAccountPw())){
            $this->logger->info("success login account service");
        }else{
            $this->logger->info("fail login account service");
        }*/
        $code = new ErrorCode();
        if(!$accountInfo->getIsSuccess()){
            $this->logger->info("fail login account service");
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::UNAUTHENTICATED),
            ];
        }else{
            $this->logger->info("success login account service ");
            //scribe 로그 남기기
            date_default_timezone_set('Asia/Seoul');
            $currentDate = date("Ymd");
            $currentTime = date("Y-m-d H:i:s");
            $levelCode = $this->accountInfoRepository->getUserInfoMaxLevel($accountInfo);

            $dataJson = json_encode([
                "date" => $currentTime,
                "last_login_date" => $currentTime,
                "channel" => "C2S",
                "user_id" => $accountInfo->getAccountCode(),
                "app_id" => ScribeService::PROJECT_NAME,
                "client_ip" => $_SERVER['REMOTE_ADDR'],
                "server_ip" => $_SERVER['SERVER_ADDR'],
                "level" => $levelCode,
                "guid" => $_SERVER['GUID']
            ]);

            $msg[] = new \LogEntry(array(
                'category' => 'login_log_'.$currentDate,
                'message' => $dataJson
            ));
            $this->scribeService->Log($msg);

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
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
            ];
        }
    }

    public function getAccountInfo(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $code = new ErrorCode();
        $myAccountInfo = new AccountInfo();
        if(!empty($data->decoded->data->accountCode)){
            $myAccountInfo->setAccountCode($data->decoded->data->accountCode);
            //회원 정보 조회
            $accountInfo = $this->accountInfoRepository->getAccountInfo($myAccountInfo);
            $this->logger->info("delete account info service");
            return [
                'accountInfo' => $accountInfo,
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
            ];
        }else{
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::UNAUTHENTICATED),
            ];
        }
    }

    public function modifyAccountInfo(array $input): array
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

        //회원 정보 수정
        $resultCode = $this->accountInfoRepository->modifyAccountInfo($myAccountInfo);
        $accountInfo = $this->accountInfoRepository->getAccountInfo($myAccountInfo);
        $code = new ErrorCode();
        if($resultCode > 0){
            $this->logger->info("update account service");
            return [
                'accountInfo' => $accountInfo,
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS_CREATED),
            ];
        }else{
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::BAD_REQUEST),
            ];
        }
    }

    public function removeAccountInfo(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $myAccountInfo = new AccountInfo();
        $myAccountInfo->setAccountCode($data->decoded->data->accountCode);

        $myAccountDeleteInfo = new AccountDeleteInfo();
        $myAccountDeleteInfo->setAccountCode($data->decoded->data->accountCode);
        $myAccountDeleteInfo->setDeleteType($data->deleteType);

        //회원 탈퇴
        $resultCode = $this->accountInfoRepository->createAccountDeleteInfo($myAccountDeleteInfo);
        $code = new ErrorCode();
        if($resultCode > 0){
            $this->accountInfoRepository->deleteAccountInfo($myAccountInfo);
            $levelCode = $this->accountInfoRepository->getUserInfoMaxLevel($myAccountInfo);
            //scribe 로그 남기기
            date_default_timezone_set('Asia/Seoul');
            $currentDate = date("Ymd");
            $currentTime = date("Y-m-d H:i:s");

            $dataJson = json_encode([
                "date" => $currentTime,
                "channel" => "C2S",
                "user_id" => $myAccountInfo->getAccountCode(),
                "app_id" => ScribeService::PROJECT_NAME,
                "level" => $levelCode,
                "client_ip" => $_SERVER['REMOTE_ADDR'],
                "server_ip" => $_SERVER['SERVER_ADDR'],
            ]);

            $msg[] = new \LogEntry(array(
                'category' => 'withdraw_log_'.$currentDate,
                'message' => $dataJson
            ));
            $this->scribeService->Log($msg);
            $this->logger->info("delete Account info service");
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::SUCCESS),
            ];
        }else{
            return [
                'codeArray' =>  $code->getErrorArrayItem(ErrorCode::FAIL_FUNCTION),
            ];
        }
    }
}