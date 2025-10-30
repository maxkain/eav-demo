<?php

namespace App\Controller\Admin\Product\Attribute;

use App\Entity\Product\Attribute\MultiEnumTag;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class MultiEnumTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MultiEnumTag::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('tag', false)->setColumns('12')
        ];
    }
}
