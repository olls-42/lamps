<?php

namespace App\EntityManager\Entity;

use App\EntityManager\Memento\SimpleMementoInterface;
use App\EntityManager\Memento\SnapshotTrait;
use App\EntityManager\Observer\ObjectLifeCycleSubject;
use App\EntityManager\Observer\ObservableEntityInterface;
use App\EntityManager\Observer\SimpleSubjectInterface;
use App\ValueObject\UniqIdValueObject;
use App\ValueObject\ValueObjectInterface;

/**
 * Abstract, base class for in-memory representation of various business entities.
 * The only item we have implemented at this point is InventoryItem (see below).
 */
abstract class AbstractEntity
    implements EntityInterface, SimpleMementoInterface, ObservableEntityInterface
{
    use SnapshotTrait;

    protected UniqIdValueObject $uniqId;
    protected string $primaryKeyProperty;
    protected array $objectPropertiesMetadata = array();
    public SimpleSubjectInterface $entityLifeCycleSubject;

    public function __construct()
    {
        $this->uniqId = new UniqIdValueObject(uniqid());
        $this->snapshots  = new \SplDoublyLinkedList();
        $this->setupPrimaryKeyProperty();
        $this->entityLifeCycleSubject = new ObjectLifeCycleSubject();
        $this->entityLifeCycleSubject->setObservableEntity($this);
    }

    public function __serialize()
    {
        $output = [
            'uniqId' => $this->uniqId->value()
        ];

        foreach ($this->getMembers() as $propertyName => $propertyClassname) {
            $output[$propertyName] = $this->{$propertyName}->value();
        }

        return $output;
    }

    public function __unserialize(array $data): void
    {
        foreach ($this->getMembers() as $paramName => $className) {
            $this->{$paramName} = new $className($data[$paramName]);
        }

        $this->uniqId = new UniqIdValueObject($data['uniqId']);

        $this->snapshots  = new \SplDoublyLinkedList();

        $this->entityLifeCycleSubject = new ObjectLifeCycleSubject();
        $this->entityLifeCycleSubject->setObservableEntity($this);
    }

    public function __toString()
    {
        return static::class . ": " . spl_object_id($this) . ": uniqId: " . $this->uniqId;
    }


    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return "Entity {$this->uniqId} with {$this->getPrimaryKey()->value()}";
    }

    /**
     * @inheritdoc
     */
    public function getEntityLifeCycleSubject(): ObjectLifeCycleSubject
    {
        return $this->entityLifeCycleSubject;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKey(): ValueObjectInterface
    {
        return $this->{$this->primaryKeyProperty};
    }

    /**
     * @inheritdoc
     */
    public function getUniqId(): UniqIdValueObject
    {
        return $this->uniqId;
    }

    /**
     * @inheritdoc
     */
    public function merge(AbstractEntity $patch): void
    {
        if (static::class != $patch::class) {
            throw new \Exception('Cant merge different classes');
        }

        $this->makeSnapshot();

        foreach ($this->getMembers() as $prop => $class) {
            $this->{$prop} = $patch->{$prop};
        }

        $this->uniqId = $patch->uniqId;
    }

    /**
     *
     *
     * @param bool $force
     * @return array
     */
    protected function getMembers(bool $force = false): array
    {
        if (!empty($this->objectPropertiesMetadata) and !$force) {
            return $this->objectPropertiesMetadata;
        }

        $objRef = new \ReflectionObject($this);
        $ctor = $objRef->getConstructor();

        foreach ($ctor->getParameters() as $param) {
            $paramName = $param->getName();
            $paramType = $param->getType();
            $paramTypeClassname = $param->getType()->getName();
            $this->objectPropertiesMetadata[$paramName] = $paramTypeClassname;
        }

        return $this->objectPropertiesMetadata;
    }

    /**
     *
     *
     * @param ValueObjectInterface $object
     * @param $value
     * @return void
     */
    protected function update(ValueObjectInterface $object, $value): void
    {
        $this->makeSnapshot();

        foreach ($this->getMembers() as $thisPropertyName => $thisPropertyClassname) {
            if ($object::class == $thisPropertyClassname) {
                // deref valueObject ? it is ok for readonly classes
                $this->{$thisPropertyName} = new $thisPropertyClassname($value);
            }
        }

        $this->entityLifeCycleSubject->notify();
    }

    /**
     *
     * Define a property for using as primary key
     *
     * @return void
     */
    abstract protected function setupPrimaryKeyProperty(): void;


}

