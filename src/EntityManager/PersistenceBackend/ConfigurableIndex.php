<?php

namespace App\EntityManager\PersistenceBackend;

/**
 */
class ConfigurableIndex implements ConfigurableIndexInterface
{

    public function __construct(
        private array $config = []
    ){
    }

    public function setConfiguration(array $configuration): void
    {
        $this->config = $configuration;
    }

    public function getConfiguration(): array
    {
        return $this->config;
    }
}
