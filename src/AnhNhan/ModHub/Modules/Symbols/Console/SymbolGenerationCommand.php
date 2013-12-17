<?php
namespace AnhNhan\ModHub\Modules\StaticResources\Console;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Symbols\Generator\SymbolGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use YamwLibs\Functions\FileFunc;
use YamwLibs\Infrastructure\Printers\ArrayPrinter;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class SymbolGenerationCommand extends AbstractSymbolsCommand
{
    protected function configure()
    {
        $this
            ->setName("symbols:generate")
            ->setDefinition($this->createDefinition())
            ->setDescription("Generate symbol definition list");
    }

    private function createDefinition()
    {
        return new InputDefinition(array(
            new InputOption("no-git", null, InputOption::VALUE_NONE, "Don't use Git to discover files"),
        ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $useGit = !$input->getOption("no-git");

        if ($useGit) {
            $output->writeln("Using Git to discover files");
            $files = $this->discoverFilesGit(ModHub\get_root_super());
            $files = preg_grep("/\\.php$/", $files);
        } else {
            $output->writeln("Not using Git to discover files");
            $files = $this->discoverFiles(ModHub\get_root());
        }

        $fileCount = count($files);
        $output->writeln("Analyzing $fileCount files...");

        // Now begins the cool part :D
        $symbolGenerator = new SymbolGenerator(ModHub\path());
        $skipped = array();

        $filesToBeParsed = array();
        foreach ($files as $file) {
            if (preg_match("/Test\\.php$/i", $file)) {
                echo "S";
                $skipped[] = $file;
                continue;
            }
            $filesToBeParsed[] = $file;
        }
        $symbolGenerator->addFiles($filesToBeParsed);

        $symbolGenerator->onFileTraverse(function ($fileName) use ($output) {
            $output->write(".");
        });

        $symbolGenerator->start();

        $symbolTree = $symbolGenerator->getTree();

        $output->writeln("");

        $output->writeln("Writing to disk...");

        $this->printToDisk($symbolTree->toSymbolMap());

        $output->writeln("Successfully wrote to disk!");
    }

    private function printToDisk($symbolTree)
    {
        $arrayPrinter = new ArrayPrinter();

        ob_start();
        echo <<<EOT
<?php
// -----------------------------------------------------------------------------
/**
 *  This file was generated by the SymbolGenerator(tm)
 *  Would be cool if you wouldn't edit it, as that would sure break things
 *
 *  To re-generate this file, run `php -f scripts/generate_symbol_list.php`
 *
 *  Thank you
 *  @love Anh Nhan <anhnhan@outlook.com>
 *
 *  @generated
 */
// -----------------------------------------------------------------------------
EOT;

        $symbolMapString = ob_get_clean();
        $symbolMapString .= $arrayPrinter->printForFile($symbolTree);

        return file_put_contents(
            ModHub\path("__symbol_map__.php"),
            $symbolMapString
        );
    }

    private function generateSymbolTree($nodes)
    {
        $symbolTreeGenerator = new SymbolTreeGenerator($nodes);
        $symbolTreeGenerator->generate();
        return $symbolTreeGenerator->getGeneratedTree();
    }

    private function discoverFilesGit($path)
    {
        $curDir = getcwd();

        $retVal = -1;
        chdir($path);
        exec('git ls-files --full-name -c src/', $rawFiles, $retVal);
        if ($retVal !== 0) {
            throw new \Exception("Git failed!");
        }
        $files = FileFunc::sanitizeStringsFromPrefix(
            $rawFiles,
            'src/'
        );

        // Change back to original directory
        chdir($curDir);

        return $files;
    }

    private function discoverFiles($path)
    {
        $rawFiles = FileFunc::recursiveScanForDirectories($path, '\.php');
        $files = FileFunc::sanitizeStringsFromPrefix($rawFiles, $path);
        return $files;
    }
}
