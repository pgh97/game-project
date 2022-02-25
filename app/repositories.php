<?php
declare(strict_types=1);

use App\Domain\Auth\Repository\AccountInfoRepository;
use App\Domain\User\Repository\UserInfoRepository;
use App\Infrastructure\Persistence\Auth\AccountInfoDBRepository;
use App\Infrastructure\Persistence\User\UserInfoDBRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        AccountInfoRepository::class => \DI\autowire(AccountInfoDBRepository::class),
        UserInfoRepository::class => \DI\autowire(UserInfoDBRepository::class),
    ]);
};
