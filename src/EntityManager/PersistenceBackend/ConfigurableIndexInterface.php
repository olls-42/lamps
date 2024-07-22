<?php

namespace App\EntityManager\PersistenceBackend;


/**
 * Sharded config
 */
interface ConfigurableIndexInterface
{
    public function setConfiguration(array $configuration): void;
    public function getConfiguration(): array;
}
