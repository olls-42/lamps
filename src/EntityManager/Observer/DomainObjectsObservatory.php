<?php

namespace App\EntityManager\Observer;

use App\EntityManager\Entity\EntityInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use SplSubject;

/**
 *
 */
class DomainObjectsObservatory
    implements ObjectsObservatoryInterface, SimpleObserverInterface
{

    private \SplObjectStorage $stateStorage;
    private array $mirroredObservers = [];

    public function __construct(
        private readonly LoggerInterface    $logger,
        private readonly ContainerInterface $locator,
    ){
        $this->stateStorage = new \SplObjectStorage();
    }

    /**
     * @useless
     */
    public function info(): void
    {
        $this->logger->debug('Observatory info');
    }

    /**
     * @inheritdoc
     */
    public function detach(ObservableEntityInterface|EntityInterface $entity): void
    {
        $this->stateStorage->detach($entity);
    }

    /**
     * @inheritdoc
     *
     * Let's route all subject to one point
     *
     * @param SimpleSubjectInterface|SplSubject $subject
     * @return void
     */
    public function update(SimpleSubjectInterface|SplSubject $subject): void
    {
        $this->logger->info(static::class . 'update event...');

        // ...
    }

    /**
     * @inheritdoc
     */
    public function attach(ObservableEntityInterface $entity): void
    {
        $entityUpdateSubject = $entity->getEntityLifeCycleSubject();

        if (!$this->stateStorage->contains($entityUpdateSubject))
        {
            // attach $this observatory first
            $entityUpdateSubject->attach($this);

            // then custom observers
            foreach ($this->mirroredObservers as $observer) {
                 $entityUpdateSubject->attach($observer);
            }

            // then attach to observer internal state
            $this->stateStorage->attach($entityUpdateSubject);
        }
    }

    /**
     * @inheritdoc
     * @throws
     */
    public function setupObservers(array $observers): void
    {
        foreach ($observers as $observer) {
            $this->mirroredObservers[] = $this->locator->get($observer);;
        }
    }
}
