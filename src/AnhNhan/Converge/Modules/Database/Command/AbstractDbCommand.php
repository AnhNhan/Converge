<?php
namespace AnhNhan\Converge\Modules\Database\Command;

use AnhNhan\Converge;
use AnhNhan\Converge\Console\ConsoleCommand;
use AnhNhan\Converge\Modules\Symbols\SymbolLoader;
use AnhNhan\Converge\Web\Core;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\InputDefinition;
use YamwLibs\Functions\FileFunc;
use Doctrine\ORM\Tools\Console\ConsoleRunner as DoctrineConsole;
use Doctrine\ORM\Version;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class AbstractDbCommand extends ConsoleCommand
{
    private $initialized = false;
    private $container;
    private $appInstanceList = array();
    private $dcConsoleApps = array();

    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    private function thisInitialize()
    {
        if ($this->initialized) {
            return;
        }

        $this->container = Core::loadSfDIContainer();
        $apps = SymbolLoader::getInstance()
            ->getConcreteClassesThatDeriveFromThisOne('AnhNhan\Converge\Web\Application\BaseApplication');
        foreach ($apps as $class_name) {
            $this->appInstanceList[] = new $class_name;
        }
        $this->appInstanceList = mpull($this->appInstanceList, null, "getInternalName");

        $this->initialized = true;
    }

    private function buildDcConsoleAppForApp($appName)
    {
        $app = idx($this->appInstanceList, $appName);
        if (!$app) {
            throw new \Exception("App '{$appName}' does not exist!");
        }
        $app->setContainer($this->container);
        $em = $app->getEntityManager();

        $cli = new ConsoleApplication('Doctrine Command Line Interface', Version::VERSION);
        //$cli->setCatchExceptions(true);
        $cli->setAutoExit(false);
        $cli->setHelperSet(DoctrineConsole::createHelperSet($em));
        DoctrineConsole::addCommands($cli);

        $commands = array(
        );

        $cli->addCommands($commands);

        $this->dcConsoleApps[$appName] = $cli;
        return $cli;
    }

    /**
     * Runs a doctrine console command with the entity manager of an application
     *
     * @param $app    The application name to act on
     * @param $string The command string to pass to the command, e.g. 'orm:schema-tool create --dump-sql'
     *
     * @return array Use `list($output, $return) = $this->runDoctrineConsole(...)`
     *               to retrieve the return value.
     */
    protected function runDoctrineConsole($app, $string, OutputInterface $output = null)
    {
        $this->thisInitialize();
        if (!($dcConsole = idx($this->dcConsoleApps, $app))) {
            $dcConsole = $this->buildDcConsoleAppForApp($app);
        }

        $input = new StringInput($string);
        $output = $output ?: new ConsoleOutput;

        $return = $dcConsole->run($input, $output);
        return array($output, $return);
    }

    protected function logSql($sql, $intent)
    {
        $superpath = Converge\get_root_super();
        if (!(file_exists($superpath . 'log') && is_dir($superpath . 'log'))) {
            mkdir($superpath . 'log');
        }
        if (!(file_exists($superpath . 'log/sql') && is_dir($superpath . 'log/sql'))) {
            mkdir($superpath . 'log/sql');
        }

        $filename = sprintf('%slog/sql/%s-%s.sql', $superpath, date('Y-m-d_H-i-s', time()), $intent);
        return \Filesystem::writeUniqueFile($filename, $sql);
    }

    protected function dropTables($app, $output)
    {
        $command = 'orm:schema-tool:drop';
        $cmdOutput = new BufferedOutput;
        list($sql) = $this->runDoctrineConsole($app, $command . ' --dump-sql', $cmdOutput);
        $sql = $sql->fetch();
        if ($sql) {
            $logpath = $this->logSql($sql, 'drop');
            $output->writeln("Logging Sql: $logpath");
            $output->writeln('Dropping tables.');
            $output->writeln('');

            // Force the drop
            list($cmdOutput) = $this->runDoctrineConsole($app, $command . ' --force', $cmdOutput);
            $output->writeln($cmdOutput->fetch());
            $output->writeln('Dropped');
        } else {
            $output->writeln('Nothing to drop. Skipping.');
            $output->writeln('');
        }
    }

    protected function createTables($app, $output)
    {
        $command = 'orm:schema-tool:create';
        $cmdOutput = new BufferedOutput;
        list($sql) = $this->runDoctrineConsole($app, $command . ' --dump-sql', $cmdOutput, $cmdOutput);
        $sql = $sql->fetch();
        if ($sql) {
            $logpath = $this->logSql($sql, 'create');
            $output->writeln("Logging Sql: $logpath");
            $output->writeln('Creating tables.');
            $output->writeln('');

            // Force the drop
            list($cmdOutput) = $this->runDoctrineConsole($app, $command, $cmdOutput);
            $output->writeln($cmdOutput->fetch());
            $output->writeln('Created.');
        } else {
            $output->writeln('Nothing to create. Skipping.');
            $output->writeln('');
        }
    }

    protected function updateTables($app, $output)
    {
        $command = 'orm:schema-tool:update';
        $cmdOutput = new BufferedOutput;
        list($sql) = $this->runDoctrineConsole($app, $command . ' --dump-sql', $cmdOutput);
        $sql = $sql->fetch();
        if (trim($sql)) {
            $logpath = $this->logSql($sql, 'update');
            $output->writeln("Logging Sql: $logpath");
            $output->writeln('Really updating tables.');
            $output->writeln('');

            // Force the drop
            list($cmdOutput) = $this->runDoctrineConsole($app, $command . ' --force', $cmdOutput);
            $output->writeln($cmdOutput->fetch());
            $output->writeln('Updated.');
        } else {
            $output->writeln('Nothing to update. Skipping.');
            $output->writeln('');
        }
    }
}
