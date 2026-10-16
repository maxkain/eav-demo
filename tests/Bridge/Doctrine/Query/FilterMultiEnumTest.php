<?php

namespace App\Tests\Bridge\Doctrine\Query;

use App\Entity\Product\Attribute\MultiEnumEav;
use App\Testing\AppKernelTestCase;

class FilterMultiEnumTest extends AppKernelTestCase
{
    public function testMultiEnumOrValues(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(MultiEnumEav::class), [4 => [32, 33]])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(47, $count);
    }

    public function testMultiEnumAndValues(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(MultiEnumEav::class), [4 => [32, 33]], orValueLogic: false)
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(40, $count);
    }

    public function testMultiEnumOrAttributesAndValues(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(MultiEnumEav::class),
                [4 => [32, 33], 5 => [43, 44]], orAttributeLogic: true)
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(55, $count);
    }

    public function testMultiEnumAndAttributesAndValues(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(MultiEnumEav::class),
                [4 => [32, 33], 5 => [43, 44]], orValueLogic: false)
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(20, $count);
    }

    public function testMultiEnumAndAttributesOrValues(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(MultiEnumEav::class), [4 => [32, 33], 5 => [43, 44]])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(32, $count);
    }
}
