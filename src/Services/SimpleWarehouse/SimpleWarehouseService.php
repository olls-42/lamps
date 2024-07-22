<?php

namespace App\Services\SimpleWarehouse;

use App\Entity\InventoryItem;
use App\EntityManager\EntityManagerInterface;
use App\EntityManager\PersistenceBackend\ConfigurableIndex;
use App\Services\SimpleWarehouse\Observer\InventoryItemQohCapacityObserver;
use App\Services\SimpleWarehouse\Observer\InventoryItemUpdateObserver;
use App\ValueObject\CostValueObject;
use App\ValueObject\QohValueObject;
use App\ValueObject\SalePriceValueObject;
use App\ValueObject\SkuValueObject;
use Psr\Log\LoggerInterface;

readonly class SimpleWarehouseService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface        $logger,
    )
    {
        $this->entityManager
            ->getPersistenceBackend()
            ->setConfiguration(new ConfigurableIndex([
                InventoryItem::class => 'sku',
                // SimpleProductEntity::class => 'foo'
            ]));

        $this->entityManager
            ->getObservatory()
            ->setupObservers([
                InventoryItemQohCapacityObserver::class,
                InventoryItemUpdateObserver::class
            ]);
    }

    public function sandbox(): void
    {
        $this->logger->debug('Hello logger');

        $this->entityManager->startSession();

        $item1 = new InventoryItem(
            new SkuValueObject('abc-555'),
            new QohValueObject(26),
            new CostValueObject(567),
            new SalePriceValueObject(727)
        );

        $this->entityManager->persist($item1);

        $item1->itemsReceived(4);
        $item1->itemsHaveShipped(5);


        /** @var $maybeExists InventoryItem */
        $maybeExists = $this->entityManager->find(InventoryItem::class,
            new SkuValueObject('abc-555'));

        if ($maybeExists) {
            $maybeExists->itemsReceived(40);
        }

        /** @var $item2 InventoryItem */
        $item2 = new InventoryItem(
                new SkuValueObject('hjg-3821'),
                new QohValueObject(5),
                new CostValueObject(789),
                new SalePriceValueObject(1200)
            );

        $this->entityManager->persist($item2);

        $item2->itemsReceived(2);
        $item2->itemsHaveShipped(5);


        // 500 kb md5 index ~ about 10k dummy classes
        //for ($i = 0; $i < 3; $i++) {
        //
        //    $item3 = new InventoryItem(
        //        new SkuValueObject('xrf-3827' . $i),
        //        new QohValueObject(50),
        //        new CostValueObject(564),
        //        new SalePriceValueObject(1540)
        //    );
        //
        //    $item3->itemsReceived(12);
        //    $item3->itemsHaveShipped(5);
        //    $item3->changeSalePrice(22);
        //
        //    //$this->entityManager->persist($item3);
        //}

        $this->entityManager->commitSession();
    }

}
