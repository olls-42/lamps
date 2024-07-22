<?php

namespace App\EntityManager\Observer;


/**
 *
 */
interface SimpleSubjectInterface extends \SplSubject
{

    /**
     *
     * @param ObservableEntityInterface $observableEntity
     * @return void
     */
    public function setObservableEntity(ObservableEntityInterface $observableEntity): void;

    /**
     *
     * @return ObservableEntityInterface|null
     */
    public function getObservableEntity(): ?ObservableEntityInterface;

    /**
     *
     * @param \SplObserver|SimpleObserverInterface $observer
     * @return void
     */
    public function attach(\SplObserver|SimpleObserverInterface $observer): void;

    /**
     * @param SimpleObserverInterface $observer
     * @return bool
     */
    public function contains(SimpleObserverInterface $observer): bool;

    /**
     *
     * @param \SplObserver|SimpleObserverInterface $observer
     * @return void
     */
    public function detach(\SplObserver|SimpleObserverInterface $observer): void;

    /**
     *
     * @return void
     */
    public function notify(): void;

}
