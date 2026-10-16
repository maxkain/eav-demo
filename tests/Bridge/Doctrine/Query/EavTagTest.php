<?php

namespace App\Tests\Bridge\Doctrine\Query;

use App\Entity\Product\Attribute\EnumAttribute;
use App\Entity\Product\Attribute\EnumEav;
use App\Entity\Product\Attribute\MultiEnumAttribute;
use App\Entity\Product\Attribute\MultiEnumEav;
use App\Entity\Product\Category;
use App\Testing\AppKernelTestCase;
use Maxkain\EavBundle\Bridge\Doctrine\EventListener\OrphanedEavsListener;
use Maxkain\EavBundle\Bridge\Doctrine\OrphanedEavs\OrphanedEavsObjectHandler;
use Maxkain\EavBundle\Contracts\Entity\Tag\EavAttributeWithTagsInterface;

class EavTagTest extends AppKernelTestCase
{
    public function testEnumTag(): void
    {
        $this->assertSame(25, $this->enumTagTest());
    }

    public function testEnumTagWithDisabledListener(): void
    {
        $this->getContainer()->get(OrphanedEavsListener::class)->setEnabled(false);
        $this->assertSame(25, $this->enumTagTest());
    }

    public function testEnumTagDisabledListenerAndWithoutEavTagCondition(): void
    {
        $this->getContainer()->get(OrphanedEavsListener::class)->setEnabled(false);
        $this->assertSame(30, $this->enumTagTest(tagConditionEnabled: false));
    }

    public function testEnumTagWithoutEavTagCondition(): void
    {
        $this->assertSame(25, $this->enumTagTest(tagConditionEnabled: false));
    }

    private function enumTagTest(bool $tagConditionEnabled = true): int
    {
        // removes Category4 tag from EnumAttribute6
        $this->removeTag(EnumAttribute::class, 6, 4);
        $qb = $this->createEntityQueryBuilder();
        return $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => [54, 53], 8 => [79, 74]],
                tagConditionEnabled: $tagConditionEnabled,
                orAttributeLogic: true
            )
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
    }

    public function testEnumTagRemoveCategoriesAndForAll(): void
    {
        $this->getContainer()->get(OrphanedEavsListener::class)->setEnabled(false);
        $categories = $this->em->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if ($category->getId() > 10) {
                $this->em->remove($category);
            }
        }
        $this->em->flush();

        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => [54, 53], 8 => [79, 74]],
                tagConditionEnabled: false, orAttributeLogic: true)
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(30, $count);

        $enumAttribute6 = $this->em->getRepository(EnumAttribute::class)->find(6);
        $enumAttribute6->setForAllTags(true);
        $this->em->flush();
        $qb = $this->createEntityQueryBuilder();
        $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => [54, 53], 8 => [79, 74]],
                orAttributeLogic: true)
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)');
        $count = $qb->getQuery()->getSingleScalarResult();
        $this->assertSame(25, $count);
    }

    public function testEnumTagRemoveCategoriesWithQueryHandler(): void
    {
        $categories = $this->em->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if ($category->getId() > 10) {
                $this->em->remove($category);
            }
        }
        $this->em->flush();

        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => [54, 53], 8 => [79, 74]],
                tagConditionEnabled: false, orAttributeLogic: true)
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(15, $count);
    }

    public function testEnumTagRemoveCategoriesWithObjectHandler(): void
    {
        $container = $this->getContainer();
        $objectHandler = $container->get(OrphanedEavsObjectHandler::class);
        $listener = $container->get(OrphanedEavsListener::class);
        $listener->setHandler($objectHandler);
        $categories = $this->em->getRepository(Category::class)->findAll();
        foreach ($categories as $category) {
            if ($category->getId() > 10) {
                $this->em->remove($category);
            }
        }
        $this->em->flush();
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(EnumEav::class), [6 => [54, 53], 8 => [79, 74]],
                tagConditionEnabled: false, orAttributeLogic: true)
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(15, $count);
    }

    public function testMultiEnumTag(): void
    {
        $this->removeTag(MultiEnumAttribute::class, 4, [2, 3, 4]);
        $qb = $this->createEntityQueryBuilder();
        $count = $this->getEavQueryFactory()
            ->addEavFilters($qb, 'e', $this->resolveOptions(MultiEnumEav::class), [4 => [32, 33]])
            ->getQueryBuilder($qb)
            ->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $this->assertSame(47, $count);
    }

    private function removeTag(string $attributeFqcn, int $attributeId, int|array $tagId): void
    {
        /** @var EavAttributeWithTagsInterface $attribute */
        $attribute = $this->em->find($attributeFqcn, $attributeId);
        $tags = $attribute->getTags();
        $tagIds = is_array($tagId) ? $tagId : [$tagId];
        foreach ($tags as $tag) {
            if (in_array($tag->getTag()->getId(), $tagIds)) {
                $attribute->removeTag($tag);
            }
        }

        $this->em->flush();
    }
}
