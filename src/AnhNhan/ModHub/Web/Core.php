<?php
namespace AnhNhan\ModHub\Web;

use AnhNhan\ModHub;
use YamwLibs\Infrastructure\ResMgmt\ResMgr;
use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bootstrap of the application
 *
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class Core
{
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

    public static function startOverFlowCapturing()
    {
        ob_start();
    }

    public static function endOverFlowCapturing()
    {
        return ob_get_clean();
    }

    public static function get404Page($page)
    {
        $payload = new AnhNhan\ModHub\Web\Application\HtmlPayload;
        $container = new \YamwLibs\Libs\Html\Markup\MarkupContainer;
        $container->push(ModHub\ht("h1", "Failed to find a controller for '$page'"));
        $payload->setPayloadContents($container);
        $payload->setTitle("Page not found");
        return $payload;
    }

    const SERVICE_CONTAINER_NAME = 'AnhNhan\ServiceContainer';

    public static function loadSfDIContainer(
        $className = null,
        $containerName = "default"
    ) {
        if ($className === null) {
            $className = self::SERVICE_CONTAINER_NAME;
        }
        $containerFileName = ModHub\get_root_super() . "cache/container.{$containerName}.php";
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
        return new $className;
    }

    public static function buildSfDIContainer($confDir = "conf/")
    {
        $confDir = ModHub\get_root_super() . $confDir;
        $builder = new ContainerBuilder;

        $builder->setParameter("project.root", ModHub\get_root_super());

        $loader  = new YamlFileLoader($builder, new FileLocator($confDir));
        $loader->load("services.yml");

        $classes = SymbolLoader::getInstance()
            ->getConcreteClassesThatDeriveFromThisOne('Symfony\Component\DependencyInjection\Extension\ExtensionInterface');
        foreach ($classes as $class) {
            $builder->registerExtension(new $class);
        }
        // Process through all registered extensions
        foreach ($builder->getExtensions() as $alias => $_) {
            $builder->loadFromExtension($alias);
        }

        $builder->compile();
        return $builder;
    }
}
