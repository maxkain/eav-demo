<?php

namespace App\Command;

use App\Testing\FixtureCreator;
use App\Testing\FixturesLoader;
use App\Testing\SchemaManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:test', 'Test utils')]
class TestCommand
{
    public function __construct(
        private FixtureCreator $fixtureCreator,
        private FixturesLoader $fixturesLoader,
        private SchemaManager $schemaManager
    ) {
    }

    #[AsCommand('update-lite-data', 'Updates lite data')]
    public function updateTestData(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->fixtureCreator->createCategories(20, $io, $output);
        $this->fixtureCreator->createTags(20, $io, $output);
        $this->fixtureCreator->createEnumAttributes(10, $io, $output);
        $this->fixtureCreator->createMultiEnumAttributes(10, $io, $output);
        $this->fixtureCreator->createStringAttributes(10);
        $this->fixtureCreator->createMultiStringAttributes(10);
        $this->fixtureCreator->createProducts(100, $io, $output);

        return Command::SUCCESS;
    }

    #[AsCommand('update-heavy-data', 'Updates heavy data')]
    public function updateBenchmarkData(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->fixtureCreator->createCategories(200, $io, $output);
        $this->fixtureCreator->createTags(50, $io, $output);
        $this->fixtureCreator->createEnumAttributes(200, $io, $output);
        $this->fixtureCreator->createMultiEnumAttributes(50, $io, $output);
        $this->fixtureCreator->createStringAttributes(50);
        $this->fixtureCreator->createMultiStringAttributes(50);
        $this->fixtureCreator->createProducts(10000, $io, $output);

        return Command::SUCCESS;
    }

    #[AsCommand('load-fixtures', 'Clears database and loads fixtures')]
    public function loadFixtures(InputInterface $input, OutputInterface $output): int
    {
        $this->fixturesLoader->load();
        return Command::SUCCESS;
    }

    #[AsCommand('schema-reset', 'Removes all tables and creates new empty schema')]
    public function schemaReset(InputInterface $input, OutputInterface $output): int
    {
        $this->schemaManager->reset();
        return Command::SUCCESS;
    }
}
