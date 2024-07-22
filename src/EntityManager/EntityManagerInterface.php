<?php

namespace App\EntityManager;

use App\EntityManager\Entity\EntityInterface;
use App\EntityManager\Observer\ObjectsObservatoryInterface;
use App\EntityManager\PersistenceBackend\PersistenceBackendInterface;
use App\ValueObject\ValueObjectInterface;

/**
 *
 */
interface EntityManagerInterface
{
    /**
     *
     * @return void
     */
    public function startSession(): void;

    /**
     *
     * @return void
     */
    public function commitSession(): void;

    /**
     *
     * @param string $tablespace
     * @param ValueObjectInterface $voId
     * @return EntityInterface|null
     */
    public function find(string $tablespace, ValueObjectInterface $voId): ?EntityInterface;

    /**
     *
     * @param EntityInterface $entity
     * @return void
     */
    public function persist(EntityInterface $entity): void;

    /**
     *
     * @param EntityInterface $entity
     * @return void
     */
    public function forget(EntityInterface $entity): void;

    /**
     * @return ObjectsObservatoryInterface
     */
    public function getObservatory(): ObjectsObservatoryInterface;

    /**
     * @return PersistenceBackendInterface
     */
    public function getPersistenceBackend(): PersistenceBackendInterface;
}
