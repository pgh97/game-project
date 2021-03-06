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
use App\Domain\Common\Service\ScribeService;
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
use App\Exception\ErrorCode;
use Psr\Log\LoggerInterface;

class FishingService extends BaseService
{
    protected FishingRepository $fishingRepository;
    protected UserRepository $userRepository;
    protected MapRepository $mapRepository;
    protected QuestRepository $questRepository;
    protected UpgradeRepository $upgradeRepository;
    protected CommonRepository $commonRepository;
    protected ScribeService $scribeService;
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
        ,ScribeService $scribeService
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->fishingRepository = $fishingRepository;
        $this->userRepository = $userRepository;
        $this->mapRepository = $mapRepository;
        $this->questRepository = $questRepository;
        $this->upgradeRepository = $upgradeRepository;
        $this->commonRepository = $commonRepository;
        $this->scribeService = $scribeService;
        $this->redisService = $redisService;
    }

    public function getFishInventory(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $myFishInventory = new UserFishInventoryInfo();
        $myFishInventory->setUserCode($data->decoded->data->userCode);
        $myFishInventory->setFishInventoryCode($data->fishInventoryCode);

        //?????? ????????? ?????? ??????
        $fishInventory = $this->fishingRepository->getUserFishInventory($myFishInventory);
        $errorCode = new ErrorCode();
        $this->logger->info("get fish inventory info service");
        return [
            'fishInventoryInfo' => $fishInventory,
            'codeArray' =>  $errorCode->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function getFishInventoryList(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setItemCode($data->mapCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        //?????? ????????? ?????? ??????
        $fishInventoryArray = $this->fishingRepository->getUserFishInventoryList($search);
        $fishInventoryArrayCnt = $this->fishingRepository->getUserFishInventoryListCnt($search);
        $errorCode = new ErrorCode();
        $this->logger->info("get list fish inventory info service");
        return [
            'fishInventoryList' => $fishInventoryArray,
            'totalCount' => $fishInventoryArrayCnt,
            'codeArray' =>  $errorCode->getErrorArrayItem(ErrorCode::SUCCESS),
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
        $errorCode = new ErrorCode();
        //?????? ?????? +- 10??????
        $fishingItemDepth = $data->depth;
        //??????
        $choiceItem = new UserChoiceItemInfo();
        $choiceItem->setUserCode($data->decoded->data->userCode);
        $choiceItem->setChoiceCode($data->choiceCode);
        $choiceItem = $this->userRepository->getUserFishingItem($choiceItem);

        $myUserInventory = new UserInventoryInfo();
        $myUserInventory->setUserCode($data->decoded->data->userCode);
        $myUserInventory->setInventoryCode($choiceItem->getFishingRodCode());
        //???????????? ?????????
        $inventoryRod = $this->userRepository->getUserInventory($myUserInventory);
        //????????? ?????? ??????
        $itemRod = $this->fishingRepository->getFishingRodGradeData($inventoryRod);

        //???????????? ?????????
        $myUserInventory->setInventoryCode($choiceItem->getFishingLineCode());
        $inventoryLine = $this->userRepository->getUserInventory($myUserInventory);
        //????????? ?????? ??????
        $itemLine = $this->fishingRepository->getFishingLineGradeData($inventoryLine);

        //???????????? ??????
        $myUserInventory->setInventoryCode($choiceItem->getFishingNeedleCode());
        $inventoryNeedle = $this->userRepository->getUserInventory($myUserInventory);
        //?????? ?????? ??????
        $itemNeedle = $this->fishingRepository->getFishingNeedleGradeData($inventoryNeedle);

        //???????????? ??????
        $myUserInventory->setInventoryCode($choiceItem->getFishingBaitCode());
        $inventoryBait = $this->userRepository->getUserInventory($myUserInventory);
        //?????? ?????? ??????
        $itemBait = $this->fishingRepository->getFishingBaitGradeData($inventoryBait);

        //???????????? ???
        $myUserInventory->setInventoryCode($choiceItem->getFishingReelCode());
        $inventoryReel = $this->userRepository->getUserInventory($myUserInventory);
        //??? ?????? ??????
        $itemReel = $this->fishingRepository->getFishingReelGradeData($inventoryReel);

        if ($itemLine->getGradeCode() == 8){
            $itemReel->setReelNumber(5 * 25);
            $itemReel->setReelWindingAmount(floor($itemReel->getReelNumber()/10));
        }elseif ($itemLine->getGradeCode() == 9){
            $itemReel->setReelNumber(5 * 2 * 20);
            $itemReel->setReelWindingAmount(floor($itemReel->getReelNumber()/10));
        }else{
            $itemReel->setReelNumber(5 * 3 * 15);
            $itemReel->setReelWindingAmount(floor($itemReel->getReelNumber()/10));
        }

        //?????? ?????????1
        $myUserInventory->setInventoryCode($choiceItem->getFishingItemCode1());
        $inventoryItem1 = $this->userRepository->getUserInventory($myUserInventory);
        //?????? ?????????2
        $myUserInventory->setInventoryCode($choiceItem->getFishingItemCode2());
        $inventoryItem2 = $this->userRepository->getUserInventory($myUserInventory);
        //?????? ?????????3
        $myUserInventory->setInventoryCode($choiceItem->getFishingItemCode3());
        $inventoryItem3 = $this->userRepository->getUserInventory($myUserInventory);
        //?????? ?????????4
        $myUserInventory->setInventoryCode($choiceItem->getFishingItemCode4());
        $inventoryItem4 = $this->userRepository->getUserInventory($myUserInventory);

        $this->logger->info(" ".$itemReel->getReelWindingAmount()*10);
        //????????? ?????? ??? ?????? ?????? ??????
        if($itemReel->getReelWindingAmount()*10 >= $fishingItemDepth){
            //?????? ????????? ????????? or ????????? ??????
            if($inventoryRod->getItemDurability() >= $mapInfo->getPerDurability()
                && $inventoryLine->getItemDurability() >= $mapInfo->getPerDurability()
                && $inventoryNeedle->getItemCount() >= 1
                && $inventoryBait->getItemCode() >= 1
                && $inventoryReel->getItemDurability() >= $mapInfo->getPerDurability()){
                //?????? ??????
                $depth = $mapInfo->getMaxDepth() - $mapInfo->getMinDepth();

                //?????? ??????
                $weatherCode = new WeatherInfoData();
                $weatherCode->setWeatherCode($weatherHistoryInfo->getWeatherCode());
                $weatherInfo = $this->commonRepository->getWeatherInfo($weatherCode);

                //?????????, ?????????, ?????? ??????, ???????????? ??????

                //????????? ????????? ?????????
                $search = new SearchInfo();
                $search->setItemCode($data->mapCode);
                $search->setUserCode($data->decoded->data->userCode);
                $fishList = $this->mapRepository->getMapFishList($search);

                //????????? ?????? ????????? (????????? ???????????? 0???)
                $itemList = $this->mapRepository->getMapItemList($search);

                //??????????????? ??????
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
                    //?????????????????? ?????? ?????? ?????? ??????
                    $mapInfo->setMaxDepth($mapInfo->getMaxDepth()-$delDepth);

                    //?????? ?????? ?????????
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
                    //?????????????????? ?????? ?????? ?????? ??????
                    $mapInfo->setMaxDepth($mapInfo->getMaxDepth()-$delDepth);

                    //?????? ?????? ?????????
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
                        //?????????????????? ?????? ?????? ?????? ??????
                        $mapInfo->setMinDepth($mapInfo->getMaxDepth());
                        //?????? ?????? ?????????
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
                        //?????????????????? ?????? ?????? ?????? ??????
                        $mapInfo->setMaxDepth($mapInfo->getMinDepth());
                        //?????? ?????? ?????????
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
                        //?????????????????? ?????? ?????? ?????? ??????
                        $mapInfo->setMinDepth($mapInfo->getMinDepth()+$addDepth);

                        //?????? ?????? ?????????
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
                        //?????????????????? ?????? ?????? ?????? ??????
                        $mapInfo->setMinDepth($mapInfo->getMinDepth()+$addDepth);

                        //?????? ?????? ?????????
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

                //????????? ?????? ????????????
                $successArray = array(0,1); //0:??????, 1:??????
                if($itemRod->getItemType() == 2){
                    $itemRod->setHookingProbability($itemRod->getHookingProbability()*3);
                }
                $suppress = $itemRod->getSuppressProbability()+$itemLine->getSuppressProbability()+$itemNeedle->getSuppressProbability();
                $hooking = $itemRod->getHookingProbability()+$itemLine->getHookingProbability()+$itemNeedle->getHookingProbability();
                $probabilityArray = array(20+$suppress+$hooking, abs(100-(20+$suppress+$hooking)));
                $successYn = $this->Percent_draw($successArray, $probabilityArray);

                if($successYn == 0){
                    //???????????? ????????? ??????
                    $fishInventoryCnt = $this->fishingRepository->getUserFishInventoryListCnt($search);
                    $inventoryCnt = $this->userRepository->getUserInventoryListCnt($search);

                    //????????? ???????????? ?????? ???????????? ??????
                    if($userInfo->getUseInventoryCount() > ($inventoryCnt+$fishInventoryCnt)){
                        //?????? ????????? ??????
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
                        //?????? or ?????????
                        $code = $this->Percent_draw($fishCodeArray, $percentArray);
                        $message = null;
                        //???????????? ??????
                        if($code != 0){
                            //????????? ????????? ??????
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

                            //????????? ????????? ??????
                            $fishGradeInfo = new FishGradeData();
                            $fishGradeInfo->setFishCode($fishInfo->getFishCode());
                            $fishGradeInfo->setMinValue($this->Percent_draw($sizeArray, $percentArray)*$fishInfo->getFishProbability());
                            $fishGradeInfo = $this->commonRepository->getFishGradeData($fishGradeInfo);

                            $userFishInventoryInfo = new UserFishInventoryInfo();
                            $userFishInventoryInfo->setUserCode($data->decoded->data->userCode);
                            $userFishInventoryInfo->setMapCode($data->mapCode);
                            $userFishInventoryInfo->setFishGradeCode($fishGradeInfo->getFishGradeCode());
                            $this->fishingRepository->createUserFishInventory($userFishInventoryInfo);

                            //????????? ????????? ??????
                            $userInfo->setUserExperience($userInfo->getUserExperience()+$fishGradeInfo->getAddExperience());

                            $myLevelInfo = new UserLevelInfoData();
                            $myLevelInfo->setLevelCode($userInfo->getLevelCode());
                            $levelInfo = $this->userRepository->getUserLevelInfo($myLevelInfo);
                            //?????? ????????? ????????? ?????????
                            if ($userInfo->getUserExperience() >= $levelInfo->getLevelExperience()){
                                $userInfo->setUserExperience($userInfo->getUserExperience()-$levelInfo->getLevelExperience());
                                $userInfo->setLevelCode($levelInfo->getLevelCode()+1);

                                $myLevelInfo->setLevelCode($userInfo->getLevelCode());
                                $levelInfo = $this->userRepository->getUserLevelInfo($myLevelInfo);

                                $userInfo->setUseInventoryCount($levelInfo->getInventoryCount());
                                $userInfo->setFatigue($levelInfo->getMaxFatigue());

                                //?????? ????????? ?????? ??????
                                $myQuestInfo = new QuestInfoData();
                                $myQuestInfo->setQuestType(1);
                                $myQuestInfo->setQuestGoal($userInfo->getLevelCode());
                                $questInfo = $this->questRepository->getQuestInfoGoal($myQuestInfo);
                                //????????? ????????? ??????
                                $userQuestInfo = new UserQuestInfo();
                                $userQuestInfo->setUserCode($userInfo->getUserCode());
                                $userQuestInfo->setQuestCode($questInfo->getQuestCode());
                                $userQuestCnt = $this->userRepository->getUserQuestInfoCnt($userQuestInfo);

                                if($userQuestCnt == 0){
                                    //?????????(?????????) ??????
                                    $boxInfo = new UserGitfBoxInfo();
                                    $boxInfo->setUserCode($userInfo->getUserCode());
                                    $boxInfo->setQuestType(1);
                                    $boxInfo->setQuestGoal($userInfo->getLevelCode());
                                    $this->userRepository->createUserGiftBox($boxInfo);

                                    //????????? ????????? ??????
                                    $this->userRepository->createUserQuestInfo($userQuestInfo);
                                }
                                $message = "?????? ".$userInfo->getLevelCode()."??? ?????????????????????!";
                            }
                            $itemInfo = $fishGradeInfo;
                            $logItemCode = $fishGradeInfo->getFishGradeCode();
                            $logItemType = 8;
                        }else{ //????????? ??????
                            $itemArray = array();
                            $percentArray = array();

                            for ($i=0; $i<count($itemList); $i++){
                                $itemArray[] = $itemList[$i]['mapItemCode'];
                                $percentArray[] = $itemList[$i]['itemProbability'];
                            }

                            $myMapItem = new MapItemData();
                            $myMapItem->setMapItemCode($this->Percent_draw($itemArray, $percentArray));
                            $mapItem = $this->mapRepository->getMapItemInfo($myMapItem);
                            //????????? ??????????????? ?????? ????????? ??????????????? ??????
                            $myInventory = new UserInventoryInfo();
                            $myInventory->setUserCode($data->decoded->data->userCode);
                            if($mapItem->getItemType()==1 || $mapItem->getItemType()==2 || $mapItem->getItemType()==5){
                                //??????????????? ????????? ?????? ??????
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
                                //???????????? ??????
                                $userInventoryCode = $this->userRepository->createUserInventoryInfo($myInventory);
                            }else{
                                //???????????? ?????? ??????, ???????????? ?????? return
                                $search->setItemCode($mapItem->getItemCode());
                                $search->setItemType($mapItem->getItemType());
                                $inventoryCode = $this->userRepository->getUserInventoryCode($search);

                                if ($inventoryCode != 0){
                                    //??????????????? ?????? ????????? ??????
                                    $myInventory->setInventoryCode($inventoryCode);
                                    $inventoryInfo = $this->userRepository->getUserInventory($myInventory);
                                    //???????????? ?????? (????????? ??????)
                                    $inventoryInfo->setItemCount($inventoryInfo->getItemCount()+3);
                                    $userInventoryCode = $this->userRepository->createUserInventoryInfo($inventoryInfo);
                                }else{
                                    $myInventory->setItemCode($mapItem->getItemCode());
                                    $myInventory->setItemType($mapItem->getItemType());
                                    $myInventory->setUpgradeCode(0);
                                    $myInventory->setUpgradeLevel(0);
                                    $myInventory->setItemCount(3);
                                    $myInventory->setItemDurability(1);
                                    //???????????? ??????
                                    $userInventoryCode = $this->userRepository->createUserInventoryInfo($myInventory);
                                }
                            }
                            $myInventory->setInventoryCode($userInventoryCode);
                            $itemInfo = $this->userRepository->getUserInventory($myInventory);
                            $logItemCode = $itemInfo->getItemCode();
                            $logItemType = $itemInfo->getItemType();
                        }

                        $this->userRepository->modifyUserLevel($userInfo);
                        if (self::isRedisEnabled() === true) {
                            $user = $this->userRepository->getUserInfo($userInfo);
                            $this->saveInCache($userInfo->getUserCode(), $user, self::USER_REDIS_KEY);
                        }
                        //scribe ?????? ?????????
                        date_default_timezone_set('Asia/Seoul');
                        $currentTime = date("Y-m-d H:i:s");

                        //?????? ?????? ?????? ?????????
                        $dataJson = json_encode([
                            "date" => $currentTime,
                            "dateTime" => $currentTime,
                            "channel_uid" => "0",
                            "game" => ScribeService::PROJECT_NAME,
                            "server_id" => 'KR',
                            "account_id" => $data->decoded->data->accountCode,
                            "account_level" => 0,
                            "character_id" => $userInfo->getUserCode(),
                            "character_type_id" => 0,
                            "character_level" => $userInfo->getLevelCode(),
                            "character_catch_id" => $logItemCode,
                            "character_catch_type" => $logItemType,
                            "app_id" => ScribeService::PROJECT_NAME,
                            "client_ip" => $_SERVER['REMOTE_ADDR'],
                            "server_ip" => $_SERVER['SERVER_ADDR'],
                            "channel" => "C2S",
                            "company" => "C2S",
                            "guid" => $_SERVER['GUID']
                        ]);

                        $msg1[] = new \LogEntry(array(
                            'category' => 'uruk_game_character_fishing_log',
                            'message' => $dataJson
                        ));
                        $this->scribeService->Log($msg1);

                        $this->logger->info("fishing operate service");
                        if(!empty($message)){
                            return [
                                'itemInfo' => $itemInfo,
                                'codeArray' =>  $errorCode->getErrorArrayItem(ErrorCode::LEVEL_SUCCESS),
                            ];
                        }else{
                            return [
                                'itemInfo' => $itemInfo,
                                'codeArray' =>  $errorCode->getErrorArrayItem(ErrorCode::SUCCESS),
                            ];
                        }
                    }else{
                        $this->logger->info("full fish inventory service");
                        return [
                            'itemInfo' => null,
                            'codeArray' =>  $errorCode->getErrorArrayItem(ErrorCode::FULL_DATA),
                        ];
                    }
                }else{

                    //?????? ????????? ??????
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
                        'itemInfo' => null,
                        'codeArray' =>  $errorCode->getErrorArrayItem(ErrorCode::MISS_FISH),
                    ];
                }
            }else{
                $this->logger->info("check fishing item service");
                return [
                    'itemInfo' => null,
                    'codeArray' =>  $errorCode->getErrorArrayItem(ErrorCode::NOT_FULL_FISHING_ITEM),
                ];
            }
        }else{
            $this->logger->info("check fishing item depth service");
            return [
                'itemInfo' => null,
                'codeArray' =>  $errorCode->getErrorArrayItem(ErrorCode::NO_FISH_DEPTH),
            ];
        }
    }

    public function deleteFishInventory(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $userFishInventory = new UserFishInventoryInfo();
        $userFishInventory->setUserCode($data->decoded->data->userCode);
        $userFishInventory->setFishInventoryCode($data->fishInventoryCode);
        //?????? ????????? ??????
        $result = $this->fishingRepository->deleteUserFishInventory($userFishInventory);
        $errorCode = new ErrorCode();
        if($result>0){
            return [
                'codeArray' =>  $errorCode->getErrorArrayItem(ErrorCode::SUCCESS),
            ];
        }else{
            return [
                'codeArray' =>  $errorCode->getErrorArrayItem(ErrorCode::FAIL_FUNCTION),
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

    // ????????? ?????? ????????? ?????? ??????
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