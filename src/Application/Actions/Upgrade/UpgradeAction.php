<?php

namespace App\Application\Actions\Upgrade;

use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;

abstract class UpgradeAction extends Action
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }
}