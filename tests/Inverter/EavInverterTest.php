<?php

namespace App\Tests\Inverter;

use App\Entity\Product\Attribute\EnumEav;
use App\Entity\Product\Attribute\MultiEnumAttribute;
use App\Entity\Product\Attribute\MultiEnumEav;
use App\Entity\Product\Attribute\MultiEnumValue;
use App\Entity\Product\Attribute\MultiStringEav;
use App\Entity\Product\Attribute\StringEav;
use App\Entity\Product\Product;
use App\Testing\AppKernelTestCase;
use Maxkain\EavBundle\Contracts\Entity\EavValueInterface;
use Maxkain\EavBundle\Inverter\EavMultipleInputItem;
use Maxkain\EavBundle\Inverter\EavSingularInputItem;

class EavInverterTest extends AppKernelTestCase
{
    public function testMultiEnumInvertWithObjectInputItem(): void
    {
        $product = $this->em->find(Product::class, 23);
        $inputItem = [new EavMultipleInputItem(9, [81, 82])];
        $this->multiEnumInvertSaveTest($product, $inputItem, fn(Product $product) => $product->getMultiEnumEavs());
    }

    public function testMultiEnumInvertWithManyAttributes(): void
    {
        $product = $this->em->find(Product::class, 23);
        $inputItem = [
            new EavMultipleInputItem(9, [81, 82]),
            new EavMultipleInputItem(2, [11, 20]),
        ];
        $attributeValues = $this->invertSave($product, $inputItem, MultiEnumEav::class,
            fn(Product $product) => $product->getMultiEnumEavs());
        $this->assertCount(2, $attributeValues);
        $this->assertArrayHasKey(9, $attributeValues);
        $this->assertArrayHasKey(2, $attributeValues);
        $this->assertCount(2, $attributeValues[9]);
        $this->assertCount(2, $attributeValues[2]);
        $this->assertArrayHasKey(81, $attributeValues[9]);
        $this->assertArrayHasKey(82, $attributeValues[9]);
        $this->assertArrayHasKey(11, $attributeValues[2]);
        $this->assertArrayHasKey(20, $attributeValues[2]);
    }

    public function testMultiEnumAddOnly(): void
    {
        $product = $this->em->find(Product::class, 23);
        $oldAttributeValues = $this->getAttributeValues($product->getMultiEnumEavs());

        $inputItem = ['attribute' => 9, 'values' => [81, 82]];
        $attributeValues = $this->invertSave($product, [$inputItem], MultiEnumEav::class,
            fn(Product $product) => $product->getMultiEnumEavs(), true);

        foreach ($inputItem['values'] as $value) {
            $oldAttributeValues[$inputItem['attribute']][$value] = true;
        }

        foreach ($attributeValues as $attribute => $values) {
            $this->assertArrayHasKey($attribute, $oldAttributeValues);
            foreach ($values as $value => $none) {
                $this->assertArrayHasKey($value, $oldAttributeValues[$attribute]);
            }
        }
    }

    public function testMultiEnumInvertWithArrayInputItem(): void
    {
        $product = $this->em->find(Product::class, 23);
        $inputItem = ['attribute' => 9, 'values' => [81, 82]];
        $this->multiEnumInvertSaveTest($product, [$inputItem], fn(Product $product) => $product->getMultiEnumEavs());
    }

    public function testMultiEnumInvertWithAllObjects(): void
    {
        $attribute = $this->em->find(MultiEnumAttribute::class, 9);
        $value1 = $this->em->find(MultiEnumValue::class, 81);
        $value2 = $this->em->find(MultiEnumValue::class, 82);
        $product = $this->em->find(Product::class, 23);
        $inputItem = new EavMultipleInputItem($attribute, [$value1, $value2]);
        $this->multiEnumInvertSaveTest($product, [$inputItem], fn(Product $product) => $product->getMultiEnumEavs());
    }

    public function testMultiEnumInvertWithObjectsAndArrayInputItem(): void
    {
        $attribute = $this->em->find(MultiEnumAttribute::class, 9);
        $value1 = $this->em->find(MultiEnumValue::class, 81);
        $value2 = $this->em->find(MultiEnumValue::class, 82);
        $product = $this->em->find(Product::class, 23);
        $inputItem = ['attribute' => $attribute, 'values' => [$value1, $value2]];
        $this->multiEnumInvertSaveTest($product, [$inputItem], fn(Product $product) => $product->getMultiEnumEavs());
    }

    public function testEnumInvertWithObjectInputItem(): void
    {
        $product = $this->em->find(Product::class, 23);
        $inputItem = [new EavSingularInputItem(1, 1)];
        $this->enumInvertSaveTest($product, $inputItem, fn(Product $product) => $product->getEnumEavs());
    }

    public function testStringInvertWithObjectInputItem(): void
    {
        $product = $this->em->find(Product::class, 23);
        $inputItem = [new EavSingularInputItem(1, 'testValue1')];
        $this->stringInvertSaveTest($product, $inputItem, fn(Product $product) => $product->getStringEavs());
    }

    public function testMultiStringInvertWithObjectInputItem(): void
    {
        $product = $this->em->find(Product::class, 23);
        $inputItem = [new EavMultipleInputItem(1, ['testValue1', 'testValue2'])];
        $this->multiStringInvertSaveTest($product, $inputItem, fn(Product $product) => $product->getMultiStringEavs());
    }

    private function enumInvertSaveTest(mixed $entity, mixed $items, callable $getEavs, bool $addOnly = false): void
    {
        $attributeValues = $this->invertSave($entity, $items, EnumEav::class, $getEavs, $addOnly);
        $this->assertCount(1, $attributeValues);
        $this->assertArrayHasKey(1, $attributeValues);
        $this->assertCount(1, $attributeValues[1]);
        $this->assertArrayHasKey(1, $attributeValues[1]);
    }

    private function multiEnumInvertSaveTest(mixed $entity, mixed $items, callable $getEavs, bool $addOnly = false): array
    {
        $attributeValues = $this->invertSave($entity, $items, MultiEnumEav::class, $getEavs, $addOnly);
        $this->assertCount(1, $attributeValues);
        $this->assertArrayHasKey(9, $attributeValues);
        $this->assertCount(2, $attributeValues[9]);
        $this->assertArrayHasKey(81, $attributeValues[9]);
        $this->assertArrayHasKey(82, $attributeValues[9]);

        return $attributeValues;
    }

    private function stringInvertSaveTest(mixed $entity, mixed $items, callable $getEavs, bool $addOnly = false): void
    {
        $attributeValues = $this->invertSave($entity, $items, StringEav::class, $getEavs, $addOnly);
        $this->assertCount(1, $attributeValues);
        $this->assertArrayHasKey(1, $attributeValues);
        $this->assertCount(1, $attributeValues[1]);
        $this->assertArrayHasKey('testValue1', $attributeValues[1]);
    }

    private function multiStringInvertSaveTest(mixed $entity, mixed $items, callable $getEavs, bool $addOnly = false): void
    {
        $attributeValues = $this->invertSave($entity, $items, MultiStringEav::class, $getEavs, $addOnly);
        $this->assertCount(1, $attributeValues);
        $this->assertArrayHasKey(1, $attributeValues);
        $this->assertCount(2, $attributeValues[1]);
        $this->assertArrayHasKey('testValue1', $attributeValues[1]);
        $this->assertArrayHasKey('testValue2', $attributeValues[1]);
    }

    private function invertSave(mixed $entity, mixed $items, string $eavFqcn, callable $getEavs, bool $addOnly = false): array
    {
        $options = $this->resolveOptions($eavFqcn);
        $output = $this->getEavInverter()->invert($entity, $items, $getEavs($entity), $options, $addOnly);
        $this->assertTrue($output->isValid());
        $this->em->flush();
        $this->em->clear();

        $entity = $this->em->find(get_class($entity), $entity->getId());

        return $this->getAttributeValues($getEavs($entity));
    }

    private function getAttributeValues(iterable $eavs): array
    {
        $attributeValues = [];
        foreach ($eavs as $eav) {
            $value = $eav->getValue();
            $value = $value instanceof EavValueInterface ? $value->getId() : $value;
            $attributeValues[$eav->getAttribute()->getId()][$value] = true;
        }

        return $attributeValues;
    }
}
