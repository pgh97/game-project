<?php

namespace App\Application\Actions\Fishing;

use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;

abstract class FishingAction extends Action
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }
}