<?php

namespace App\Controller\Admin\Product\Attribute;

use App\Entity\Product\Attribute\EnumAttribute;
use App\Entity\Product\Attribute\MultiEnumAttribute;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MultiEnumAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MultiEnumAttribute::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addColumn(6),
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            CollectionField::new('values')->useEntryCrudForm()->renderExpanded()
                ->addCssClass('compact-ea-collection')
                ->setFormTypeOption('entry_options', ['block_prefix' => 'compact_ea_collection_entry', 'label' => null])
            ,
            FormField::addColumn(6),
            BooleanField::new('forAllTags'),
            CollectionField::new('tags')->useEntryCrudForm()->renderExpanded()
                ->addCssClass('compact-ea-collection')
                ->setFormTypeOption('entry_options', ['block_prefix' => 'compact_ea_collection_entry'])
            ,
            BooleanField::new('forAllCategories'),
            CollectionField::new('categories')->useEntryCrudForm()->renderExpanded()
                ->addCssClass('compact-ea-collection')
                ->setFormTypeOption('entry_options', ['block_prefix' => 'compact_ea_collection_entry'])
        ];
    }
}
