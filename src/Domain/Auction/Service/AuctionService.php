<?php

namespace App\Domain\Auction\Service;

use App\Domain\Auction\Entity\AuctionInfoData;
use App\Domain\Auction\Entity\AuctionRanking;
use App\Domain\Auction\Repository\AuctionRepository;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

class AuctionService extends BaseService
{
    protected AuctionRepository $auctionRepository;
    protected UserRepository $userRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const USER_REDIS_KEY = 'user:%s';
    private const AUCTION_RANK_GOLD = 'AUCTION_RANK_GOLD:%s';
    private const AUCTION_RANK_PEARL = 'AUCTION_RANK_PEARL:%s';

    public function __construct(LoggerInterface $logger
        ,AuctionRepository $auctionRepository
        ,UserRepository $userRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->auctionRepository = $auctionRepository;
        $this->userRepository = $userRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }

    public function getAuctionInfo(array $input):AuctionInfoData
    {
        $data = json_decode((string) json_encode($input), false);
        $myAuctionInfo = new AuctionInfoData();
        $myAuctionInfo->setUserCode($data->decoded->data->userCode);
        $myAuctionInfo->setAuctionCode($data->auctionCode);

        //경매 아이템 조회
        $auctionInfo = $this->auctionRepository->getAuctionInfo($myAuctionInfo);
        $this->logger->info("get auction info Action");
        return $auctionInfo;
    }

    public function getAuctionInfoList(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        //일정시간 경과 경매 가격 변동
        $this->auctionRepository->modifyAuctionInfoList($search);
        //경매 아이템 목록 조회
        $auctionArray = $this->auctionRepository->getAuctionInfoList($search);
        $auctionArrayCnt = $this->auctionRepository->getAuctionInfoListCnt($search);
        $this->logger->info("get list auction info Action");
        return [
            'auctionList' => $auctionArray,
            'totalCount' => $auctionArrayCnt,
        ];
    }

    public function sellAuctionItem(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $sellCount = $data->sellCount;
        //경매 조회
        $myAuction = new AuctionInfoData();
        $myAuction->setUserCode($data->decoded->data->userCode);
        $myAuction->setAuctionCode($data->auctionCode);
        $auctionInfo = $this->auctionRepository->getAuctionInfo($myAuction);

        //회원정보 조회
        if(self::isRedisEnabled() === true){
            $userInfo = $this->getOneUserCache($data->decoded->data->userCode, self::USER_REDIS_KEY);
        }else{
            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($data->decoded->data->userCode);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        }

        //경매의 물고기 등급 코드에 따라 인벤토리 총 카운트조회
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setItemCode($auctionInfo->getFishGradeCode());
        $search->setItemType(8);
        $count = $this->userRepository->getUserInventoryListCnt($search);

        $this->logger->info("sell auction item service");

        //판매할 인벤토리 카운트 비교
        if($count>=$sellCount){
            //물고기 판매 금액 획득
            $addPrice = $auctionInfo->getAuctionPrice() * $sellCount;
            //인벤토리 물고기 삭제
            $search->setLimit($sellCount);
            $this->userRepository->deleteUserInventoryFish($search);

            if($count-$sellCount == 0){
                //경매 아이템 삭제 (팔수있는 물고기가 없기 때문)
                $this->auctionRepository->deleteAuctionInfo($auctionInfo);
            }

            //캐릭터 정보 수정
            if($auctionInfo->getMoneyCode() == 1){
                $userInfo->setMoneyGold($userInfo->getMoneyGold()+$addPrice);
                $moneyNm = "골드";
            }elseif ($auctionInfo->getMoneyCode() == 2){
                $userInfo->setMoneyPearl($userInfo->getMoneyPearl()+$addPrice);
                $moneyNm = "진주";
            }else{
                $userInfo->setFatigue($userInfo->getFatigue()+$addPrice);
                $moneyNm = "피로도";
            }
            $this->userRepository->modifyUserInfo($userInfo);

            //경래 판매 누적 랭킹 등록
            date_default_timezone_set('Asia/Seoul');
            $currentDay = date("Y-m-d");
            $myRank = new AuctionRanking();
            $myRank->setWeekDate($currentDay);
            $myRank->setUserCode($auctionInfo->getUserCode());
            $myRank->setMoneyCode($auctionInfo->getMoneyCode());
            $myRank->setPriceSum($addPrice);
            $this->auctionRepository->createAuctionRank($myRank);

            //$dayName = array("일","월","화","수","목","금","토");
            if(date('w', strtotime($currentDay)) == 0) {
                $currentDay = date("Y-m-d", strtotime("-6 day"));
            } elseif (date('w', strtotime($currentDay)) == 1) {
                $currentDay = date("Y-m-d");
            } else {
                $numDay = date('w', strtotime($currentDay)) -1;
                $currentDay = date("Y-m-d", strtotime("-".$numDay." day"));
            }

            if (self::isRedisEnabled() === true) {
                $userInfo = $this->userRepository->getUserInfo($userInfo);
                $this->saveInCache($data->decoded->data->userCode, $userInfo, self::USER_REDIS_KEY);
                if($auctionInfo->getMoneyCode() == 1){
                    $this->saveInAddRank($data->decoded->data->userCode, $addPrice,self::USER_REDIS_KEY,self::AUCTION_RANK_GOLD, $currentDay);
                }elseif($auctionInfo->getMoneyCode() == 2){
                    $this->saveInAddRank($data->decoded->data->userCode, $addPrice,self::USER_REDIS_KEY,self::AUCTION_RANK_PEARL, $currentDay);
                }
            }
            return [
                'userInfo' => $userInfo,
                'message' => "물고기를 ".$sellCount."개 판매하여 ".$addPrice." ".$moneyNm."를 얻었습니다.",
            ];
        }else{
            return [
                'message' => "판매할 수 있는 물고기 갯수가 부족합니다.",
            ];
        }
    }

    public function getAuctionRank(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        //회원정보 조회
        if(self::isRedisEnabled() === true){
            $userInfo = $this->getOneUserCache($data->decoded->data->userCode, self::USER_REDIS_KEY);
        }else{
            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($data->decoded->data->userCode);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        }

        //7일전까지의 랭킹 목록 조회
        /*$myRankInfo = new AuctionRanking();
        $myRankInfo->setMoneyCode($data->moneyCode);
        $rankInfoList = $this->auctionRepository->getAuctionRankList($myRankInfo);*/

        date_default_timezone_set('Asia/Seoul');
        $currentDay = date("Y-m-d");
        //$dayName = array("일","월","화","수","목","금","토");
        if(date('w', strtotime($currentDay)) == 0) {
            $currentDay = date("Y-m-d", strtotime("-6 day"));
        } elseif (date('w', strtotime($currentDay)) == 1) {
            $currentDay = date("Y-m-d");
        } else {
            $numDay = date('w', strtotime($currentDay)) -1;
            $currentDay = date("Y-m-d", strtotime("-".$numDay." day"));
        }

        //redis에 등록
        /*for ($i = 0; $i < count($rankInfoList); $i++){
            if($data->moneyCode == 1){
                $this->saveInRank($rankInfoList[$i]['userCode'], 0,self::USER_REDIS_KEY,self::AUCTION_RANK_GOLD, $currentDay);
            }elseif($data->moneyCode == 2){
                $this->saveInRank($rankInfoList[$i]['userCode'], 0,self::USER_REDIS_KEY,self::AUCTION_RANK_PEARL, $currentDay);
            }
        }*/

        //redis에 랭킹 조회
        if($data->moneyCode == 1){
            $rankInfoList = $this->getArrayRank(self::AUCTION_RANK_GOLD, 0,-1, $currentDay);
        }elseif($data->moneyCode == 2){
            $rankInfoList = $this->getArrayRank(self::AUCTION_RANK_PEARL,0,-1, $currentDay);
        }else{
            $rankInfoList = NULL;
        }

        // redis 랭킹 치환
        $rankList = array();
        $i = 1;
        foreach ($rankInfoList as $id => $score){
            $info = array();
            $info['userCode'] = str_replace('uruk-game:user:', '', $id);
            $info['priceSum'] = $score;
            $info['rank'] = $i;
            array_push($rankList, $info);
            $i++;
        }

        $this->logger->info("get auction ranking service");
        if(array_filter($rankList)){
            return [
                'auctionRankList' => $rankList,
                'message' => null,
            ];
        }else{
            return [
                'message' => "현재 측정된 랭킹이 없습니다.",
            ];
        }
    }

    protected function saveInCache(int $userCode, object $user, string $redisKey): void
    {
        $redisKey = sprintf($redisKey, $userCode);
        $key = $this->redisService->generateKey($redisKey);
        //$this->redisService->setex($key, $user);
        $this->redisService->set($key, $user);
    }

    protected function deleteFromCache(int $userCode, string $redisKey): void
    {
        $redisKey = sprintf($redisKey, $userCode);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del([$key]);
    }

    protected function saveInAddRank(int $userCode, int $score, string $redisId, string $redisKey, string $date): void
    {
        $redisId = sprintf($redisId, $userCode);
        $id = $this->redisService->generateKey($redisId);

        $redisKey = sprintf($redisKey, $date);
        $redisKey = $this->redisService->generateKey($redisKey);
        $this->redisService->zincrby($redisKey, $id, $score);
    }

    protected function saveInRank(int $userCode, int $score, string $redisId, string $redisKey, string $date): void
    {
        $redisId = sprintf($redisId, $userCode);
        $id = $this->redisService->generateKey($redisId);

        $redisKey = sprintf($redisKey, $date);
        $redisKey = $this->redisService->generateKey($redisKey);
        $this->redisService->zadd($redisKey, $id, $score);
    }

    protected function getOneUserCache(int $code, string $redisKeys): UserInfo
    {
        $redisKey = sprintf($redisKeys, $code);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $model = json_decode((string)json_encode($this->redisService->get($key)), false);

            $userInfo = new UserInfo();
            $userInfo->setAccountCode($model->accountCode);
            $userInfo->setUserCode($model->userCode);
            $userInfo->setUserNickNm($model->userNickNm);
            $userInfo->setLevelCode($model->levelCode);
            $userInfo->setUserExperience($model->userExperience);
            $userInfo->setMoneyGold($model->moneyGold);
            $userInfo->setMoneyPearl($model->moneyPearl);
            $userInfo->setFatigue($model->fatigue);
            $userInfo->setUseInventoryCount($model->useInventoryCount);
            $userInfo->setUseSaveItemCount($model->useSaveItemCount);
            $userInfo->setCreateDate($model->createDate);
        } else {
            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($code);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);
        }
        return $userInfo;
    }

    protected function getArrayRank(string $redisKey , int $startRank , int $endRank, string $date):array
    {
        $redisKey = sprintf($redisKey, $date);
        $redisKey = $this->redisService->generateKey($redisKey);
        return $this->redisService->zrevrange($redisKey, $startRank, $endRank);
    }
}