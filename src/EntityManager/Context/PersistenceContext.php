<?php

namespace App\EntityManager\Context;

use App\EntityManager\Entity\EntityInterface;

/**
 * @inheritdoc
 */
class PersistenceContext
    implements PersistenceContextInterface
{
    private \SplObjectStorage $context;

    public function __construct(
    )
    {
        $this->context = new \SplObjectStorage();
    }

    /**
     * @inheritdoc
     */
    public function contains(EntityInterface $entity): bool
    {
        return $this->context->contains($entity);
    }

    /**
     * @inheritdoc
     */
    public function getContext(): \SplObjectStorage
    {
        return $this->context;
    }

    /**
     * @inheritdoc
     */
    public function attach(EntityInterface $entity, string $info = ''): void
    {
        $this->context->attach($entity, $info);
    }

    /**
     * @inheritdoc
     */
    public function detach(EntityInterface $entity): void
    {
        $this->context->detach($entity);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        $this->context->count();
    }

    /**
     * @inheritdoc
     */
    public function startSession(): void
    {
        $this->context = new \SplObjectStorage();
    }

    /**
     * @inheritdoc
     */
    public function commitSession(): void
    {
        $this->context = new \SplObjectStorage();
    }
}
