<?php

namespace App\ValueObject;


readonly class QohValueObject implements ValueObjectInterface
{

    public function __construct(
        private int $qoh,
    ){
        assert($this->qoh > 0, 'QOH can\'t be negative');
    }

    public function value(): int
    {
        return $this->qoh;
    }

    public function __toString(): string
    {
        return spl_object_hash($this) . " : QOH has: {$this->qoh} items";
    }

    public function __serialize(): array
    {
        return ['qoh' => $this->qoh];
    }

    public function __unserialize(array $data): void
    {
        $this->qoh = $data['qoh'];
    }
}
