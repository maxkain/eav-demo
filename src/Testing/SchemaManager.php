<?php

namespace App\Testing;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class SchemaManager
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function reset(): void
    {
        $tool = new SchemaTool($this->em);
        $metaData = $this->em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($metaData);
        $tool->createSchema($metaData);
    }
}
