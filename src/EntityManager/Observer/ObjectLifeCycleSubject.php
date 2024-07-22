<?php

namespace App\EntityManager\Observer;


class ObjectLifeCycleSubject implements SimpleSubjectInterface
{
    const CREATED = 'created';
    const UPDATED = 'updated';
    const DELETED = 'deleted';

    private ?ObservableEntityInterface $observableEntity = null;

    private \SplObjectStorage $observers;

    public function __construct()
    {
        $this->observers = new \SplObjectStorage();
    }

    /**
     * @inheritdoc
     */
    public function getObservableEntity(): ?ObservableEntityInterface
    {
        return $this->observableEntity;
    }

    /**
     * @inheritdoc
     */
    public function setObservableEntity(ObservableEntityInterface $observableEntity): void
    {
        $this->observableEntity = $observableEntity;
    }

    /**
     * @inheritdoc
     */
    public function attach(SimpleObserverInterface|\SplObserver $observer): void
    {
        $this->observers->attach($observer);
    }

    /**
     * @inheritdoc
     */
    public function detach(\SplObserver|SimpleObserverInterface $observer): void
    {
        $this->observers->detach($observer);
    }

    /**
     * @inheritdoc
     */
    public function notify(): void
    {
        /** @var \SplObserver $observer */
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function contains(SimpleObserverInterface $observer): bool
    {
        return $this->observers->contains($observer);
    }

}
