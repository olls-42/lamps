<?php

namespace App\ValueObject;

readonly class SalePriceValueObject implements ValueObjectInterface
{

    public function __construct(
        private int $cent
    ){
        assert($this->cent > 0, 'Cost should be great than zero');
    }

    public function value(): int
    {
        return $this->cent;
    }

    public function equals(ValueObjectInterface $object): bool
    {
        // TODO: Implement equals() method.
    }

    public function __toString(): string
    {
        return spl_object_hash($this) . " : SalePrice is: {$this->cent}";
    }

    public function __serialize(): array
    {
        return ['cent' => $this->cent];
    }

    public function __unserialize(array $data): void
    {
        $this->cent = $data['cent'];
    }
}
