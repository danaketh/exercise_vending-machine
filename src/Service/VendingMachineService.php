<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\InvalidCoinException;
use App\Exception\InvalidProductCodeException;
use App\Exception\NotEnoughMoneyException;
use App\Model\Product;

final class VendingMachineService
{
    private int $insertedAmount = 0;

    /**
     * @var int[] $coins
     */
    private array $coins = [1,5,10,25,50,100];

    /**
     * @var Product[] $products
     */
    private array $products;

    /**
     * @param Product[] $products
     */
    public function __construct(array $products)
    {
        $this->products = $products;
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function addCoin(int $coin): self
    {
        if (!in_array($coin, $this->coins, true)) {
            throw new InvalidCoinException(sprintf('Invalid coin %d', $coin));
        }

        $this->insertedAmount += $coin;

        return $this;
    }

    public function getInsertedAmount(): int
    {
        return $this->insertedAmount;
    }

    /**
     * @return array<int, int>
     */
    public function reset(): array
    {
        $change = $this->calculateChange($this->insertedAmount);
        $this->insertedAmount = 0;

        return $change;
    }

    public function selectProduct(int $index): Product
    {
        $realIndex = $index - 1;

        if (!isset($this->products[$realIndex])) {
            throw new InvalidProductCodeException('Invalid product code ' . $index);
        }

        return $this->pay($this->products[$realIndex]);
    }

    /**
     * @return array<int, int>
     */
    public function getChange(): array
    {
        return $this->reset();
    }

    private function pay(Product $product): Product
    {
        if ($this->insertedAmount < $product->getPrice()) {
            throw new NotEnoughMoneyException(sprintf(
                'Not enough money. Please insert additional %d cents',
                $product->getPrice() - $this->insertedAmount
            ));
        }

        $this->insertedAmount -= $product->getPrice();

        return $product;
    }

    /**
     * @return array<int, int>
     */
    private function calculateChange(int $amount): array
    {
        $coins = $this->coins;
        rsort($coins); // make sure we always start from the top
        $change = [];

        foreach ($coins as $coin) {
            if ($amount >= $coin) {
                $count = intdiv($amount, $coin);
                $amount %= $coin;
                $change[$coin] = $count;
            }
        }

        return $change;
    }
}
