<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Maxkain\EavBundle\Bridge\Doctrine\OrphanedEavs\OrphanedEavsObjectHandler;
use Maxkain\EavBundle\Bridge\Doctrine\OrphanedEavs\OrphanedEavsCleaner;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(OrphanedEavsObjectHandler::class)->public();
    $services->set(OrphanedEavsCleaner::class)->public();
};
