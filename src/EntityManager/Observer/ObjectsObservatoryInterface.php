<?php

namespace App\EntityManager\Observer;


/**
 *
 */
interface ObjectsObservatoryInterface
{
    /**
     * @param array $observers
     * @return void
     */
    public function setupObservers(array $observers): void;

    /**
     * @param ObservableEntityInterface $entity
     * @return void
     */
    public function attach(ObservableEntityInterface $entity): void;

    /**
     * @param ObservableEntityInterface $entity
     * @return void
     */
    public function detach(ObservableEntityInterface $entity): void;
}
