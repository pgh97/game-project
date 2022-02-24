<?php
declare(strict_types=1);

use App\Domain\Auth\Repository\AccountInfoRepository;
use App\Infrastructure\Persistence\Auth\AccountInfoDBRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        AccountInfoRepository::class => \DI\autowire(AccountInfoDBRepository::class),
    ]);
};
