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
            AssociationField::new('category'),
            AssociationField::new('tags'),
            FormField::addTab('Attributes'),
            FormField::addColumn(6),
            $this->eavFieldFactory->create('enumEavs', null, EnumEav::class),
            $this->eavFieldFactory->create('multiEnumEavs', null, MultiEnumEav::class),
            FormField::addColumn(6),
            $this->eavFieldFactory->create('stringEavs', null, StringEav::class),
            $this->eavFieldFactory->create('multiStringEavs', null, MultiStringEav::class),
        ];
    }
}
