<?php

declare(strict_types=1);

namespace App\Model;

final class Product
{
    public function __construct(private string $name, private string $key, private int $price)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
}
