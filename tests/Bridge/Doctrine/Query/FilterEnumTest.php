<?php

namespace App\Tests\Bridge\Doctrine\Query;

use App\Entity\Product\Attribute\EnumEav;
use App\Testing\AppKernelTestCase;

class FilterEnumTest extends AppKernelTestCase
{
    public function testEnumOneValue(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => 54])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(10, $count);
    }

    public function testEnumAndValues(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => [54, 53]], orValueLogic: false)
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(0, $count);
    }

    public function testEnumOrValues(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => [54, 53]])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(20, $count);
    }

    public function testEnumAndAttributesOneValue(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => 54, 8 => 79])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(0, $count);
    }

    public function testEnumOrAttributesOneValue(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => 54, 8 => 79], orAttributeLogic: true)
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(20, $count);
    }

    public function testEnumAndAttributesManyValues(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => [54, 53], 8 => [79, 74]])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(10, $count);
    }

    public function testEnumOrAttributesManyValues(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => [54, 53], 8 => [79, 74]], orAttributeLogic: true)
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(30, $count);
    }
}
