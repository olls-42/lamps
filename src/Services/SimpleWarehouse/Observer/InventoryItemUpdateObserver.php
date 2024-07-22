<?php

namespace App\Services\SimpleWarehouse\Observer;

use App\EntityManager\Observer\SimpleSubjectInterface;
use App\EntityManager\Observer\SimpleObserverInterface;
use Psr\Log\LoggerInterface;
use SplSubject;
use Symfony\Contracts\Service\Attribute\Required;

class InventoryItemUpdateObserver implements SimpleObserverInterface
{
    private string $description = '';
    private ?LoggerInterface $logger;

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    #[Required]
    public function withLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function update(SimpleSubjectInterface|SplSubject $subject): void
    {
        $this->logger->info('InventoryItem Update Observer, ' . $this->description);

        $inventoryItem = $subject->getObservableEntity();

        $output = "qoh: {$inventoryItem->getQoh()->value()}, ";
        $output .= "sku: {$inventoryItem->getSku()->value()} " . PHP_EOL;

        $this->logger->debug($inventoryItem . ' the entity has been updated with new values:');
        $this->logger->debug($output);

        file_put_contents('var/log/item-updates.log', $output, FILE_APPEND);
    }
}
