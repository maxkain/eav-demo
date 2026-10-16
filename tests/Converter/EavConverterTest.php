<?php

namespace App\Tests\Converter;

use App\Entity\Product\Attribute\MultiEnumEav;
use App\Entity\Product\Product;
use App\Testing\AppKernelTestCase;
use Maxkain\EavBundle\Converter\EavMultipleOutputItem;

class EavConverterTest extends AppKernelTestCase
{
    public function testEavConverter(): void
    {
        $product = $this->em->getRepository(Product::class)->find(23);
        $options = $this->resolveOptions(MultiEnumEav::class);
        $items = $this->getEavConverter()->convert($product->getMultiEnumEavs(), $options);
        $this->assertCount(7, $items);
        foreach ($items as $item) {
            $this->assertInstanceOf(EavMultipleOutputItem::class, $item);
        }

        $this->assertSame(1, $items[0]->attribute);
        $this->assertSame('MultiEnumAttribute 1', $items[0]->attributeName);
        $this->assertSame([3, 4, 5], $items[0]->values);
        $this->assertSame(['MultiEnumValue 1-3', 'MultiEnumValue 1-4', 'MultiEnumValue 1-5'], $items[0]->valueTitles);
    }

    public function testEavConverterWithArrayOutput(): void
    {
        $product = $this->em->getRepository(Product::class)->find(23);
        $options = $this->resolveOptions('productMultiEnumSecondary');

        $items = $this->getEavConverter()->convert($product->getMultiEnumEavs(), $options);
        $this->assertCount(7, $items);
        foreach ($items as $item) {
            $this->assertIsArray($item);
        }

        $this->assertSame(1, $items[0]['attribute']);
        $this->assertSame('MultiEnumAttribute 1', $items[0]['attributeName']);
        $this->assertSame([3, 4, 5], $items[0]['values']);
        $this->assertSame(['MultiEnumValue 1-3', 'MultiEnumValue 1-4', 'MultiEnumValue 1-5'], $items[0]['valueTitles']);
    }
}
