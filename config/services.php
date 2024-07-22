<?php

// config/services.php
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\EntityManager\Context\PersistenceContextInterface;
use App\EntityManager\Context\PersistenceContext;
use App\EntityManager\EntityManagerInterface;
use App\EntityManager\Observer\DomainObjectsObservatory;
use App\EntityManager\Observer\ObjectsObservatoryInterface;
use App\EntityManager\PersistenceBackend\FileSystemPersistenceBackend;
use App\EntityManager\PersistenceBackend\PersistenceBackendInterface;
use App\EntityManager\SimpleEntityManager;
use App\Services\SimpleWarehouse\Observer\InventoryItemQohCapacityObserver;
use App\Services\SimpleWarehouse\Observer\InventoryItemUpdateObserver;

return function (ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
        ->autowire()      // Automatically injects dependencies in your services.
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    // makes classes in src/ available to be used as services
    // this creates a service per class whose id is the fully-qualified class name
    $services->load('App\\', '../src/')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}');

    // order is important in this file because service definitions
    // always *replace* previous ones; add your own service configuration below

    $services->set(PersistenceBackendInterface::class, FileSystemPersistenceBackend::class);
    $services->set(PersistenceContextInterface::class, PersistenceContext::class);

    $services->set(ObjectsObservatoryInterface::class, DomainObjectsObservatory::class)
        ->arg('$logger', service('logger'))
        ->arg('$locator', service_locator([
            InventoryItemQohCapacityObserver::class => service(InventoryItemQohCapacityObserver::class),
            InventoryItemUpdateObserver::class => service(InventoryItemUpdateObserver::class)
        ]));

    $services->set(EntityManagerInterface::class, SimpleEntityManager::class);

};
