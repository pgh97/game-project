<?php

namespace App\Application\Actions\Auth;

use App\Application\Actions\Action;
use App\Domain\Auth\Repository\AccountInfoRepository;
use App\Domain\Auth\Service\AccountInfoService;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class AuthAction extends Action
{
    protected AccountInfoRepository $accountInfoRepository;
    /*public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        parent::__construct($container, $logger);
    }*/

    public function __construct(LoggerInterface $logger, AccountInfoRepository $accountInfoRepository)
    {
        parent::__construct($logger);
        $this->accountInfoRepository = $accountInfoRepository;
    }

//    protected function getCreateAccountInfoService(): AccountInfoService
//    {
//        return $this->container->get('create_accountInfo_service');
//    }
}