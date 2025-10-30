<?php

namespace App\Command;

use App\Entity\Product\Attribute\EnumAttribute;
use App\Entity\Product\Attribute\EnumEav;
use App\Entity\Product\Attribute\EnumTag;
use App\Entity\Product\Attribute\EnumValue;
use App\Entity\Product\Attribute\MultiEnumAttribute;
use App\Entity\Product\Attribute\MultiEnumEav;
use App\Entity\Product\Attribute\MultiEnumTag;
use App\Entity\Product\Attribute\MultiEnumValue;
use App\Entity\Product\Category;
use App\Entity\Product\Product;
use App\Entity\Product\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Maxkain\EavBundle\Bridge\Doctrine\EavQueryFactory;
use Maxkain\EavBundle\Contracts\Entity\Tag\EavAttributeWithTagsInterface;
use Maxkain\EavBundle\Inverter\EavInverterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:test',
    description: 'Test',
)]
class TestCommand extends Command
{
    private int $batchSize = 500;

    public function __construct(
        private EntityManagerInterface $em,
        private EavInverterInterface $eavInverter,
        private EavQueryFactory $eavQueryFactory,
        private Stopwatch $stopwatch
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('action', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = $this->stopwatch;

        $io = new SymfonyStyle($input, $output);
        $action = $input->getArgument('action');
        if ($action == 'create-all') {
            $this->createCategories(300, $io, $output);
            $this->createTags(50, $io, $output);
            $this->createEnumAttributes(500, $io, $output);
            $this->createMultiEnumAttributes(100, $io, $output);
            $this->createProducts(10000, $io, $output);
        }

        if ($action == 'filter') {
            $multiEnumFilter = [
                1060 => [10600]
            ];

            $enumFilter = [
                634 => 6349,
                649 => 6499
            ];

            $stopwatch->start('test');
            $qb = $this->em->getRepository(Product::class)->createQueryBuilder('e')->select('COUNT(e.id)');
            $this->eavQueryFactory->addEavFilters($qb, 'e', MultiEnumEav::class, $multiEnumFilter);
            $this->eavQueryFactory->addEavFilters($qb, 'e', EnumEav::class, $enumFilter);
            $stopwatch->lap('test');

            $result = $qb->getQuery()->getSingleScalarResult();
            $io->text($result);
            $stopwatch->lap('test');

            $qb = $this->em->getRepository(Product::class)->createQueryBuilder('e')->select('e.id')->setMaxResults(50);
            $this->eavQueryFactory->addEavFilters($qb, 'e', MultiEnumEav::class, $multiEnumFilter);
            $this->eavQueryFactory->addEavFilters($qb, 'e', EnumEav::class, $enumFilter);
            $result = $qb->getQuery()->getSingleColumnResult();

            dump($stopwatch->stop('test')->getPeriods());
        }

        $io->success('Done.');

        return Command::SUCCESS;
    }

    private function createCategories(int $count, SymfonyStyle $io, OutputInterface $output): void
    {
        $io->info('Creating categories...');
        $t = microtime(true);

        $em = $this->em;
        $categoriesCount = $em->getRepository(Category::class)->count();
        $categories = $em->getRepository(Category::class)->createQueryBuilder('e')->select()->getQuery();

        $progress = new ProgressBar($output, $categoriesCount);
        $progress->start();

        $this->putEntities($io, $progress, $categories->toIterable(), Category::class, $categoriesCount, $count, true,
            function (int $i, Category $category) {
                $category->setName('Category ' . $i);
            }
        );
    }

    private function createTags(int $count, SymfonyStyle $io, OutputInterface $output): void
    {
        $io->info('Creating tags...');

        $em = $this->em;
        $tagsCount = $em->getRepository(Tag::class)->count();
        $tags = $em->getRepository(Tag::class)->createQueryBuilder('e')->select()->getQuery();

        $progress = new ProgressBar($output, $tagsCount);
        $progress->start();

        $this->putEntities($io, $progress, $tags->toIterable(), Tag::class, $tagsCount, $count, true,
            function (int $i, Tag $tag) {
                $tag->setName('Tag ' . $i);
            }
        );
    }

    private function createEnumAttributes(int $count, SymfonyStyle $io, OutputInterface $output): void
    {
        $io->info('Creating enum attributes...');

        $em = $this->em;
        $attributesCount = $em->getRepository(EnumAttribute::class)->count();
        $attributes = $em->getRepository(EnumAttribute::class)->createQueryBuilder('e')->select()->getQuery();

        $progress = new ProgressBar($output, $attributesCount);
        $progress->start();

        $this->putEntities($io, $progress, $attributes->toIterable(), EnumAttribute::class, $attributesCount, $count, true,
            function (int $i, EnumAttribute|EavAttributeWithTagsInterface $attribute) use ($em) {
                $attribute->setForAllTags(false);
                $attribute->setName('EnumAttribute ' . $i);
            }
        );

        $attributes = $em->getRepository(EnumAttribute::class)->createQueryBuilder('e')->select()->getQuery();
        $i = 1;
        foreach ($attributes->toIterable() as $attribute) {
            $this->createEnumValues($attribute, $i, 10);
            $this->createEnumTags($attribute, $i, 20);
            $i++;
        }

        $em->flush();
        $em->clear();
    }

    private function createEnumValues(EnumAttribute $attribute, int $attributeIndex, int $count): void
    {
        $em = $this->em;
        $values = $em->getRepository(EnumValue::class)->findBy(['attribute' => $attribute]);

        $this->putEntities(null, null, $values, EnumValue::class, count($values), $count, false,
            function (int $i, EnumValue $value) use ($em, $attribute, $attributeIndex) {
                $value->setAttribute($attribute);
                try {
                    if (!$value->getTitle()) {
                        $value->setTitle('EnumValue ' . $attributeIndex . '-' . $i);
                    }
                } catch (\Throwable $e) {
                    $value->setTitle('EnumValue ' . $attributeIndex . '-' . $i);
                }
            }
        );
    }

    private function createEnumTags(EnumAttribute $attribute, int $attributeIndex, int $count): void
    {
        $em = $this->em;

        $categories = $em->getRepository(Category::class)->findAll();
        $attributeTags = $em->getRepository(EnumTag::class)->findBy(['attribute' => $attribute]);

        $this->putEntities(null, null, $attributeTags, EnumTag::class, count($attributeTags), $count, false,
            function (int $i, EnumTag $enumTag) use ($em, $attribute, $attributeIndex, $categories, $count) {
                $enumTag->setAttribute($attribute);
                $enumTag->setTag($this->calculateTag($attributeIndex * $count + $i, $categories));
            }
        );
    }

    private function createMultiEnumAttributes(int $count, SymfonyStyle $io, OutputInterface $output): void
    {
        $io->info('Creating multi enum attributes...');

        $em = $this->em;
        $attributesCount = $em->getRepository(MultiEnumAttribute::class)->count();
        $attributes = $em->getRepository(MultiEnumAttribute::class)->createQueryBuilder('e')->select()->getQuery();

        $progress = new ProgressBar($output, $attributesCount);
        $progress->start();

        $this->putEntities($io, $progress, $attributes->toIterable(), MultiEnumAttribute::class, $attributesCount, $count, true,
            function (int $i, MultiEnumAttribute|EavAttributeWithTagsInterface $attribute) use ($em) {
                $attribute->setForAllTags(false);
                $attribute->setName('MultiEnumAttribute ' . $i);
            }
        );

        $attributes = $em->getRepository(MultiEnumAttribute::class)->createQueryBuilder('e')->select()->getQuery();
        $i = 1;
        foreach ($attributes->toIterable() as $attribute) {
            $this->createMultiEnumValues($attribute, $i, 10);
            $this->createMultiEnumTags($attribute, $i, 10);
            $i++;
        }

        $em->flush();
        $em->clear();
    }

    private function createMultiEnumValues(MultiEnumAttribute $attribute, int $attributeIndex, int $count): void
    {
        $em = $this->em;
        $values = $em->getRepository(MultiEnumValue::class)->findBy(['attribute' => $attribute]);

        $this->putEntities(null, null, $values, MultiEnumValue::class, count($values), $count, false,
            function (int $i, MultiEnumValue $value) use ($em, $attribute, $attributeIndex) {
                $value->setAttribute($attribute);
                try {
                    if (!$value->getTitle()) {
                        $value->setTitle('MultiEnumValue ' . $attributeIndex . '-' . $i);
                    }
                } catch (\Throwable $e) {
                    $value->setTitle('MultiEnumValue ' . $attributeIndex . '-' . $i);
                }
            }
        );
    }

    private function createMultiEnumTags(MultiEnumAttribute $attribute, int $attributeIndex, int $count): void
    {
        $em = $this->em;

        $tags = $em->getRepository(Tag::class)->findAll();
        $attributeTags = $em->getRepository(MultiEnumTag::class)->findBy(['attribute' => $attribute]);

        $this->putEntities(null, null, $attributeTags, MultiEnumTag::class, count($attributeTags), $count, false,
            function (int $i, MultiEnumTag $enumTag) use ($em, $attribute, $attributeIndex, $tags, $count) {
                $enumTag->setAttribute($attribute);
                $enumTag->setTag($this->calculateTag($attributeIndex * $count + $i, $tags));
            }
        );
    }

    private function createProducts(int $count, SymfonyStyle $io, OutputInterface $output): void
    {
        $io->info('Creating products...');

        $em = $this->em;
        $productsCount = $em->getRepository(Product::class)->count();
        $products = $em->getRepository(Product::class)->createQueryBuilder('e')->select()->getQuery();

        $progress = new ProgressBar($output, $productsCount);
        $progress->start();

        $categories = $em->getRepository(Category::class)->findAll();
        $tags = $em->getRepository(Tag::class)->findAll();

        $this->putEntities($io, $progress, $products->toIterable(), Product::class, $productsCount, $count, true,
            function (int $i, Product $product)  use ($categories, $tags, $em) {
                $product->setName('Product ' . $i);
                $this->resetProductTags($i, $product, $categories, $tags);
                $this->setEnumValues($i, $product);
                $this->setMultiEnumValues($i, $product);
            }
        );
    }

    private function setEnumValues(int $i, Product $product): void
    {
        $enumAttributes = $this->eavInverter->findAllowedAttributes($product, EnumEav::class);
        $enumAttributes = $this->calculateTags($i, $enumAttributes);
        $items = [];
        foreach ($enumAttributes as $attribute) {
            $value = $this->calculateTag($i, $attribute->getValues());
            $items[] = ['attribute' => $attribute->getId(), 'value' => $value];
        }

        $this->eavInverter->invert($product, $items, $product->getEnumEavs(), EnumEav::class);
    }

    private function setMultiEnumValues(int $i, Product $product): void
    {
        $multiEnumAttributes = $this->eavInverter->findAllowedAttributes($product, MultiEnumEav::class);
        $multiEnumAttributes = $this->calculateTags($i, $multiEnumAttributes);
        $items = [];
        foreach ($multiEnumAttributes as $attribute) {
            $values = $this->calculateTags($i, $attribute->getValues());
            $items[] = ['attribute' => $attribute->getId(), 'values' => $values];
        }

        $eavs = $product->getMultiEnumEavs();
        $this->eavInverter->invert($product, $items, $eavs, MultiEnumEav::class);
    }

    private function resetProductTags(int $index, Product $product, array &$categories, array &$tags): void
    {
        $productCategory = $this->calculateTag($index, $categories);
        $productTags = $this->calculateTags($index, $tags);

        $product->setCategory($productCategory);
        $product->getTags()->clear();
        foreach ($productTags as $productTag) {
            $product->addTag($productTag);
        }
    }

    private function calculateTag(int $index, iterable|\Countable $tags): object
    {
        return $tags[$index % count($tags)];
    }

    private function calculateTags(int $index, iterable|\Countable $tags): array
    {
        if (count($tags) == 0) {
            return [];
        }

        $entityTagsCount = $index % (int) ceil(count($tags));
        $entityTags = [];
        for ($i = 0; $i < $entityTagsCount; $i++) {
            $entityTags[] = $tags[($index + $i) % count($tags)];
        }

        return $entityTags;
    }

    private function putEntities(
        ?SymfonyStyle $io,
        ?ProgressBar $progress,
        iterable $entities,
        string $entityFqcn,
        int $entitiesCount,
        int $count,
        bool $flush,
        mixed $processor,
    ): void {
        if ($io) {
            $t = microtime(true);
        }

        $em = $this->em;
        $i = 1;
        $processedEntities = [];
        foreach ($entities as $entity) {
            if ($i > $count) {
                $em->remove($entity);
            } else {
                $processor($i, $entity);
            }

            $processedEntities[] = $entity;

            if ($i % $this->batchSize == 0) {
                if ($flush) {
                    $em->flush();
                }
            }

            $progress?->advance();
            $i++;
        }

        if ($flush) {
            $em->flush();
        }

        for ($i = $entitiesCount + 1; $i <= $count; $i++) {
            $entity = new $entityFqcn;
            $processor($i, $entity);
            $em->persist($entity);
            $processedEntities[] = $entity;
            if ($i % $this->batchSize == 0) {
                if ($flush) {
                    $em->flush();
                }
            }

            $progress?->advance();
        }

        if ($flush) {
            $em->flush();
        }

        $progress?->finish();

        $io?->info(round((microtime(true) - $t) * 1000) . ' ms');
        $io?->info(round(memory_get_peak_usage(true) / 1024 / 1024) . ' Mb');
    }
}
