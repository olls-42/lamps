<?php

namespace App\Tests\App\Tests\EntityManager\Persistence;

use App\EntityManager\Entity\AbstractEntity;
use App\EntityManager\PersistenceBackend\PersistenceBackendInterface;
use App\ValueObject\ValueObjectInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PersistenceBackendTest extends KernelTestCase
{
    private ?PersistenceBackendInterface $persistenceBackend;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->persistenceBackend = $kernel->getContainer()
            ->get('persistence.persistence_backend');

        parent::setUp();
    }

    public function test_sleep(): void {

    }

    public function test_wakeup(array $indexedEntities): void{}

    // namespace

    public function test_useNamespace(AbstractEntity $simpleEntity)
    {}

    public function test_setNamespace(string $namespace): void
    {}

    public function test_getNamespace(): string
    {}

    // simple crud

    public function test_insert(ValueObjectInterface $primaryKey, string $data): int
    {}

    public function test_select(ValueObjectInterface $primaryKey): ?string
    {}

    public function test_update(ValueObjectInterface $primaryKey, string $data): int
    {}

    public function test_delete(ValueObjectInterface $primaryKey): int
    {}


}
