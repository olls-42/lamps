<?php

namespace App\Commands;

use App\Services\SimpleWarehouse\SimpleWarehouseService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:lamps')]
class DefaultCommand extends Command
{

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SimpleWarehouseService $simpleService,
    )
    {
        parent::__construct('lamp:init');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->debug("Console command execute has called.");

        $this->simpleService->sandbox();

        return Command::SUCCESS;
    }
}
