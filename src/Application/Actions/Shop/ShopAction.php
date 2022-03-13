<?php

namespace App\Application\Actions\Shop;

use App\Application\Actions\Action;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Shop\Repository\ShopRepository;
use Psr\Log\LoggerInterface;

abstract class ShopAction extends Action
{
    protected ShopRepository $shopRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        ,ShopRepository $shopRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->shopRepository = $shopRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}