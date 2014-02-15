<?php
namespace AnhNhan\ModHub\Modules\Database\Command;

use AnhNhan\ModHub;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DoctrineManager extends AbstractDbCommand
{
    protected function configure()
    {
        $this
            ->setName("doctrine:manage")
            ->setDefinition($this->createDefinition())
            ->setDescription("Compiles static resources")
        ;
    }

    private function createDefinition()
    {
        return new InputDefinition(array(
            new InputArgument("applications", InputArgument::REQUIRED, "The application(s) to manage. Comma-separated."),
            new InputArgument("action", InputArgument::REQUIRED, "What action should be done. [rebuild]"),
            // new InputOption("preview", null, InputOption::VALUE_NONE, "Don't. Move."),
        ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apps = $input->getArgument("applications");
        $action = $input->getArgument("action");
        $action = strtolower(trim($action));

        $allowed_actions = [
            "rebuild" => true,
            "create"  => true,
        ];

        if (!isset($allowed_actions[$action])) {
            throw new \InvalidArgumentException("Not supported action: $action");
        }

        foreach (explode(",", $apps) as $app) {
            $app = trim($app);

            $output->writeln(str_repeat("-", 79));
            $output->writeln(str_pad("-- $app  ", 79, "-"));
            $output->writeln(str_repeat("-", 79));
            $output->writeln("");
            switch ($action) {
                case "rebuild":
                    $output->writeln("Dropping old tables.");
                    $this->dropTables($app, $output);
                    // Fallthrough
                case "create":
                    $output->writeln("Creating tables.");
                    $this->createTables($app, $output);

                    $output->writeln("Done.");
                    break;
            }
        }
    }
}
