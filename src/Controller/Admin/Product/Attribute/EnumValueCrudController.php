<?php

namespace App\Controller\Admin\Product\Attribute;

use App\Entity\Product\Attribute\EnumValue;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EnumValueCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EnumValue::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', false)->setColumns('12'),
        ];
    }
}
