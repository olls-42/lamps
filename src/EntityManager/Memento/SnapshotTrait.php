<?php

namespace App\EntityManager\Memento;


use App\EntityManager\Entity\AbstractEntity;

/**
 *
 * a iterator over sequence of historical changes can be used as FIFO or LIFO
 */
trait SnapshotTrait
{
    /**
     * Snapshots is set of dereferenced pointers known spl_object_hash
     */
    protected \SplDoublyLinkedList $snapshots;

    /**
     * @inheritdoc
     */
    public function setOriginal(AbstractEntity $olderObject): void
    {
        $this->snapshots->unshift($olderObject);

        foreach ($this->snapshots as $snapshot) {
            $snapshot->uniqid = $olderObject->getUniqId();
        }

        // dirty fix, we update parent class property
        $this->uniqId = $olderObject->getUniqId();
    }

    /**
     * @inheritdoc
     */
    public function getCurrentSnapshot(): AbstractEntity
    {
        return $this->snapshots->current();
    }

    /**
     * @inheritdoc
     */
    public function getLastSnapshot(): AbstractEntity
    {
        return $this->snapshots->pop();
    }

    /**
     * @return \SplDoublyLinkedList
     */
    public function getSnapshots(): \SplDoublyLinkedList
    {
        return $this->snapshots;
    }

    protected function makeSnapshot(): void
    {
        $this->snapshots->push(clone $this);
    }

}
