<?php

namespace App\Tests\Bridge\Doctrine\Query;

use App\Entity\Product\Attribute\MultiEnumEav;
use App\Testing\AppKernelTestCase;

class ValueSelectTest extends AppKernelTestCase
{
    public function testMultiEnumValueSelectMin(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(MultiEnumEav::class), [4 => [32, 33], 5 => [43, 44]])
            ->getQueryBuilder($qb)
            ->select('e.id')
            ->orderBy('attribute_4', 'ASC');
        $this->getEavQueryFactory()->addValueSelect($qb, 'e', 4, $this->resolveOptions(MultiEnumEav::class),
            as: 'attribute_4');
        $row = $qb->getQuery()->setMaxResults(1)->getArrayResult()[0];
        $this->assertSame('MultiEnumValue 4-1', $row['attribute_4']);
    }

    public function testMultiEnumValueSelectMax(): void
    {
        $qb = $this->createEntityQueryBuilder();
        $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(MultiEnumEav::class), [4 => [32, 33], 5 => [43, 44]])
            ->getQueryBuilder($qb)
            ->select('e.id')
            ->orderBy('attribute_4', 'DESC');
        $this->getEavQueryFactory()->addValueSelect($qb, 'e', 4, $this->resolveOptions(MultiEnumEav::class),
            select: 'MAX(:value)', as: 'attribute_4');
        $row = $qb->getQuery()->setMaxResults(1)->getArrayResult()[0] ?? null;
        $this->assertIsArray($row);
        $this->assertArrayHasKey('attribute_4', $row);
        $this->assertSame('MultiEnumValue 4-9', $row['attribute_4']);
    }
}
