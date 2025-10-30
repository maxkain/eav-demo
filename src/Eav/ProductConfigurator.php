<?php

namespace App\Eav;

use App\Entity\Product\Attribute\EnumAttribute;
use App\Entity\Product\Attribute\EnumTag;
use App\Entity\Product\Attribute\EnumEav;
use App\Entity\Product\Attribute\EnumValue;
use App\Entity\Product\Attribute\MultiEnumAttribute;
use App\Entity\Product\Attribute\MultiEnumCategory;
use App\Entity\Product\Attribute\MultiEnumEav;
use App\Entity\Product\Attribute\MultiEnumTag;
use App\Entity\Product\Attribute\MultiEnumValue;
use App\Entity\Product\Attribute\MultiStringEav;
use App\Entity\Product\Attribute\MultiStringAttribute;
use App\Entity\Product\Attribute\StringEav;
use App\Entity\Product\Attribute\StringAttribute;
use App\Entity\Product\Category;
use App\Entity\Product\Product;
use App\Entity\Product\Tag;
use Maxkain\EavBundle\Options\EavConfiguratorInterface;
use Maxkain\EavBundle\Options\EavOptions;
use Maxkain\EavBundle\Options\EavOptionsInterface;
use Maxkain\EavBundle\Options\PropertyMapping;

class ProductConfigurator implements EavConfiguratorInterface
{
    /**
     * @return array<int|string, EavOptionsInterface>
     */
    public function configure(): array
    {
        $multiEnum = new EavOptions(
            eavFqcn: MultiEnumEav::class,
            entityFqcn: Product::class,
            attributeFqcn: MultiEnumAttribute::class,
            valueFqcn: MultiEnumValue::class,
            multiple: true,
            tagFqcn: Tag::class,
            attributeTagFqcn: MultiEnumTag::class,
            multipleTags: true,
            propertyMapping: new PropertyMapping(
                entityTags: 'tags',
                attributeForAllTags: 'forAllTags'
            )
        );

        $multiEnumCategory = (clone $multiEnum)
            ->setTagFqcn(Category::class)
            ->setAttributeTagFqcn(MultiEnumCategory::class)
            ->setMultipleTags(false)
            ->setPropertyMapping(new PropertyMapping(
                entityTag: 'category',
                attributeForAllTags: 'forAllCategories'
            ));

        return [
            new EavOptions(
                eavFqcn: EnumEav::class,
                entityFqcn: Product::class,
                attributeFqcn: EnumAttribute::class,
                valueFqcn: EnumValue::class,
                tagFqcn: Category::class,
                attributeTagFqcn: EnumTag::class
            ),
            $multiEnum,
            'multiEnumCategory' => $multiEnumCategory,
            new EavOptions(
                eavFqcn: StringEav::class,
                entityFqcn: Product::class,
                attributeFqcn: StringAttribute::class,
            ),
            new EavOptions(
                eavFqcn: MultiStringEav::class,
                entityFqcn: Product::class,
                attributeFqcn: MultiStringAttribute::class,
                multiple: true
            )
        ];
    }
}
