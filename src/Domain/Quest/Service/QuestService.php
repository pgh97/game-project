<?php

namespace App\Domain\Quest\Service;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Quest\Entity\QuestInfoData;
use App\Domain\Quest\Repository\QuestRepository;
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

    public function getQuestInfo(array $input):QuestInfoData
    {
        $data = json_decode((string) json_encode($input), false);
        $myQuestInfo = new QuestInfoData();
        //$myQuestInfo->setUserCode($data->decoded->data->userCode);
        $myQuestInfo->setQuestCode($data->questCode);

        $questInfo = $this->questRepository->getQuestInfo($myQuestInfo);
        $this->logger->info("get quest info service");
        return $questInfo;
    }

    public function getQuestInfoList(array $input): array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        $questArray = $this->questRepository->getQuestInfoList($search);
        $questArrayCnt = $this->questRepository->getQuestInfoListCnt($search);
        $this->logger->info("get list quest info service");
        return [
            'questList' => $questArray,
            'totalCount' => $questArrayCnt,
        ];
    }
}