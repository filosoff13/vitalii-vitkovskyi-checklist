<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleExceptionListener
{
    public function onConsoleException(ConsoleErrorEvent $event): void
    {
        $io = new SymfonyStyle($event->getInput(), $event->getOutput());
        $io->error($event->getError()->getMessage());
    }
}
