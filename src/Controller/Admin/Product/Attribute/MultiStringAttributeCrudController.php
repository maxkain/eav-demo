<?php

namespace App\Controller\Admin\Product\Attribute;

use App\Entity\Product\Attribute\MultiStringAttribute;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MultiStringAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MultiStringAttribute::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
        ];
    }
}
