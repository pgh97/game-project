<?php

namespace App\Domain\Fishing\Service;

use App\Domain\Common\Entity\fish\FishGradeData;
use App\Domain\Common\Entity\fish\FishInfoData;
use App\Domain\Common\Entity\Level\UserLevelInfoData;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Entity\Weather\WeatherInfoData;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\Map\Entity\MapInfoData;
use App\Domain\Map\Entity\MapItemData;
use App\Domain\Map\Entity\MapTideData;
use App\Domain\Map\Repository\MapRepository;
use App\Domain\Quest\Entity\QuestInfoData;
use App\Domain\Quest\Repository\QuestRepository;
use App\Domain\Upgrade\Entity\FishingItemUpgradeData;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use App\Domain\User\Entity\UserChoiceItemInfo;
use App\Domain\User\Entity\UserFishInventoryInfo;
use App\Domain\User\Entity\UserGitfBoxInfo;
use App\Domain\User\Entity\UserInfo;
use App\Domain\User\Entity\UserInventoryInfo;
use App\Domain\User\Entity\UserQuestInfo;
use App\Domain\User\Entity\UserWeatherHistory;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

class FishingService extends BaseService
{
    protected FishingRepository $fishingRepository;
    protected UserRepository $userRepository;
    protected MapRepository $mapRepository;
    protected QuestRepository $questRepository;
    protected UpgradeRepository $upgradeRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const FISHING_REDIS_KEY = 'fishing:%s';
    private const USER_REDIS_KEY = 'user:%s';
    private const WEATHER_REDIS_KEY = 'weather:%s';
    private const MAP_REDIS_KEY = 'map:%s';
    private const TIDE_REDIS_KEY = 'mapTide:%s';

    public function __construct(LoggerInterface $logger
        ,FishingRepository $fishingRepository
        ,UserRepository $userRepository
        ,MapRepository $mapRepository
        ,QuestRepository $questRepository
        ,UpgradeRepository $upgradeRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->fishingRepository = $fishingRepository;
        $this->userRepository = $userRepository;
        $this->mapRepository = $mapRepository;
        $this->questRepository = $questRepository;
        $this->upgradeRepository = $upgradeRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }

    public function getFishInventory(array $input):UserFishInventoryInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myFishInventory = new UserFishInventoryInfo();
        $myFishInventory->setUserCode($data->decoded->data->userCode);
        $myFishInventory->setFishInventoryCode($data->fishInventoryCode);

        //잡은 물고기 상세 조회
        $fishInventory = $this->fishingRepository->getUserFishInventory($myFishInventory);
        $this->logger->info("get fish inventory info service");
        return $fishInventory;
    }

    public function getFishInventoryList(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setItemCode($data->mapCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        //잡은 물고기 목록 조회
        $fishInventoryArray = $this->fishingRepository->getUserFishInventoryList($search);
        $fishInventoryArrayCnt = $this->fishingRepository->getUserFishInventoryListCnt($search);
        $this->logger->info("get list fish inventory info service");
        return [
            'fishInventoryList' => $fishInventoryArray,
            'totalCount' => $fishInventoryArrayCnt,
        ];
    }

    public function fishingOperate(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        if(self::isRedisEnabled() === true){
            $userInfo = $this->getOneUserCache($data->decoded->data->userCode, self::USER_REDIS_KEY);
            $weatherHistoryInfo = $this->getOneWeatherCache($data->decoded->data->weatherHistoryCode, $data->decoded->data->userCode,self::WEATHER_REDIS_KEY);
            $mapInfo = $this->getOneMapCache($data->mapCode, self::MAP_REDIS_KEY);
            $mapTideInfo = $this->getOneMapTideCache($data->mapCode, self::TIDE_REDIS_KEY);
        }else{
            $myUserInfo = new UserInfo();
            $myUserInfo->setUserCode($data->decoded->data->userCode);
            $userInfo = $this->userRepository->getUserInfo($myUserInfo);

            $myWeatherInfo= new UserWeatherHistory();
            $myWeatherInfo->setUserCode($data->decoded->data->userCode);
            $weatherHistoryInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);

            $myMapInfo = new MapInfoData();
            $myMapInfo->setMapCode($data->mapCode);
            $mapInfo = $this->mapRepository->getMapInfo($myMapInfo);

            $myTideInfo = new MapTideData();
            $myTideInfo->setMapCode($data->mapCode);
            $myTideInfo->setSort(1);
            $mapTideInfo = $this->mapRepository->getMapTideInfo($myTideInfo);
       }
        //채비 수심 +- 10까지
        $fishingItemDepth = $data->depth;
        //채비
        $choiceItem = new UserChoiceItemInfo();
        $choiceItem->setUserCode($data->decoded->data->userCode);
        $choiceItem->setChoiceCode($data->choiceCode);
        $choiceItem = $this->userRepository->getUserFishingItem($choiceItem);

        $myUserInventory = new UserInventoryInfo();
        $myUserInventory->setUserCode($data->decoded->data->userCode);
        $myUserInventory->setInventoryCode($choiceItem->getFishingRodCode());
        //인벤토리 낚시대
        $inventoryRod = $this->userRepository->getUserInventory($myUserInventory);
        //낚시대 등급 정보
        $itemRod = $this->fishingRepository->getFishingRodGradeData($inventoryRod);

        //인벤토리 낚시줄
        $myUserInventory->setInventoryCode($choiceItem->getFishingLineCode());
        $inventoryLine = $this->userRepository->getUserInventory($myUserInventory);
        //낚시줄 등급 정보
        $itemLine = $this->fishingRepository->getFishingLineGradeData($inventoryLine);

        //인벤토리 바늘
        $myUserInventory->setInventoryCode($choiceItem->getFishingNeedleCode());
        $inventoryNeedle = $this->userRepository->getUserInventory($myUserInventory);
        //바늘 등급 정보
        $itemNeedle = $this->fishingRepository->getFishingNeedleGradeData($inventoryNeedle);

        //인벤토리 미끼
        $myUserInventory->setInventoryCode($choiceItem->getFishingBaitCode());
        $inventoryBait = $this->userRepository->getUserInventory($myUserInventory);
        //미끼 등급 정보
        $itemBait = $this->fishingRepository->getFishingBaitGradeData($inventoryBait);

        //인벤토리 릴
        $myUserInventory->setInventoryCode($choiceItem->getFishingReelCode());
        $inventoryReel = $this->userRepository->getUserInventory($myUserInventory);
        //릴 등급 정보
        $itemReel = $this->fishingRepository->getFishingReelGradeData($inventoryReel);

        //낚시 아이템1
        $myUserInventory->setInventoryCode($choiceItem->getFishingItemCode1());
        $inventoryItem1 = $this->userRepository->getUserInventory($myUserInventory);
        //낚시 아이템2
        $myUserInventory->setInventoryCode($choiceItem->getFishingItemCode2());
        $inventoryItem2 = $this->userRepository->getUserInventory($myUserInventory);
        //낚시 아이템3
        $myUserInventory->setInventoryCode($choiceItem->getFishingItemCode3());
        $inventoryItem3 = $this->userRepository->getUserInventory($myUserInventory);
        //낚시 아이템4
        $myUserInventory->setInventoryCode($choiceItem->getFishingItemCode4());
        $inventoryItem4 = $this->userRepository->getUserInventory($myUserInventory);

        //필수 채비의 내구도 or 카운트 비교
        if($inventoryRod->getItemDurability() >= $mapInfo->getPerDurability()
            && $inventoryLine->getItemDurability() >= $mapInfo->getPerDurability()
            && $inventoryNeedle->getItemCount() >= 1
            && $inventoryBait->getItemCode() >= 1
            && $inventoryReel->getItemDurability() >= $mapInfo->getPerDurability()){
            //평균 수심
            $depth = $mapInfo->getMaxDepth() - $mapInfo->getMinDepth();

            //날씨 정보
            $weatherCode = new WeatherInfoData();
            $weatherCode->setWeatherCode($weatherHistoryInfo->getWeatherCode());
            $weatherInfo = $this->commonRepository->getWeatherInfo($weatherCode);

            //낚시대, 낚시줄, 릴의 제압, 훅킹확률 적용
            
            //지역별 물고기 리스트
            $search = new SearchInfo();
            $search->setItemCode($data->mapCode);
            $search->setUserCode($data->decoded->data->userCode);
            $fishList = $this->mapRepository->getMapFishList($search);

            //지역별 부품 리스트 (물고기 리스트에 0임)
            $itemList = $this->mapRepository->getMapItemList($search);

            //조수간만차 계산
            date_default_timezone_set('Asia/Seoul');
            $currentDay = date("Y-m-d");
            $tomorrowDay = date("Y-m-d", strtotime("+1 day"));
            $currentTime = date("Y-m-d H:i:s");

            if(strtotime($mapTideInfo->getLowTideTime1()) >= strtotime($mapTideInfo->getHighTideTime1())){
                $mapTideInfo->setHighTideTime1($currentDay." ".$mapTideInfo->getHighTideTime1());
                $mapTideInfo->setLowTideTime1($currentDay." ".$mapTideInfo->getLowTideTime1());
            }else{
                $mapTideInfo->setHighTideTime1($currentDay." ".$mapTideInfo->getHighTideTime1());
                $mapTideInfo->setLowTideTime1($tomorrowDay." ".$mapTideInfo->getLowTideTime1());
            }

            if(strtotime($mapTideInfo->getLowTideTime2()) >= strtotime($mapTideInfo->getHighTideTime2())){
                $mapTideInfo->setHighTideTime2($currentDay." ".$mapTideInfo->getHighTideTime2());
                $mapTideInfo->setLowTideTime2($currentDay." ".$mapTideInfo->getLowTideTime2());
            }else{
                $mapTideInfo->setHighTideTime2($currentDay." ".$mapTideInfo->getHighTideTime2());
                $mapTideInfo->setLowTideTime2($tomorrowDay." ".$mapTideInfo->getLowTideTime2());
            }

            $fishCodeArray = array();
            $fishCodeArray[] = 0;
            $percentArray = array();
            $percentArray[] = 1;

            if(strtotime($mapTideInfo->getHighTideTime1()) < strtotime($currentTime)
                && strtotime($mapTideInfo->getLowTideTime1()) > strtotime($currentTime)){
                $timeDif1 = strtotime($mapTideInfo->getLowTideTime1()) - strtotime($mapTideInfo->getHighTideTime1());
                $currentTimeDif1= strtotime($currentTime) - strtotime($mapTideInfo->getHighTideTime1());
                $current1 = floor($currentTimeDif1/(60*60));
                $delDepth = floor($depth/$timeDif1) * $current1;
                //조수간만차에 따라 최대 수심 감소
                $mapInfo->setMaxDepth($mapInfo->getMaxDepth()-$delDepth);

                //채비 수심 정하기
                if($mapInfo->getMaxDepth() >= $fishingItemDepth + 10){
                    $maxDepth = $fishingItemDepth+10;
                }else{
                    $maxDepth = $mapInfo->getMaxDepth();
                }

                if($mapInfo->getMinDepth() >= $fishingItemDepth - 10){
                    $minDepth = $fishingItemDepth-10;
                }else{
                    $minDepth = $mapInfo->getMinDepth();
                }

                for ($i=0; $i<count($fishList); $i++){
                    if($maxDepth >= $fishList[$i]['minDepth']){
                        if($minDepth <= $fishList[$i]['minDepth']){
                            $fishCodeArray[] = $fishList[$i]['fishCode'];
                            $percentArray[] = $fishList[$i]['fishProbability']+$itemBait->getFishProbability();
                        }
                    }
                }

            }else if(strtotime($mapTideInfo->getHighTideTime2()) < strtotime($currentTime)
                && strtotime($mapTideInfo->getLowTideTime2()) > strtotime($currentTime)){
                $timeDif2 = strtotime($mapTideInfo->getLowTideTime2()) - strtotime($mapTideInfo->getHighTideTime2());
                $currentTimeDif2= strtotime($currentTime) - strtotime($mapTideInfo->getHighTideTime2());
                $current2 = floor($currentTimeDif2/(60*60));
                $delDepth = floor($depth/$timeDif2) * $current2;
                //조수간만차에 따라 최대 수심 감소
                $mapInfo->setMaxDepth($mapInfo->getMaxDepth()-$delDepth);

                //채비 수심 정하기
                if($mapInfo->getMaxDepth() >= $fishingItemDepth + 10){
                    $maxDepth = $fishingItemDepth+10;
                }else{
                    $maxDepth = $mapInfo->getMaxDepth();
                }

                if($mapInfo->getMinDepth() >= $fishingItemDepth - 10){
                    $minDepth = $fishingItemDepth-10;
                }else{
                    $minDepth = $mapInfo->getMinDepth();
                }

                for ($i=0; $i<count($fishList); $i++){
                    if($maxDepth >= $fishList[$i]['minDepth']){
                        if($minDepth <= $fishList[$i]['minDepth']){
                            $fishCodeArray[] = $fishList[$i]['fishCode'];
                            $percentArray[] = $fishList[$i]['fishProbability']+$itemBait->getFishProbability();
                        }
                    }
                }
            }else{
                if(strtotime($mapTideInfo->getHighTideTime1()) == strtotime($currentTime) || strtotime($mapTideInfo->getHighTideTime2()) == strtotime($currentTime)){
                    //조수간만차에 따라 최소 수심 증가
                    $mapInfo->setMinDepth($mapInfo->getMaxDepth());
                    //채비 수심 정하기
                    if($mapInfo->getMaxDepth() >= $fishingItemDepth + 10){
                        $maxDepth = $fishingItemDepth+10;
                    }else{
                        $maxDepth = $mapInfo->getMaxDepth();
                    }

                    if($mapInfo->getMinDepth() >= $fishingItemDepth - 10){
                        $minDepth = $fishingItemDepth-10;
                    }else{
                        $minDepth = $mapInfo->getMinDepth();
                    }

                    for ($i=0; $i<count($fishList); $i++){
                        if($maxDepth >= $fishList[$i]['minDepth']){
                            if($minDepth <= $fishList[$i]['minDepth']){
                                $fishCodeArray[] = $fishList[$i]['fishCode'];
                                $percentArray[] = abs($fishList[$i]['fishProbability']+$mapTideInfo->getAppearProbability())+$itemBait->getFishProbability();
                            }
                        }
                    }
                }else if(strtotime($mapTideInfo->getLowTideTime1()) == strtotime($currentTime) || strtotime($mapTideInfo->getLowTideTime2()) == strtotime($currentTime)){
                    //조수간만차에 따라 최대 수심 감소
                    $mapInfo->setMaxDepth($mapInfo->getMinDepth());
                    //채비 수심 정하기
                    if($mapInfo->getMaxDepth() >= $fishingItemDepth + 10){
                        $maxDepth = $fishingItemDepth+10;
                    }else{
                        $maxDepth = $mapInfo->getMaxDepth();
                    }

                    if($mapInfo->getMinDepth() >= $fishingItemDepth - 10){
                        $minDepth = $fishingItemDepth-10;
                    }else{
                        $minDepth = $mapInfo->getMinDepth();
                    }

                    for ($i=0; $i<count($fishList); $i++){
                        if($maxDepth >= $fishList[$i]['minDepth']){
                            if($minDepth <= $fishList[$i]['minDepth']){
                                $fishCodeArray[] = $fishList[$i]['fishCode'];
                                $percentArray[] = abs($fishList[$i]['fishProbability']-$mapTideInfo->getAppearProbability())+$itemBait->getFishProbability();
                            }
                        }
                    }
                }else if(strtotime($mapTideInfo->getLowTideTime1()) < strtotime($currentTime)
                    && strtotime($mapTideInfo->getHighTideTime2()) > strtotime($currentTime)){
                    $timeDif3 = strtotime($mapTideInfo->getHighTideTime2())-strtotime($mapTideInfo->getLowTideTime1());
                    $currentTimeDif3= strtotime($currentTime) - strtotime($mapTideInfo->getLowTideTime1());
                    $current3 = floor($currentTimeDif3/(60*60));
                    $addDepth = floor($depth/$timeDif3) * $current3;
                    //조수간만차에 따라 최소 수심 증가
                    $mapInfo->setMinDepth($mapInfo->getMinDepth()+$addDepth);

                    //채비 수심 정하기
                    if($mapInfo->getMaxDepth() >= $fishingItemDepth + 10){
                        $maxDepth = $fishingItemDepth+10;
                    }else{
                        $maxDepth = $mapInfo->getMaxDepth();
                    }

                    if($mapInfo->getMinDepth() >= $fishingItemDepth - 10){
                        $minDepth = $fishingItemDepth-10;
                    }else{
                        $minDepth = $mapInfo->getMinDepth();
                    }

                    for ($i=0; $i<count($fishList); $i++){
                        if($maxDepth >= $fishList[$i]['minDepth']){
                            if($minDepth <= $fishList[$i]['minDepth']){
                                $fishCodeArray[] = $fishList[$i]['fishCode'];
                                $percentArray[] = $fishList[$i]['fishProbability']+$itemBait->getFishProbability();
                            }
                        }
                    }
                }else {//if(strtotime($mapTideInfo->getLowTideTime2()) < strtotime($currentTime)){
                    $tmpTime = date($mapTideInfo->getHighTideTime1(), strtotime("+1 day"));
                    $timeDif4 = strtotime($tmpTime) - strtotime($mapTideInfo->getLowTideTime2());
                    $currentTimeDif4= strtotime($currentTime) - strtotime($mapTideInfo->getLowTideTime2());
                    $current4 = floor($currentTimeDif4/(60*60));
                    $addDepth = floor($depth/$timeDif4) * $current4;
                    //조수간만차에 따라 최소 수심 증가
                    $mapInfo->setMinDepth($mapInfo->getMinDepth()+$addDepth);

                    //채비 수심 정하기
                    if($mapInfo->getMaxDepth() >= $fishingItemDepth + 10){
                        $maxDepth = $fishingItemDepth+10;
                    }else{
                        $maxDepth = $mapInfo->getMaxDepth();
                    }

                    if($mapInfo->getMinDepth() >= $fishingItemDepth - 10){
                        $minDepth = $fishingItemDepth-10;
                    }else{
                        $minDepth = $mapInfo->getMinDepth();
                    }

                    for ($i=0; $i<count($fishList); $i++){
                        if($maxDepth >= $fishList[$i]['minDepth']){
                            if($minDepth <= $fishList[$i]['minDepth']){
                                $fishCodeArray[] = $fishList[$i]['fishCode'];
                                $percentArray[] = $fishList[$i]['fishProbability']+$itemBait->getFishProbability();
                            }
                        }
                    }
                }
            }

            //제압과 훅킹 성공여부
            $successArray = array(0,1); //0:성공, 1:실패
            if($itemRod->getItemType() == 2){
                $itemRod->setHookingProbability($itemRod->getHookingProbability()*3);
            }
            $suppress = $itemRod->getSuppressProbability()+$itemLine->getSuppressProbability()+$itemNeedle->getSuppressProbability();
            $hooking = $itemRod->getHookingProbability()+$itemLine->getHookingProbability()+$itemNeedle->getHookingProbability();
            $probabilityArray = array(20+$suppress+$hooking, abs(100-(20+$suppress+$hooking)));
            $successYn = $this->Percent_draw($successArray, $probabilityArray);

            if($successYn == 0){
                //인벤토리 카운트 비교
                $fishInventoryCnt = $this->fishingRepository->getUserFishInventoryListCnt($search);
                $inventoryCnt = $this->userRepository->getUserInventoryListCnt($search);
                
                //캐릭터 인벤토리 최대 카운트와 비교
                if($userInfo->getUseInventoryCount() > ($inventoryCnt+$fishInventoryCnt)){
                    //채비 내구도 감소
                    $inventoryRod->setItemDurability($inventoryRod->getItemDurability()-$mapInfo->getPerDurability());
                    $this->userRepository->createUserInventoryInfo($inventoryRod);

                    $inventoryLine->setItemDurability($inventoryLine->getItemDurability()-$mapInfo->getPerDurability());
                    $this->userRepository->createUserInventoryInfo($inventoryLine);

                    $inventoryNeedle->setItemCount($inventoryNeedle->getItemCount()-1);
                    $this->userRepository->createUserInventoryInfo($inventoryNeedle);

                    $inventoryBait->setItemCount($inventoryBait->getItemCount()-1);
                    $this->userRepository->createUserInventoryInfo($inventoryBait);

                    $inventoryReel->setItemDurability($inventoryReel->getItemDurability()-$mapInfo->getPerDurability());
                    $this->userRepository->createUserInventoryInfo($inventoryReel);

                    if(!empty($inventoryItem1->getInventoryCode())){
                        $inventoryItem1->setItemCount($inventoryItem1->getItemCount()-1);
                        $this->userRepository->createUserInventoryInfo($inventoryItem1);
                    }
                    if(!empty($inventoryItem2->getInventoryCode())){
                        $inventoryItem2->setItemCount($inventoryItem1->getItemCount()-1);
                        $this->userRepository->createUserInventoryInfo($inventoryItem2);
                    }
                    if(!empty($inventoryItem3->getInventoryCode())){
                        $inventoryItem3->setItemCount($inventoryItem1->getItemCount()-1);
                        $this->userRepository->createUserInventoryInfo($inventoryItem3);
                    }
                    if(!empty($inventoryItem4->getInventoryCode())){
                        $inventoryItem4->setItemCount($inventoryItem1->getItemCount()-1);
                        $this->userRepository->createUserInventoryInfo($inventoryItem4);
                    }
                    //부품 or 물고기
                    $code = $this->Percent_draw($fishCodeArray, $percentArray);
                    $message = null;
                    //물고기일 경우
                    if($code != 0){
                        //등급별 물고기 뽑기
                        $fishInfo = new FishInfoData();
                        $fishInfo->setFishCode($code);
                        $fishInfo = $this->commonRepository->getFishInfo($fishInfo);

                        $sizeArray = array();
                        $percentArray = array();

                        for ($i=$fishInfo->getMinSize(); $i<=$fishInfo->getMaxSize(); $i++){
                            $sizeArray[] = $i;
                            if($i == $fishInfo->getMinSize()){
                                $percentArray[] = $mapInfo->getFishSizeProbability();
                            }else{
                                $temp = (100-$mapInfo->getFishSizeProbability())/2;
                                $percentArray[] = $temp;
                            }
                        }

                        //등급별 물고기 조회
                        $fishGradeInfo = new FishGradeData();
                        $fishGradeInfo->setFishCode($fishInfo->getFishCode());
                        $fishGradeInfo->setMinValue($this->Percent_draw($sizeArray, $percentArray)*$fishInfo->getFishProbability());
                        $fishGradeInfo = $this->commonRepository->getFishGradeData($fishGradeInfo);

                        $userFishInventoryInfo = new UserFishInventoryInfo();
                        $userFishInventoryInfo->setUserCode($data->decoded->data->userCode);
                        $userFishInventoryInfo->setMapCode($data->mapCode);
                        $userFishInventoryInfo->setFishGradeCode($fishGradeInfo->getFishGradeCode());
                        $this->fishingRepository->createUserFishInventory($userFishInventoryInfo);

                        //캐릭터 경험치 제공
                        $userInfo->setUserExperience($userInfo->getUserExperience()+$fishGradeInfo->getAddExperience());

                        $myLevelInfo = new UserLevelInfoData();
                        $myLevelInfo->setLevelCode($userInfo->getLevelCode());
                        $levelInfo = $this->userRepository->getUserLevelInfo($myLevelInfo);
                        //레벨 경험치 비교후 레벨업
                        if ($userInfo->getUserExperience() >= $levelInfo->getLevelExperience()){
                            $userInfo->setUserExperience($userInfo->getUserExperience()-$levelInfo->getLevelExperience());
                            $userInfo->setLevelCode($levelInfo->getLevelCode()+1);

                            $myLevelInfo->setLevelCode($userInfo->getLevelCode());
                            $levelInfo = $this->userRepository->getUserLevelInfo($myLevelInfo);

                            $userInfo->setUseInventoryCount($levelInfo->getInventoryCount());
                            $userInfo->setFatigue($levelInfo->getMaxFatigue());

                            //지역 퀘스트 상세 조회
                            $myQuestInfo = new QuestInfoData();
                            $myQuestInfo->setQuestType(1);
                            $myQuestInfo->setQuestGoal($userInfo->getLevelCode());
                            $questInfo = $this->questRepository->getQuestInfoGoal($myQuestInfo);
                            //캐릭터 퀘스트 여부
                            $userQuestInfo = new UserQuestInfo();
                            $userQuestInfo->setUserCode($userInfo->getUserCode());
                            $userQuestInfo->setQuestCode($questInfo->getQuestCode());
                            $userQuestCnt = $this->userRepository->getUserQuestInfoCnt($userQuestInfo);

                            if($userQuestCnt == 0){
                                //선물함(우편함) 등록
                                $boxInfo = new UserGitfBoxInfo();
                                $boxInfo->setUserCode($userInfo->getUserCode());
                                $boxInfo->setQuestType(1);
                                $boxInfo->setQuestGoal($userInfo->getLevelCode());
                                $this->userRepository->createUserGiftBox($boxInfo);

                                //캐릭터 퀘스트 등록
                                $this->userRepository->createUserQuestInfo($userQuestInfo);
                            }
                            $message = "레벨 ".$userInfo->getLevelCode()."로 레벨업했습니다!";
                        }
                        $itemInfo = $fishGradeInfo;
                    }else{ //부품일 경우
                        $itemArray = array();
                        $percentArray = array();

                        for ($i=0; $i<count($itemList); $i++){
                            $itemArray[] = $itemList[$i]['mapItemCode'];
                            $percentArray[] = $itemList[$i]['itemProbability'];
                        }

                        $myMapItem = new MapItemData();
                        $myMapItem->setMapItemCode($this->Percent_draw($itemArray, $percentArray));
                        $mapItem = $this->mapRepository->getMapItemInfo($myMapItem);
                        //물고기 인벤토리가 아닌 캐릭터 인벤토리에 저장
                        $myInventory = new UserInventoryInfo();
                        $myInventory->setUserCode($data->decoded->data->userCode);
                        if($mapItem->getItemType()==1 || $mapItem->getItemType()==2 || $mapItem->getItemType()==5){
                            //업그레이드 초기값 코드 조회
                            $myUpgrade = new FishingItemUpgradeData();
                            $myUpgrade->setItemGradeCode($mapItem->getItemCode());
                            $myUpgrade->setItemType($mapItem->getItemType());
                            $myUpgrade->setUpgradeLevel(1);
                            $upgradeCode = $this->upgradeRepository->getFishingItemUpgradeCode($myUpgrade);

                            $myInventory->setItemCode($mapItem->getItemCode());
                            $myInventory->setItemType($mapItem->getItemType());
                            $myInventory->setUpgradeCode($upgradeCode);
                            $myInventory->setUpgradeLevel(1);
                            $myInventory->setItemCount(1);
                            $myInventory->setItemDurability(5);
                            //인벤토리 등록
                            $userInventoryCode = $this->userRepository->createUserInventoryInfo($myInventory);
                        }else{
                            //인벤토리 여부 확인, 인벤토리 코드 return
                            $search->setItemCode($mapItem->getItemCode());
                            $search->setItemType($mapItem->getItemType());
                            $inventoryCode = $this->userRepository->getUserInventoryCode($search);

                            if ($inventoryCode != 0){
                                //인벤토리에 같은 아이템 조회
                                $myInventory->setInventoryCode($inventoryCode);
                                $inventoryInfo = $this->userRepository->getUserInventory($myInventory);
                                //인벤토리 등록 (카운트 증가)
                                $inventoryInfo->setItemCount($inventoryInfo->getItemCount()+3);
                                $userInventoryCode = $this->userRepository->createUserInventoryInfo($inventoryInfo);
                            }else{
                                $myInventory->setItemCode($mapItem->getItemCode());
                                $myInventory->setItemType($mapItem->getItemType());
                                $myInventory->setUpgradeCode(0);
                                $myInventory->setUpgradeLevel(0);
                                $myInventory->setItemCount(3);
                                $myInventory->setItemDurability(1);
                                //인벤토리 등록
                                $userInventoryCode = $this->userRepository->createUserInventoryInfo($myInventory);
                            }
                        }
                        $myInventory->setInventoryCode($userInventoryCode);
                        $itemInfo = $this->userRepository->getUserInventory($myInventory);
                    }

                    $this->userRepository->modifyUserLevel($userInfo);
                    if (self::isRedisEnabled() === true) {
                        $user = $this->userRepository->getUserInfo($userInfo);
                        $this->saveInCache($userInfo->getUserCode(), $user, self::USER_REDIS_KEY);
                    }
                    $this->logger->info("fishing operate service");
                    return [
                        'itemInfo' => $itemInfo,
                        'message' => $message,
                    ];
                }else{
                    $this->logger->info("full fish inventory service");
                    return [
                        'itemInfo' => null,
                        'message' => '인벤토리가 가득찼습니다! 입항해주세요~',
                    ];
                }
            }else{

                //채비 내구도 감소
                $inventoryRod->setItemDurability($inventoryRod->getItemDurability()-$mapInfo->getPerDurability());
                $this->userRepository->createUserInventoryInfo($inventoryRod);

                $inventoryLine->setItemDurability($inventoryLine->getItemDurability()-$mapInfo->getPerDurability());
                $this->userRepository->createUserInventoryInfo($inventoryLine);

                $inventoryNeedle->setItemCount($inventoryNeedle->getItemCount()-1);
                $this->userRepository->createUserInventoryInfo($inventoryNeedle);

                $inventoryBait->setItemCount($inventoryBait->getItemCount()-1);
                $this->userRepository->createUserInventoryInfo($inventoryBait);

                $inventoryReel->setItemDurability($inventoryReel->getItemDurability()-$mapInfo->getPerDurability());
                $this->userRepository->createUserInventoryInfo($inventoryReel);

                if(!empty($inventoryItem1->getInventoryCode())){
                    $inventoryItem1->setItemCount($inventoryItem1->getItemCount()-1);
                    $this->userRepository->createUserInventoryInfo($inventoryItem1);
                }
                if(!empty($inventoryItem2->getInventoryCode())){
                    $inventoryItem2->setItemCount($inventoryItem1->getItemCount()-1);
                    $this->userRepository->createUserInventoryInfo($inventoryItem2);
                }
                if(!empty($inventoryItem3->getInventoryCode())){
                    $inventoryItem3->setItemCount($inventoryItem1->getItemCount()-1);
                    $this->userRepository->createUserInventoryInfo($inventoryItem3);
                }
                if(!empty($inventoryItem4->getInventoryCode())){
                    $inventoryItem4->setItemCount($inventoryItem1->getItemCount()-1);
                    $this->userRepository->createUserInventoryInfo($inventoryItem4);
                }

                return [
                    'fishInfo' => null,
                    'message' => '물고기를 놓쳤습니다.',
                ];
            }
        }else{
            $this->logger->info("check fishing item service");
            return [
                'fishInfo' => null,
                'message' => '채비의 내구도 혹은 갯수를 확인해주세요~',
            ];
        }
    }

    public function deleteFishInventory(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $userFishInventory = new UserFishInventoryInfo();
        $userFishInventory->setUserCode($data->decoded->data->userCode);
        $userFishInventory->setFishInventoryCode($data->fishInventoryCode);
        //잡은 물고기 삭제
        $result = $this->fishingRepository->deleteUserFishInventory($userFishInventory);
        if($result>0){
            return [
                'message' => '잡은 물고기를 삭제했습니다.'
            ];
        }else{
            return [
                'message' => '잡은 물고기 삭제를 실패했습니다.'
            ];
        }
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

    protected function getOneWeatherCache(int $code, int $userCode, string $redisKeys): UserWeatherHistory
    {
        $redisKey = sprintf($redisKeys, $code);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $model = json_decode((string)json_encode($this->redisService->get($key)), false);

            $weatherInfo = new UserWeatherHistory();
            $weatherInfo->setWeatherHistoryCode($model->weatherHistoryCode);
            $weatherInfo->setUserCode($model->userCode);
            $weatherInfo->setWeatherCode($model->weatherCode);
            $weatherInfo->setTemperature($model->temperature);
            $weatherInfo->setWind($model->wind);
            $weatherInfo->setMapCode($model->mapCode);
            $weatherInfo->setCreateDate($model->createDate);
            $weatherInfo->setMapUpdateDate($model->mapUpdateDate);
            $weatherInfo->setWindUpdateDate($model->windUpdateDate);
            $weatherInfo->setTemperatureUpdateDate($model->temperatureUpdateDate);
        } else {
            $myWeatherInfo = new UserWeatherHistory();
            $myWeatherInfo->setUserCode($userCode);
            $weatherInfo = $this->userRepository->getUserWeatherHistory($myWeatherInfo);
        }
        return $weatherInfo;
    }

    protected function getOneMapCache(int $code, string $redisKeys):MapInfoData
    {
        $redisKey = sprintf($redisKeys, $code);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $model = json_decode((string) json_encode($this->redisService->get($key)), false);

            $mapInfo = new MapInfoData();
            $mapInfo->setMapCode($model->mapCode);
            $mapInfo->setMapName($model->mapName);
            $mapInfo->setMaxDepth($model->maxDepth);
            $mapInfo->setMinDepth($model->minDepth);
            $mapInfo->setMinLevel($model->minLevel);
            $mapInfo->setDistance($model->distance);
            $mapInfo->setMoneyCode($model->moneyCode);
            $mapInfo->setDeparturePrice($model->departurePrice);
            $mapInfo->setDepartureTime($model->departureTime);
            $mapInfo->setPerDurability($model->perDurability);
            $mapInfo->setMapFishCount($model->mapFishCount);
            $mapInfo->setFishSizeProbability($model->fishSizeProbability);
            $mapInfo->setCreateDate($model->createDate);
        }else{
            $myMapInfo = new MapInfoData();
            $myMapInfo->setMapCode($code);
            $mapInfo = $this->mapRepository->getMapInfo($myMapInfo);
        }
        return $mapInfo;
    }

    protected function getOneMapTideCache(int $code, string $redisKeys): MapTideData
    {
        $redisKey = sprintf($redisKeys, $code);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $model = json_decode((string) json_encode($this->redisService->get($key)), false);

            $mapTideInfo = new MapTideData();
            $mapTideInfo->setMapTideCode($model->mapTideCode);
            $mapTideInfo->setMapCode($model->mapCode);
            $mapTideInfo->setTideCode($model->tideCode);
            $mapTideInfo->setTideSort($model->tideSort);
            $mapTideInfo->setHighTideTime1($model->highTideTime1);
            $mapTideInfo->setLowTideTime1($model->lowTideTime1);
            $mapTideInfo->setHighTideTime2($model->highTideTime2);
            $mapTideInfo->setLowTideTime2($model->lowTideTime2);
            $mapTideInfo->setWaterSplashTime($model->waterSplashTime);
            $mapTideInfo->setAppearProbability($model->appearProbability);
            $mapTideInfo->setCreateDate($model->createDate);
        }else{
            $myMapInfo = new MapTideData();
            $myMapInfo->setMapCode($code);
            $myMapInfo->setSort(1);
            $mapTideInfo = $this->mapRepository->getMapTideInfo($myMapInfo);
        }
        return $mapTideInfo;
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

    // 확률에 따른 물고기 등급 뽑기
    protected function Percent_draw($items_list, $percent_list)
    {
        $range_now = 0;
        $range_last = 0;
        $decimal = 4;
        if(count($percent_list) != count($items_list)) return false;
        $draw = mt_rand(1,pow(10,$decimal)*array_sum($percent_list));
        for($sequence=0; $sequence<count($percent_list); $sequence++) {
            $range_now += pow(10,$decimal)*$percent_list[$sequence];
            if($range_now >= $draw && $range_last < $draw) {
                return $items_list[$sequence];
            }else{
                $range_last = $range_now;
            }
        }
    }
}