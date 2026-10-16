<?php

namespace App\Testing;

use App\Entity\Product\Attribute\EnumAttribute;
use App\Entity\Product\Attribute\EnumEav;
use App\Entity\Product\Attribute\EnumTag;
use App\Entity\Product\Attribute\EnumValue;
use App\Entity\Product\Attribute\MultiEnumAttribute;
use App\Entity\Product\Attribute\MultiEnumCategory;
use App\Entity\Product\Attribute\MultiEnumEav;
use App\Entity\Product\Attribute\MultiEnumTag;
use App\Entity\Product\Attribute\MultiEnumValue;
use App\Entity\Product\Attribute\MultiStringAttribute;
use App\Entity\Product\Attribute\MultiStringEav;
use App\Entity\Product\Attribute\StringAttribute;
use App\Entity\Product\Attribute\StringEav;
use App\Entity\Product\Category;
use App\Entity\Product\Product;
use App\Entity\Product\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Maxkain\EavBundle\AttributeFinder\EavAttributeFinderInterface;
use Maxkain\EavBundle\Contracts\Entity\Tag\EavAttributeWithTagsInterface;
use Maxkain\EavBundle\Inverter\EavInverterInterface;
use Maxkain\EavBundle\Options\EavOptionsResolverInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixtureCreator
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected EavInverterInterface $eavInverter,
        protected BatchEntitySaver $batchEntitySaver,
        protected EavOptionsResolverInterface $eavOptionsResolver,
        protected EavAttributeFinderInterface $eavAttributeFinder
    ) {
    }

    public function createCategories(int $count, ?SymfonyStyle $io = null, ?OutputInterface $output = null): void
    {
        $io?->info('Creating categories...');

        $em = $this->em;
        $categoriesCount = $em->getRepository(Category::class)->count();
        $categories = $em->getRepository(Category::class)->createQueryBuilder('e')->select()->getQuery();

        $progress = null;
        if ($output) {
            $progress = new ProgressBar($output, $categoriesCount);
            $progress->start();
        }

        $this->batchEntitySaver->save($io, $progress, $categories->toIterable(), Category::class, $categoriesCount, $count, true,
            function (int $i, Category $category) {
                $category->setName('Category ' . $i);
            }
        );
    }

    public function createTags(int $count, ?SymfonyStyle $io = null, ?OutputInterface $output = null): void
    {
        $io?->info('Creating tags...');

        $em = $this->em;
        $tagsCount = $em->getRepository(Tag::class)->count();
        $tags = $em->getRepository(Tag::class)->createQueryBuilder('e')->select()->getQuery();

        $progress = null;
        if ($output) {
            $progress = new ProgressBar($output, $tagsCount);
            $progress->start();
        }

        $this->batchEntitySaver->save($io, $progress, $tags->toIterable(), Tag::class, $tagsCount, $count, true,
            function (int $i, Tag $tag) {
                $tag->setName('Tag ' . $i);
            }
        );
    }

    public function createEnumAttributes(int $count, ?SymfonyStyle $io = null, ?OutputInterface $output = null): void
    {
        $io?->info('Creating enum attributes...');

        $em = $this->em;
        $attributesCount = $em->getRepository(EnumAttribute::class)->count();
        $attributes = $em->getRepository(EnumAttribute::class)->createQueryBuilder('e')->select()->getQuery();

        $progress = null;
        if ($output) {
            $progress = new ProgressBar($output, $attributesCount);
            $progress->start();
        }

        $this->batchEntitySaver->save($io, $progress, $attributes->toIterable(), EnumAttribute::class, $attributesCount, $count, true,
            function (int $i, EnumAttribute|EavAttributeWithTagsInterface $attribute) use ($em) {
                $attribute->setForAllTags(false);
                $attribute->setName('EnumAttribute ' . $i);
            }
        );

        $attributes = $em->getRepository(EnumAttribute::class)->createQueryBuilder('e')->select()->getQuery();
        $i = 1;
        foreach ($attributes->toIterable() as $attribute) {
            $this->createEnumValues($attribute, $i, 10);
            $this->createEnumCategories($attribute, $i, 20);
            $i++;
        }

        $em->flush();
        $em->clear();
    }

    public function createStringAttributes(int $count, ?SymfonyStyle $io = null, ?OutputInterface $output = null): void
    {
        $io?->info('Creating string attributes...');

        $em = $this->em;
        $attributesCount = $em->getRepository(StringAttribute::class)->count();
        $attributes = $em->getRepository(StringAttribute::class)->createQueryBuilder('e')->select()->getQuery();

        $progress = null;
        if ($output) {
            $progress = new ProgressBar($output, $attributesCount);
            $progress->start();
        }

        $this->batchEntitySaver->save($io, $progress, $attributes->toIterable(), StringAttribute::class, $attributesCount, $count, true,
            function (int $i, StringAttribute $attribute) use ($em) {
                $attribute->setName('StringAttribute ' . $i);
            }
        );

        $em->flush();
        $em->clear();
    }

    public function createMultiStringAttributes(int $count, ?SymfonyStyle $io = null, ?OutputInterface $output = null): void
    {
        $io?->info('Creating multiString attributes...');

        $em = $this->em;
        $attributesCount = $em->getRepository(MultiStringAttribute::class)->count();
        $attributes = $em->getRepository(MultiStringAttribute::class)->createQueryBuilder('e')->select()->getQuery();

        $progress = null;
        if ($output) {
            $progress = new ProgressBar($output, $attributesCount);
            $progress->start();
        }

        $this->batchEntitySaver->save($io, $progress, $attributes->toIterable(), MultiStringAttribute::class, $attributesCount, $count, true,
            function (int $i, MultiStringAttribute $attribute) use ($em) {
                $attribute->setName('MultiStringAttribute ' . $i);
            }
        );

        $em->flush();
        $em->clear();
    }

    public function createEnumValues(EnumAttribute $attribute, int $attributeIndex, int $count): void
    {
        $em = $this->em;
        $values = $em->getRepository(EnumValue::class)->findBy(['attribute' => $attribute]);

        $this->batchEntitySaver->save(null, null, $values, EnumValue::class, count($values), $count, false,
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

    public function createEnumCategories(EnumAttribute $attribute, int $attributeIndex, int $count): void
    {
        $em = $this->em;

        $categories = $em->getRepository(Category::class)->findAll();
        $em->getRepository(EnumTag::class)->createQueryBuilder('e')
            ->delete()->where("e.attribute = {$attribute->getId()}")->getQuery()->execute();

        $this->batchEntitySaver->save(null, null, [], EnumTag::class, 0, $count, false,
            function (int $i, EnumTag $enumTag) use ($em, $attribute, $attributeIndex, $categories, $count) {
                $enumTag->setAttribute($attribute);
                $enumTag->setTag($this->calculateTag($attributeIndex * $count + $i, $categories));
            }
        );
    }

    public function createMultiEnumAttributes(int $count, ?SymfonyStyle $io = null, ?OutputInterface $output = null): void
    {
        $io?->info('Creating multi enum attributes...');

        $em = $this->em;
        $attributesCount = $em->getRepository(MultiEnumAttribute::class)->count();
        $attributes = $em->getRepository(MultiEnumAttribute::class)->createQueryBuilder('e')->select()->getQuery();

        $progress = null;
        if ($output) {
            $progress = new ProgressBar($output, $attributesCount);
            $progress->start();
        }

        $this->batchEntitySaver->save($io, $progress, $attributes->toIterable(), MultiEnumAttribute::class, $attributesCount, $count, true,
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
            $this->createMultiEnumCategories($attribute, $i, 15);
            $i++;
        }

        $em->flush();
        $em->clear();
    }

    public function createMultiEnumValues(MultiEnumAttribute $attribute, int $attributeIndex, int $count): void
    {
        $em = $this->em;
        $values = $em->getRepository(MultiEnumValue::class)->findBy(['attribute' => $attribute]);

        $this->batchEntitySaver->save(null, null, $values, MultiEnumValue::class, count($values), $count, false,
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

    public function createMultiEnumTags(MultiEnumAttribute $attribute, int $attributeIndex, int $count): void
    {
        $em = $this->em;

        $tags = $em->getRepository(Tag::class)->findAll();
        $em->getRepository(MultiEnumTag::class)->createQueryBuilder('e')
            ->delete()->where("e.attribute = {$attribute->getId()}")->getQuery()->execute();

        $this->batchEntitySaver->save(null, null, [], MultiEnumTag::class, 0, $count, false,
            function (int $i, MultiEnumTag $enumTag) use ($em, $attribute, $attributeIndex, $tags, $count) {
                $enumTag->setAttribute($attribute);
                $enumTag->setTag($this->calculateTag($attributeIndex * $count + $i, $tags));
            }
        );
    }

    public function createMultiEnumCategories(MultiEnumAttribute $attribute, int $attributeIndex, int $count): void
    {
        $em = $this->em;

        $categories = $em->getRepository(Category::class)->findAll();
        $em->getRepository(MultiEnumCategory::class)->createQueryBuilder('e')
            ->delete()->where("e.attribute = {$attribute->getId()}")->getQuery()->execute();

        $this->batchEntitySaver->save(null, null, [], MultiEnumCategory::class, 0, $count, false,
            function (int $i, MultiEnumCategory $multiEnumCategory) use ($em, $attribute, $attributeIndex, $categories, $count) {
                $multiEnumCategory->setAttribute($attribute);
                $multiEnumCategory->setTag($this->calculateTag($attributeIndex * $count + $i, $categories));
            }
        );
    }

    public function createProducts(int $count, ?SymfonyStyle $io = null, ?OutputInterface $output = null): void
    {
        $io?->info('Creating products...');

        $em = $this->em;
        $productsCount = $em->getRepository(Product::class)->count();
        $products = $em->getRepository(Product::class)->createQueryBuilder('e')->select()->getQuery();

        $progress = null;
        if ($output) {
            $progress = new ProgressBar($output, $count);
            $progress->start();
        }

        $categories = $em->getRepository(Category::class)->findAll();
        $tags = $em->getRepository(Tag::class)->findAll();

        $this->batchEntitySaver->save($io, $progress, $products->toIterable(), Product::class, $productsCount, $count, true,
            function (int $i, Product $product)  use (&$categories, &$tags, $em) {
                $product->setName('Product ' . $i);
                $this->resetProductTags($i, $product, $categories, $tags);
                $this->setEnumValues($i, $product);
                $this->setMultiEnumValues($i, $product);
                $this->setStringValues($i, $product);
                $this->setMultiStringValues($i, $product);
            },
            function () use (&$categories, &$tags, $em) {
                $categories = $em->getRepository(Category::class)->findAll();
                $tags = $em->getRepository(Tag::class)->findAll();
            }
        );
    }

    public function setEnumValues(int $i, Product $product): void
    {
        $options = $this->eavOptionsResolver->resolve(EnumEav::class);;
        $enumAttributes = $this->eavAttributeFinder->findAllowed($product, $options);
        $enumAttributes = $this->calculateTags($i, $enumAttributes);
        $items = [];
        foreach ($enumAttributes as $attribute) {
            $value = $this->calculateTag($i, $attribute->getValues());
            $items[] = ['attribute' => $attribute->getId(), 'value' => $value];
        }

        $this->eavInverter->invert($product, $items, $product->getEnumEavs(), $options);
    }

    public function setStringValues(int $i, Product $product): void
    {
        $options = $this->eavOptionsResolver->resolve(StringEav::class);;
        $stringAttributes = $this->eavAttributeFinder->findAllowed($product, $options);
        $items = [];
        foreach ($stringAttributes as $attribute) {
            $n = $i % count($stringAttributes);
            $value = 'StringValue ' . $attribute->getId() . '-' . $n;
            $items[] = ['attribute' => $attribute->getId(), 'value' => $value];
        }

        $this->eavInverter->invert($product, $items, $product->getStringEavs(), $options);
    }

    public function setMultiStringValues(int $i, Product $product): void
    {
        $options = $this->eavOptionsResolver->resolve(MultiStringEav::class);;
        $multiStringAttributes = $this->eavAttributeFinder->findAllowed($product, $options);
        $items = [];
        foreach ($multiStringAttributes as $attribute) {
            $values = [];
            $n = $i % count($multiStringAttributes);
            for ($j = 1; $j <= 3; $j++) {
                $values[] = 'MultiStringValue ' . $attribute->getId() . '-' . $n . '-' . $j;
            }
            $items[] = ['attribute' => $attribute->getId(), 'values' => $values];
        }

        $this->eavInverter->invert($product, $items, $product->getMultiStringEavs(), $options);
    }

    public function setMultiEnumValues(int $i, Product $product): void
    {
        $options = $this->eavOptionsResolver->resolve(MultiEnumEav::class);;
        $multiEnumAttributes = $this->eavAttributeFinder->findAllowed($product, $options);
        $multiEnumAttributes = $this->calculateTags($i, $multiEnumAttributes);
        $items = [];
        foreach ($multiEnumAttributes as $attribute) {
            $values = $this->calculateTags($i, $attribute->getValues());
            $items[] = ['attribute' => $attribute->getId(), 'values' => $values];
        }

        $this->eavInverter->invert($product, $items, $product->getMultiEnumEavs(), $options);
    }

    public function resetProductTags(int $index, Product $product, array &$categories, array &$tags): void
    {
        $productCategory = $this->calculateTag($index, $categories);
        $productTags = $this->calculateTags($index, $tags);

        $product->setCategory($productCategory);
        $product->getTags()->clear();
        foreach ($productTags as $productTag) {
            $product->addTag($productTag);
        }
    }

    public function calculateTag(int $index, iterable|\Countable $tags): object
    {
        return $tags[$index % count($tags)];
    }

    public function calculateTags(int $index, iterable|\Countable $tags): array
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
}
