<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Product\Attribute\EnumAttributeCrudController;
use App\Controller\Admin\Product\Attribute\MultiEnumAttributeCrudController;
use App\Controller\Admin\Product\Attribute\MultiStringAttributeCrudController;
use App\Controller\Admin\Product\Attribute\StringAttributeCrudController;
use App\Controller\Admin\Product\CategoryCrudController;
use App\Controller\Admin\Product\ProductCrudController;
use App\Controller\Admin\Product\TagCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct()
    {
        ini_set('memory_limit', '256M');
    }

    public function index(): Response
    {
        return $this->redirectToRoute('admin_product_index');
    }

    public function configureAssets(): Assets
    {
        $assets = parent::configureAssets();
        $assets
            ->addCssFile('styles/admin.css')
            ->addCssFile('bundles/maxkaineav/styles/compact_ea_collection.css');

        return $assets;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EavBundle');
    }

    public function configureCrud(): Crud
    {
        $crud = parent::configureCrud();
        $crud->setFormThemes(['@Eav/easy-admin/theme.html.twig', '@EasyAdmin/crud/form_theme.html.twig']);

        return $crud;
    }

    public function configureActions(): Actions
    {
        $actions = parent::configureActions();

        return $actions;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::subMenu('Product', 'fas fa-list')->setSubItems([
            MenuItem::linkTo(ProductCrudController::class, 'Product list', 'fas fa-list'),
            MenuItem::linkTo(CategoryCrudController::class, 'Category list', 'fas fa-list'),
            MenuItem::linkTo(TagCrudController::class, 'Tag list', 'fas fa-list'),
            MenuItem::linkTo(StringAttributeCrudController::class, 'String Attributes', 'fas fa-list'),
            MenuItem::linkTo(MultiStringAttributeCrudController::class, 'Multi string Attributes', 'fas fa-list'),
            MenuItem::linkTo(EnumAttributeCrudController::class, 'Enum Attributes', 'fas fa-list'),
            MenuItem::linkTo(MultiEnumAttributeCrudController::class, 'Multi enum Attributes', 'fas fa-list'),
        ]);
    }
}
