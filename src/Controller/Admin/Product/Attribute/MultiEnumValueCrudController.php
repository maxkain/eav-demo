<?php

namespace App\Controller\Admin\Product\Attribute;

use App\Entity\Product\Attribute\MultiEnumValue;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MultiEnumValueCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MultiEnumValue::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', false)->setColumns('12'),
        ];
    }
}
