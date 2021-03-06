<?php
namespace AnhNhan\Converge\Web;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\StaticResources\ResMgr;
use AnhNhan\Converge\Modules\Symbols\SymbolLoader;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class Core
{
    // Helper method, for internal use only
    public static function prepareResponse(
        Request $request,
        Application\HttpPayload $payload,
        $overflow
    ) {
        $contents = $overflow . $payload->getRenderedHttpBody();
        $headers = $payload->getHttpHeaders();
        unset($headers[0]);
        $response = Response::create(
            $contents,
            $payload->getHttpCode(),
            $headers
            )
            ->prepare($request)
        ;

        return $response;
    }

    // Helper methods, onlz for internal use
    public static function startOverFlowCapturing()
    {
        ob_start();
    }

    public static function endOverFlowCapturing()
    {
        return ob_get_clean();
    }

    /**
     * @deprecated, though we do not have a replacement yet
     */
    public static function get404Page($page)
    {
        $payload = new ResponseHtml404;
        $payload->setText("Failed to find a controller for '$page'");
        return $payload;
    }

    const SERVICE_CONTAINER_NAME = 'AnhNhan\ServiceContainer';

    public static function loadBootstrappedSfDIContainer($containerName = "default")
    {
        $container = self::loadSfDIContainer();

        $appList = new \AnhNhan\Converge\Application\ApplicationList($container);
        $container->set('app.list', $appList);

        return $container;
    }

    /**
     * Loads a Symfony\DependencyInjection container from cache. If it does not exist,
     * it will be built fresh from the service configuration.
     */
    public static function loadSfDIContainer(
        $className = null,
        $containerName = "default"
    ) {
        if ($className === null) {
            $className = self::SERVICE_CONTAINER_NAME;
        }
        $containerFileName = Converge\get_root_super() . "cache/container.{$containerName}.php";
        $isDebug = true;
        $configCache = new ConfigCache($containerFileName, $isDebug);

        if (!$configCache->isFresh()) {
            $container = self::buildSfDIContainer();
            $dumper = new \Symfony\Component\DependencyInjection\Dumper\PhpDumper($container);

            if (strpos($className, "\\") !== false) {
                $parts = explode("\\", $className);
                $dumpConfig = array("class" => end($parts));
                array_pop($parts);
                $dumpConfig += array("namespace" => implode("\\", $parts));
            } else {
                $dumpConfig = array("class" => $className);
            }

            $configCache->write(
                $dumper->dump($dumpConfig),
                $container->getResources()
            );
        }

        require_once $containerFileName;
        $container = new $className;
        $container->set(
            "application.user",
            id(new \AnhNhan\Converge\Modules\People\PeopleApplication)->setContainer($container)
        );

        return $container;
    }

    /**
     * Dumb building of a Symfony\DependencyInjection container. This method does not
     * load or cache the container, but re-loads all resources and extensions and processes them.
     *
     * If you want to add an extension, simply extend the `ExtensionInterface` and make sure that
     * it is registered in the symbol map.
     *
     * @param $confDir string The directory containing the configuration files, including
     *                        service configuration.
     *                        This path is relative to the project root.
     *
     * @return ContainerBuilder A ContainerBuilder instance that has undergone compilation (hence
     *                          not worth the trouble of changing it yourself)
     */
    public static function buildSfDIContainer($confDir = "conf/")
    {
        $confDir = Converge\get_root_super() . $confDir;
        $builder = new ContainerBuilder;

        $builder->setParameter("project.root", Converge\get_root_super());

        $classes = SymbolLoader::getInstance()
            ->getConcreteClassesThatImplement('Symfony\Component\DependencyInjection\Extension\ExtensionInterface');
        foreach ($classes as $class) {
            $builder->registerExtension(new $class);
        }

        $loader  = new YamlFileLoader($builder, new FileLocator($confDir));
        $loader->load("services.yml");

        // Process through all registered extensions
        foreach ($builder->getExtensions() as $alias => $_) {
            $builder->loadFromExtension($alias);
        }

        $builder->compile();
        return $builder;
    }
}
