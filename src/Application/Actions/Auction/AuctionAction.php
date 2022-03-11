<?php

namespace App\Application\Actions\Auction;

use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;

abstract class AuctionAction extends Action
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }
}