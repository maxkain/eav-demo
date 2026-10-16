<?php

namespace App\Controller\Admin\Product;

use App\Entity\Product\Attribute\EnumEav;
use App\Entity\Product\Attribute\MultiEnumEav;
use App\Entity\Product\Attribute\MultiStringEav;
use App\Entity\Product\Product;
use App\Entity\Product\Attribute\StringEav;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Maxkain\EavBundle\Bridge\EasyAdmin\EavFieldFactory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public function __construct(
        private EavFieldFactory $eavFieldFactory,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Main'),
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            FormField::addFieldset(),
            AssociationField::new('category'),
            AssociationField::new('tags'),
            FormField::addTab('Attributes'),
            FormField::addColumn(6),
            FormField::addFieldset('Enum Eavs')->collapsible()->renderCollapsed(),
            $this->eavFieldFactory->create('enumEavs', false, EnumEav::class),
            FormField::addFieldset('String Eavs')->collapsible()->renderCollapsed(),
            $this->eavFieldFactory->create('stringEavs', false, StringEav::class),
            FormField::addColumn(6),
            FormField::addFieldset('MultiEnum Eavs')->collapsible()->renderCollapsed(),
            $this->eavFieldFactory->create('multiEnumEavs', false, MultiEnumEav::class),
            FormField::addFieldset('MultiString Eavs')->collapsible()->renderCollapsed(),
            $this->eavFieldFactory->create('multiStringEavs', false, MultiStringEav::class),
        ];
    }
}
