<?php
namespace AnhNhan\ModHub;

require_once __DIR__ . "/../../__init__.php";

use AnhNhan\ModHub\Modules\Symbols\SymbolLoader;
use Doctrine\ORM\Tools\Console\ConsoleRunner as DoctrineConsole;
use Doctrine\ORM\Version;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\ArgvInput;

$root = get_root_super();
if (!file_exists($root . "cache/db/") || !is_dir($root . "cache/db/")) {
    mkdir($root . "cache/db/", null, true);
}

$apps = SymbolLoader::getInstance()
    ->getConcreteClassesThatDeriveFromThisOne('AnhNhan\ModHub\Web\Application\BaseApplication');
$appInstanceList = array();
foreach ($apps as $class_name) {
    $appInstanceList[] = new $class_name;
}
$appInstanceList = mpull($appInstanceList, null, "getInternalName");

$initialArgV = $_SERVER['argv'];
$doctrineArgV = array();

$doctrineArgV[] = sdx($initialArgV);
$appName = sdx($initialArgV);
while ($initialArgV) {
    $doctrineArgV[] = sdx($initialArgV);
}

if (empty($appName)) {
    echo "You have to provide an application name as the first argument.";
    exit(1);
} else if (!isset($appInstanceList[$appName])) {
    echo "Application '$appName' does not exist!";
    exit(1);
}

$app = $appInstanceList[$appName];
$app->setContainer(\AnhNhan\ModHub\Web\Core::loadSfDIContainer());
$em = $app->getEntityManager();


$cli = new ConsoleApplication('Doctrine Command Line Interface', Version::VERSION);
$cli->setCatchExceptions(true);
$cli->setHelperSet(DoctrineConsole::createHelperSet($em));
DoctrineConsole::addCommands($cli);

$commands = array(
);

$cli->addCommands($commands);
$cli->run(new ArgvInput($doctrineArgV));
