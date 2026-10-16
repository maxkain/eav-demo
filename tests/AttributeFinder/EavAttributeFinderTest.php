<?php

namespace App\Tests\AttributeFinder;

use App\Entity\Product\Attribute\MultiEnumEav;
use App\Entity\Product\Product;
use App\Testing\AppKernelTestCase;
use Maxkain\EavBundle\AttributeFinder\EavAttributeFinderInterface;

class EavAttributeFinderTest extends AppKernelTestCase
{
    public function testAttributeFinder(): void
    {
        /** @var EavAttributeFinderInterface $attributeFinder */
        $attributeFinder = $this->getContainer()->get(EavAttributeFinderInterface::class);
        $product = $this->em->getRepository(Product::class)->find(23);
        $options = $this->resolveOptions(MultiEnumEav::class);
        $attributes = $attributeFinder->findAllowed($product, $options);
        $this->assertCount(8, $attributes);
    }
}
