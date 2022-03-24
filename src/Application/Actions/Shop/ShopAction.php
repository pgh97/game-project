<?php

namespace App\Application\Actions\Shop;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Common\Service\ScribeService;
use App\Domain\Shop\Repository\ShopRepository;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

abstract class ShopAction extends Action
{
    protected ShopRepository $shopRepository;
    protected UserRepository $userRepository;
    protected CommonRepository $commonRepository;
    protected ScribeService $scribeService;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        ,ShopRepository $shopRepository
        ,UserRepository $userRepository
        ,CommonRepository $commonRepository
        ,ScribeService $scribeService
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->shopRepository = $shopRepository;
        $this->userRepository = $userRepository;
        $this->commonRepository = $commonRepository;
        $this->scribeService = $scribeService;
        $this->redisService = $redisService;
    }
}