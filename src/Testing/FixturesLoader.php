<?php

namespace App\Testing;

class FixturesLoader
{
    public function __construct(
        private FixtureCreator $fixtureCreator,
        private SchemaManager $schemaManager
    ) {
    }

    public function load(): void
    {
        $this->schemaManager->reset();
        $this->fixtureCreator->createCategories(20);
        $this->fixtureCreator->createTags(20);
        $this->fixtureCreator->createEnumAttributes(10);
        $this->fixtureCreator->createMultiEnumAttributes(10);
        $this->fixtureCreator->createStringAttributes(10);
        $this->fixtureCreator->createMultiStringAttributes(10);
        $this->fixtureCreator->createProducts(100);
    }
}
