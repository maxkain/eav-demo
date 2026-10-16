<?php

namespace App\Tests\Bridge\Doctrine\OrphanedEavs;

use App\Entity\Product\Attribute\EnumEav;
use App\Entity\Product\Category;
use App\Testing\AppKernelTestCase;
use Maxkain\EavBundle\Bridge\Doctrine\EventListener\OrphanedEavsListener;
use Maxkain\EavBundle\Bridge\Doctrine\OrphanedEavs\OrphanedEavsCleaner;
use Maxkain\EavBundle\Bridge\Doctrine\OrphanedEavs\OrphanedEavsObjectHandler;

class OrphanedEavsCleanerTest extends AppKernelTestCase
{
    public function testCleanerWithQueryHandler(): void
    {
        $this->assertSame(15, $this->cleanerTest());
    }

    public function testCleanerWithObjectHandler(): void
    {
        $objectHandler = $this->getContainer()->get(OrphanedEavsObjectHandler::class);
        $cleaner = $this->getContainer()->get(OrphanedEavsCleaner::class);
        $cleaner->setHandler($objectHandler);
        $this->assertSame(15, $this->cleanerTest());
    }

    private function cleanerTest(): int
    {
        $this->getContainer()->get(OrphanedEavsListener::class)->setEnabled(false);
        $categories = $this->em->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if ($category->getId() > 10) {
                $this->em->remove($category);
            }
        }
        $this->em->flush();

        $this->getContainer()->get(OrphanedEavsCleaner::class)->clean();
        $qb = $this->createEntityQueryBuilder();

        return $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => [54, 53], 8 => [79, 74]],
                tagConditionEnabled: false, orAttributeLogic: true)
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
    }
}
