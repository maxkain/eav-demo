<?php

namespace App\Controller\Admin;

use App\Entity\Product\Attribute\EnumAttribute;
use App\Entity\Product\Attribute\MultiEnumAttribute;
use App\Entity\Product\Attribute\MultiStringAttribute;
use App\Entity\Product\Category;
use App\Entity\Product\Product;
use App\Entity\Product\Attribute\StringAttribute;
use App\Entity\Product\Tag;
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
        $assets->addCssFile('bundles/maxkaineav/styles/compact_ea_collection.css');

        return $assets;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Dev Local');
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
            MenuItem::linkToCrud('Product list', 'fas fa-list', Product::class),
            MenuItem::linkToCrud('Category list', 'fas fa-list', Category::class),
            MenuItem::linkToCrud('Tag list', 'fas fa-list', Tag::class),
            MenuItem::linkToCrud('String Attributes', 'fas fa-list', StringAttribute::class),
            MenuItem::linkToCrud('Multi string Attributes', 'fas fa-list', MultiStringAttribute::class),
            MenuItem::linkToCrud('Enum Attributes', 'fas fa-list', EnumAttribute::class),
            MenuItem::linkToCrud('Multi enum Attributes', 'fas fa-list', MultiEnumAttribute::class),
        ]);
    }
}
