<?php

namespace App\EntityManager\Memento;

use App\EntityManager\Entity\AbstractEntity;

interface SimpleMementoInterface
{
    /**
     *
     * @param AbstractEntity $olderObject
     * @return void
     */
    public function setOriginal(AbstractEntity $olderObject): void;

    /**
     *
     * @return AbstractEntity
     */
    public function getLastSnapshot(): AbstractEntity;

    /**
     *
     * @return AbstractEntity
     */
    public function getCurrentSnapshot(): AbstractEntity;

    /**
     *
     * @return \SplDoublyLinkedList
     */
    public function getSnapshots(): \SplDoublyLinkedList;
}
