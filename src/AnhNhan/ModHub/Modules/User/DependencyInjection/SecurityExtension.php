<?php
namespace AnhNhan\ModHub\Modules\User\DependencyInjection;

use AnhNhan\ModHub\Modules\User\UserApplication;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/*
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
 class SecurityExtension implements ExtensionInterface
 {
    public function getAlias()
    {
        return "user-security";
    }

    public function load(array $config, ContainerBuilder $container)
    {
        $config = array_mergev($config);
        $container->setParameter("security.provider_key", $config["providerKey"]);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../resources/')
        );
        $loader->load('security-services.yml');
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