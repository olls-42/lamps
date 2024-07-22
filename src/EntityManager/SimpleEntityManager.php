<?php

namespace App\EntityManager;


use App\EntityManager\Context\PersistenceContextInterface;
use App\EntityManager\Entity\EntityInterface;
use App\EntityManager\Entity\AbstractEntity;
use App\EntityManager\Exception\ItemNotFoundException;
use App\EntityManager\Memento\SimpleMementoInterface;
use App\EntityManager\Observer\ObjectsObservatoryInterface;
use App\EntityManager\PersistenceBackend\PersistenceBackendInterface;
use App\ValueObject\ValueObjectInterface;
use Psr\Log\LoggerInterface;


/**
 * @inheritdoc
 */
readonly class SimpleEntityManager
    implements EntityManagerInterface
{
    public function __construct(
        private LoggerInterface             $logger,
        private PersistenceContextInterface $persistenceContext,
        private PersistenceBackendInterface $persistenceBackend,
        private ObjectsObservatoryInterface $objectsObservatory,
    )
    {
        $this->logger->debug('Entity Manager online!');
    }

    /**
     * @inheritdoc
     *
     * @param string $tablespace
     * @param ValueObjectInterface $voId
     * @return AbstractEntity|null
     */
    public function find(string $tablespace, ValueObjectInterface $voId): ?AbstractEntity
    {
        if ($knownObject = $this->persistenceBackend->get($voId)) {
            $this->persistenceContext->attach($knownObject);
            $this->objectsObservatory->attach($knownObject);

            return $knownObject;
        }

        return null;
    }

    public function contains(AbstractEntity $object): bool
    {
        return $this->persistenceContext->contains($object);
    }

    public function detach(AbstractEntity $object): void
    {
        $this->objectsObservatory->detach($object);
        $this->persistenceContext->detach($object);
    }

    public function persist(AbstractEntity|EntityInterface $entity): void
    {
        if ($this->persistenceContext->contains($entity)) return;

        // if we has already used this sku (primary-key) before
        /** @var AbstractEntity|SimpleMementoInterface $knownObject */
        if ($knownObject = $this->persistenceBackend->get($entity->getPrimaryKey())) {
            $entity->merge($knownObject);
        }

        // make as series of notification to observers
        if ($count = $entity->getSnapshots()->count()) {
            foreach ($entity->getSnapshots() as $snapshot) {
                $this->objectsObservatory->attach($snapshot);
            }
        }

        $this->objectsObservatory->attach($entity);

        // make entity indexable and persistent asap
        if ($this->persistenceBackend->put($entity)) {
            $this->persistenceContext->attach($entity, 'new perishable object');
        }
    }

    public function forget(EntityInterface $entity): void
    {
        if (!$this->persistenceContext->contains($entity))
            throw new ItemNotFoundException($entity);

        if ($done = $this->persistenceBackend->unlink($entity)){
            $this->objectsObservatory->detach($entity);
            $this->persistenceContext->detach($entity);
       }
    }

    public function startSession(): void
    {
        $this->persistenceContext->startSession();
    }

    public function commitSession(): void
    {
        $this->persistenceContext->commitSession();
    }

    public function getObservatory(): ObjectsObservatoryInterface
    {
        return $this->objectsObservatory;
    }

    public function getPersistenceBackend(): PersistenceBackendInterface
    {
        return $this->persistenceBackend;
    }
}
