<?php

namespace App\EntityManager\Entity;

use App\EntityManager\Observer\ObjectLifeCycleSubject;
use App\ValueObject\UniqIdValueObject;
use App\ValueObject\ValueObjectInterface;

/**
 *
 */
interface EntityInterface
{
    /**
     *
     * @return UniqIdValueObject
     */
    public function getUniqId(): UniqIdValueObject;

    /**
     *
     * @return ValueObjectInterface
     */
    public function getPrimaryKey(): ValueObjectInterface;

    /**
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     *
     * @param AbstractEntity $patch
     * @return void
     */
    public function merge(AbstractEntity $patch): void;

    /**
     *
     *
     * @return ObjectLifeCycleSubject
     */
    public function getEntityLifeCycleSubject(): ObjectLifeCycleSubject;

}
