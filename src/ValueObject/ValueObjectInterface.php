<?php

namespace App\ValueObject;

interface ValueObjectInterface
{
    public function value();
    public function __toString(): string;
    public function __serialize(): array;
    public function __unserialize(array $data): void;
}
