<?php

namespace App\ValueObject;

readonly class SkuValueObject implements ValueObjectInterface
{
    public function __construct(
        private string $sku,
    )
    {
        assert(strlen($this->sku) > 5, 'sku must be longer 5 characters');
    }

    public function value(): string
    {
        return $this->sku;
    }

    public function equals(ValueObjectInterface $object): bool
    {
        return $this->sku === $object->sku;
    }

    public function __toString(): string
    {
        return spl_object_hash($this) .  " : SKU is ;: {$this->sku}";
    }

    public function __serialize(): array
    {
        return ['sku' => $this->sku];
    }

    public function __unserialize(array $data): void
    {
        $this->sku = $data['sku'];
    }
}
