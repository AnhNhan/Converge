<?php
namespace AnhNhan\Converge\Modules\People\DependencyInjection;

use AnhNhan\Converge\Modules\People\PeopleApplication;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/*
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
 class PeopleExtension implements ExtensionInterface
 {
    public function getAlias()
    {
        return "user-extension";
    }

    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../resources/')
        );
        $loader->load('services.yml');
    }

    /* - ( Anti-XML stuff ) - */

    public function getXsdValidationBasePath()
    {
        return false;
    }

    public function getNamespace()
    {
        return null;
    }
 }
