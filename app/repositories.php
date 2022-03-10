<?php
declare(strict_types=1);

use App\Domain\Auth\Repository\AccountInfoRepository;
use App\Domain\User\Repository\UserRepository;
use App\Domain\Map\Repository\MapRepository;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\Auction\Repository\AuctionRepository;
use App\Domain\Upgrade\Repository\UpgradeRepository;
use App\Domain\Repair\Repository\RepairRepository;
use App\Domain\Quest\Repository\QuestRepository;
use App\Domain\Shop\Repository\ShopRepository;

use App\Infrastructure\Persistence\Auth\AccountInfoDBRepository;
use App\Infrastructure\Persistence\User\UserDBRepository;
use App\Infrastructure\Persistence\Map\MapDBRepository;
use App\Infrastructure\Persistence\Fishing\FishingDBRepository;
use App\Infrastructure\Persistence\Auction\AuctionDBRepository;
use App\Infrastructure\Persistence\Upgrade\UpgradeDBRepository;
use App\Infrastructure\Persistence\Repair\RepairDBRepository;
use App\Infrastructure\Persistence\Quest\QuestDBRepository;
use App\Infrastructure\Persistence\Shop\ShopDBRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        AccountInfoRepository::class => \DI\autowire(AccountInfoDBRepository::class),
        UserRepository::class => \DI\autowire(UserDBRepository::class),
        MapRepository::class => \DI\autowire(MapDBRepository::class),
        FishingRepository::class => \DI\autowire(FishingDBRepository::class),
        AuctionRepository::class => \DI\autowire(AuctionDBRepository::class),
        UpgradeRepository::class => \DI\autowire(UpgradeDBRepository::class),
        RepairRepository::class => \DI\autowire(RepairDBRepository::class),
        QuestRepository::class => \DI\autowire(QuestDBRepository::class),
        ShopRepository::class => \DI\autowire(ShopDBRepository::class),
    ]);
};
