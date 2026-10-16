<?php

namespace App\Testing;

use App\Entity\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Maxkain\EavBundle\Bridge\Doctrine\Query\EavQueryFactory;
use Maxkain\EavBundle\Contracts\Entity\Tag\EavAttributeWithTagsInterface;
use Maxkain\EavBundle\Converter\EavConverterInterface;
use Maxkain\EavBundle\Inverter\EavInverterInterface;
use Maxkain\EavBundle\Options\EavOptionsInterface;
use Maxkain\EavBundle\Options\EavOptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AppKernelTestCase extends KernelTestCase
{
    protected EntityManagerInterface $em;
    protected ManagerRegistry $doctrine;
    protected EavOptionsResolverInterface $eavOptionsResolver;
    protected EavInverterInterface $eavInverter;
    protected EavConverterInterface $eavConverter;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel(['debug' => false]);
        $this->doctrine = $this->getContainer()->get(ManagerRegistry::class);
        $this->em = $this->getContainer()->get(EntityManagerInterface::class);
        $this->eavOptionsResolver = $this->getContainer()->get(EavOptionsResolverInterface::class);
        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->em->getConnection()->isTransactionActive()) {
            $this->em->rollBack();
        }

        parent::tearDown();
    }

    protected function createEntityQueryBuilder(): QueryBuilder
    {
        return $this->em->getRepository(Product::class)->createQueryBuilder('e');
    }

    protected function getEavQueryFactory(): EavQueryFactory
    {
        return $this->getContainer()->get(EavQueryFactory::class);
    }

    protected function resolveOptions(EavOptionsInterface|string $options): EavOptionsInterface
    {
        return $this->eavOptionsResolver->resolve($options);
    }

    protected function getEavInverter(): EavInverterInterface
    {
        return $this->getContainer()->get(EavInverterInterface::class);
    }

    protected function getEavConverter(): EavConverterInterface
    {
        return $this->getContainer()->get(EavConverterInterface::class);
    }
}
