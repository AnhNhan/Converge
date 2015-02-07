<?php
namespace AnhNhan\Converge\Modules\People\DependencyInjection;

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
        $container->setParameter("security.http.login.path", $config["security.http.login.path"]);
        $container->setParameter("security.http.logout.path", $config["security.http.logout.target"]);
        $container->setParameter("security.http.logout.target", $config["security.http.logout.target"]);
        $container->setParameter("session.mongo.collection", $config["session.mongo.collection"]);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../resources/')
        );
        $loader->load('security-services.yml');
        $loader->load('csrf-services.yml');
        $loader->load('session-services.yml');
        $loader->load('http-security-services.yml');
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
