<?php

namespace App\Application\Actions\Fishing;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Common\Service\ScribeService;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\Map\Repository\MapRepository;
use App\Domain\Quest\Repository\QuestRepository;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

abstract class FishingAction extends Action
{
    protected FishingRepository $fishingRepository;
    protected UserRepository $userRepository;
    protected MapRepository $mapRepository;
    protected QuestRepository $questRepository;
    protected UpgradeRepository $upgradeRepository;
    protected CommonRepository $commonRepository;
    protected ScribeService $scribeService;
    protected RedisService $redisService;

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
        parent::__construct($logger);
        $this->fishingRepository = $fishingRepository;
        $this->userRepository = $userRepository;
        $this->mapRepository = $mapRepository;
        $this->questRepository = $questRepository;
        $this->upgradeRepository = $upgradeRepository;
        $this->commonRepository = $commonRepository;
        $this->scribeService = $scribeService;
        $this->redisService = $redisService;
    }
}