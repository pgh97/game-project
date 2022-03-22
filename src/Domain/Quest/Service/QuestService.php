<?php

namespace App\Domain\Quest\Service;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Quest\Entity\QuestInfoData;
use App\Domain\Quest\Repository\QuestRepository;
use App\Exception\ErrorCode;
use Psr\Log\LoggerInterface;

class QuestService extends BaseService
{
    protected QuestRepository $questRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const QUEST_REDIS_KEY = 'quest:%s';

    public function __construct(LoggerInterface $logger
        ,QuestRepository $questRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->questRepository = $questRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }

    public function getQuestInfo(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $myQuestInfo = new QuestInfoData();
        $myQuestInfo->setQuestCode($data->questCode);
        //퀘스트 상세 조회
        if(!empty($data->decoded->data->userCode)){
            $myQuestInfo->setUserCode($data->decoded->data->userCode);
            $questInfo = $this->questRepository->getUserQuestInfo($myQuestInfo);
        }else{
            $questInfo = $this->questRepository->getQuestInfo($myQuestInfo);
        }

        $code = new ErrorCode();
        $myQuestInfo->setQuestCode($data->questCode);
        $this->logger->info("get quest info service");
        return [
            'questInfo' => $questInfo,
            'codeArray' => $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }

    public function getQuestInfoList(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        //퀘스트 목록 조회
        if(!empty($data->decoded->data->userCode)){
            $search->setUserCode($data->decoded->data->userCode);
            $questArray = $this->questRepository->getUserQuestInfoList($search);
            $questArrayCnt = $this->questRepository->getUserQuestInfoListCnt($search);
        }else{
            $questArray = $this->questRepository->getQuestInfoList($search);
            $questArrayCnt = $this->questRepository->getQuestInfoListCnt($search);
        }
        $code = new ErrorCode();
        $this->logger->info("get list quest info service");
        return [
            'questList' => $questArray,
            'totalCount' => $questArrayCnt,
            'codeArray' => $code->getErrorArrayItem(ErrorCode::SUCCESS),
        ];
    }
}