<?php

namespace App\Controller\Admin\Product\Attribute;

use App\Entity\Product\Attribute\MultiEnumCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class MultiEnumCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MultiEnumCategory::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('tag', false)->setColumns('12')
        ];
    }
}
