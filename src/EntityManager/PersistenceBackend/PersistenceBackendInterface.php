<?php

namespace App\EntityManager\PersistenceBackend;

use App\EntityManager\Entity\EntityInterface;
use App\ValueObject\ValueObjectInterface;

interface PersistenceBackendInterface
{
    public function getIndexSize(string $indexName);
    public function put(EntityInterface $baseEntity): bool|int;
    public function get(ValueObjectInterface $primaryKey): ?EntityInterface;
    public function setConfiguration(ConfigurableIndex $indexConfig): void;
    public function unlink(EntityInterface $entity): bool;
}
