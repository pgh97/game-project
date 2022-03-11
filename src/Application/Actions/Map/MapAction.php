<?php

namespace App\Application\Actions\Map;

use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;

abstract class MapAction extends Action
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }
}