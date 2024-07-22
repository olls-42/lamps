<?php

namespace App\Entity;


use App\EntityManager\Entity\AbstractEntity;
use App\EntityManager\Observer\ObjectLifeCycleSubject;
use App\Services\SimpleWarehouse\Observer\InventoryItemQohCapacityObserver;
use App\Services\SimpleWarehouse\Observer\InventoryItemUpdateObserver;
use App\ValueObject\CostValueObject;
use App\ValueObject\QohValueObject;
use App\ValueObject\SalePriceValueObject;
use App\ValueObject\SkuValueObject;

class InventoryItem extends AbstractEntity
{
    public function __construct(
        protected SkuValueObject       $sku,
        protected QohValueObject       $qoh,
        protected CostValueObject      $cost,
        protected SalePriceValueObject $salePrice,
    )
    {
        parent::__construct();
    }

    public function getQoh(): QohValueObject
    {
        return $this->qoh;
    }

    public function getSku(): SkuValueObject
    {
        return $this->sku;
    }

    // Update the number of items, because we have shipped some.
    public function itemsHaveShipped(int $items): static
    {
        $this->update($this->qoh, $this->qoh->value() - $items, 'some message for this update');

        return $this;
    }

    // We received new items, update the count.
    public function itemsReceived(int $items): static
    {
        $this->update($this->qoh, $this->qoh->value() + $items, 'another note for this update');

        return $this;
    }

    public function changeSalePrice(int $cents): static
    {
        $this->update($this->salePrice, $cents);

        return $this;
    }

    /**
     * @deprecated
     * @inheritdoc
     */
    public function getObserversConfiguration(): array
    {
        return [
            ObjectLifeCycleSubject::class => [
                InventoryItemUpdateObserver::class,
                InventoryItemQohCapacityObserver::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function setupPrimaryKeyProperty(): void
    {
        $this->primaryKeyProperty = 'sku';
    }
}
