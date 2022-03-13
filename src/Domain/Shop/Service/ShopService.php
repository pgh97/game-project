<?php

namespace App\Domain\Shop\Service;

use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Shop\Repository\ShopRepository;
use Psr\Log\LoggerInterface;

class ShopService extends BaseService
{
    protected ShopRepository $shopRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const SHOP_REDIS_KEY = 'shop:%s';

    public function __construct(LoggerInterface $logger
        ,ShopRepository $shopRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->shopRepository = $shopRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}