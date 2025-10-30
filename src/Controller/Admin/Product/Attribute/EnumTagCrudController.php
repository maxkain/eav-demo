<?php

namespace App\Controller\Admin\Product\Attribute;

use App\Entity\Product\Attribute\EnumTag;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class EnumTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EnumTag::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('tag', false)->setColumns('12'),
            BooleanField::new('showInFilter'),
        ];
    }
}
