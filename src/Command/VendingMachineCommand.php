<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Product;
use App\Service\VendingMachineService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

use function assert;

final class VendingMachineCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('vending-machine')
            ->setDescription('Vending Machine');
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $vendingMachineService = new VendingMachineService([
            new Product('Soda', 'soda', 137),
            new Product('Water', 'water', 85),
            new Product('Juice', 'juice', 111),
            new Product('Sandwich', 'sandwich', 272),
            new Product('Chips', 'chips', 99),
        ]);
        $helper = $this->getHelper('question');
        assert($helper instanceof QuestionHelper);

        $this->printVendingMachine($output, $vendingMachineService);

        $insertCoins = true;

        while ($insertCoins) {
            $request = new Question('Insert coin (or press [Enter] to continue): ', 0);
            $insertedCoin = $helper->ask($input, $output, $request);

            if ($insertedCoin === '0') {
                $insertCoins = false;
                continue;
            }

            $vendingMachineService->addCoin((int)$insertedCoin);
            $this->printVendingMachine($output, $vendingMachineService);
        }

        $products = $this->getProductsList($vendingMachineService->getProducts());
        $choiceQuestion = new ChoiceQuestion(
            'Please select your product: ',
            $products,
        );
        $choice = $helper->ask($input, $output, $choiceQuestion);
        $productIndex = array_search($choice, $products, true);

        if ($productIndex === false) {
            $output->writeln("Invalid product selected");

            return 1;
        }

        $product = $vendingMachineService->selectProduct($productIndex);
        $output->writeln(sprintf("You selected: %s", $product->getName()));

        if ($vendingMachineService->getInsertedAmount() > 0) {
            $output->writeln("");
            $output->writeln("Please, take your change:");
            $change = $vendingMachineService->getChange();

            foreach ($change as $coin => $amount) {
                $output->writeln(sprintf("%d¢: %d", $coin, $amount));
            }
        }

        $output->writeln("");
        $output->writeln("Thank you for your purchase! Enjoy!");

        return 0;
    }

    /**
     * @param array<Product> $products
     *
     * @return array<int, string>
     */
    private function getProductsList(array $products): array
    {
        $list = [];

        foreach ($products as $index => $product) {
            $list[$index + 1] = sprintf("%s (%d¢)", $product->getName(), $product->getPrice());
        }

        return $list;
    }

    private function printVendingMachine(OutputInterface $output, VendingMachineService $vendingMachineService): void
    {
        $output->writeln("");
        $output->writeln('Vending Machine');
        $output->writeln('===============');
        $output->writeln(sprintf('Total inserted: %d¢', $vendingMachineService->getInsertedAmount()));
        $output->writeln("");
        $this->printProducts($output, $vendingMachineService->getProducts());
        $output->writeln("");
    }

    /**
     * @param Product[] $products
     */
    private function printProducts(OutputInterface $output, array $products): void
    {
        foreach ($products as $product) {
            $output->writeln(sprintf("%s (%d¢)", $product->getName(), $product->getPrice()));
        }
    }
}
