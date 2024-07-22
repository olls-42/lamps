<?php

namespace App\EntityManager\Context;


use App\EntityManager\Entity\EntityInterface;

/**
 */
interface PersistenceContextInterface
{

    /**
     *
     * @return \SplObjectStorage
     */
    public function getContext(): \SplObjectStorage;

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
     * @param EntityInterface $entity
     * @param string $info
     * @return void
     */
    public function attach(EntityInterface $entity, string $info): void;

    /**
     *  todo docs, examples
     *
     * @param EntityInterface $entity
     * @return void
     */
    public function detach(EntityInterface $entity): void;

    /**
     *  todo docs, examples
     *
     * @param EntityInterface $entity
     * @return bool
     */
    public function contains(EntityInterface $entity): bool;

    /**
     *  todo docs, examples
     *
     * @return int
     */
    public function count(): int;
}
