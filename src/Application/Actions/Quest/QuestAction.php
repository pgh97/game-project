<?php

namespace App\Application\Actions\Quest;

use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;

abstract class QuestAction extends Action
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }
}