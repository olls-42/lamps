<?php

namespace App\ValueObject;

readonly class UniqIdValueObject implements ValueObjectInterface
{
    public function __construct(
        private string $value
    )
    {
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function __serialize(): array
    {
        return ['value' => $this->value];
    }

    public function __unserialize(array $data): void
    {
        $this->value = $data['value'];
    }
}
