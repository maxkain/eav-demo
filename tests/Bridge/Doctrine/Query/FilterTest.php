<?php

namespace App\Tests\Bridge\Doctrine\Query;

use App\Entity\Product\Attribute\EnumEav;
use App\Entity\Product\Attribute\MultiEnumEav;
use App\Entity\Product\Attribute\MultiStringEav;
use App\Entity\Product\Attribute\StringEav;
use App\Testing\AppKernelTestCase;
use Maxkain\EavBundle\Query\EavComparison;
use Maxkain\EavBundle\Query\EavExpression;

class FilterTest extends AppKernelTestCase
{
    public function testString(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(StringEav::class),
                [1 => 'StringValue 1-1', 2 => 'StringValue 2-1'])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(10, $count);
    }

    public function testMultiString(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(MultiStringEav::class),
                [1 => ['MultiStringValue 1-1-1', 'MultiStringValue 1-1-2'], 2 => 'MultiStringValue 2-1-1'])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(10, $count);
    }

    public function testEavComparison(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class),
                [6 => new EavComparison('=', 54)])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(10, $count);
    }

    public function testEavComparisonTitle(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class),
                [6 => new EavComparison('>', 'EnumValue 6-4')])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(30, $count);
    }

    public function testEavExpression(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class),
                [6 => new EavExpression(":field = 54")])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(10, $count);
    }

    public function testEavExpressionTitle(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class),
                [6 => new EavExpression(":fieldTitle = 'EnumValue 6-4'")])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(10, $count);
    }

    public function testMultiEavOrAttributesManyValues(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => [54, 53], 8 => [79, 74]], orAttributeLogic: true)
            ->addEavFilters($qb, 'e', $this->resolveOptions(MultiEnumEav::class), [4 => [32, 33], 5 => [43, 44]])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(17, $count);
    }
}
