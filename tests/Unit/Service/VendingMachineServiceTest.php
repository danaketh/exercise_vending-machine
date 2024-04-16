<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Exception\NotEnoughMoneyException;
use App\Model\Product;
use App\Service\VendingMachineService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\App\Service\VendingMachineService::class)]
final class VendingMachineServiceTest extends TestCase
{
    public function testGetProducts(): void
    {
        $vendingMachineService = new VendingMachineService([
            new Product('Soda', 'soda', 137),
            new Product('Water', 'water', 85),
            new Product('Juice', 'juice', 111),
            new Product('Sandwich', 'sandwich', 272),
        ]);

        self::assertCount(4, $vendingMachineService->getProducts());
    }

    public function testAddCoin(): void
    {
        $vendingMachineService = new VendingMachineService([
            new Product('Soda', 'soda', 137),
        ]);

        $vendingMachineService->addCoin(50)
            ->addCoin(100)
            ->addCoin(5)
            ->addCoin(1);
        self::assertEquals(156, $vendingMachineService->getInsertedAmount());
    }

    public function testReset(): void
    {
        $vendingMachineService = new VendingMachineService([
            new Product('Soda', 'soda', 137),
        ]);

        $vendingMachineService->addCoin(100)
            ->addCoin(100)
            ->addCoin(50)
            ->addCoin(25)
            ->addCoin(10)
            ->addCoin(1)
            ->addCoin(1);
        self::assertEquals(287, $vendingMachineService->getInsertedAmount());
        $change = $vendingMachineService->reset();
        self::assertEquals(0, $vendingMachineService->getInsertedAmount());
        self::assertEquals([100 => 2, 50 => 1, 25 => 1, 10 => 1, 1 => 2], $change);
    }

    public function testSelectProduct(): void
    {
        $vendingMachineService = new VendingMachineService([
            new Product('Soda', 'soda', 137),
            new Product('Water', 'water', 85),
            new Product('Juice', 'juice', 111),
            new Product('Sandwich', 'sandwich', 272),
        ]);

        $this->expectException(NotEnoughMoneyException::class);
        $vendingMachineService->selectProduct(4);
        $product = $vendingMachineService->addCoin(100)
            ->addCoin(100)
            ->addCoin(100)
            ->selectProduct(4);
        self::assertEquals('Sandwich', $product->getName());
        self::assertEquals(28, $vendingMachineService->getInsertedAmount());
    }

    public function testGetChange(): void
    {
        $vendingMachineService = new VendingMachineService([
            new Product('Soda', 'soda', 137),
            new Product('Water', 'water', 85),
            new Product('Juice', 'juice', 111),
            new Product('Sandwich', 'sandwich', 272),
        ]);

        $product = $vendingMachineService->addCoin(100)
            ->addCoin(100)
            ->addCoin(100)
            ->selectProduct(4);
        self::assertEquals('Sandwich', $product->getName());
        self::assertEquals(28, $vendingMachineService->getInsertedAmount());
        $change = $vendingMachineService->getChange();
        self::assertEquals([25 => 1, 1 => 3], $change);
    }
}
