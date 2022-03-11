<?php

namespace App\Application\Actions\Repair;

use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;

abstract class RepairAction extends Action
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }
}