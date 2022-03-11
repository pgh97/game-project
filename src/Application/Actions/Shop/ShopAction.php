<?php

namespace App\Application\Actions\Shop;

use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;

abstract class ShopAction extends Action
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }
}