<?php
namespace AnhNhan\ModHub\Modules\StaticResources\Console;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Console\ConsoleCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use YamwLibs\Infrastructure\Printers\ArrayPrinter;
use YamwLibs\Infrastructure\ResMgmt\Builders\CssBuilder;
use YamwLibs\Functions\FileFunc;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class CompileCommand extends ConsoleCommand
{
    protected function configure()
    {
        $this
            ->setName("rsrc:compile")
            ->setDefinition($this->createDefinition())
            ->setDescription("Compiles static resources");
    }

    private function createDefinition()
    {
        return new InputDefinition(array(
            new InputOption("js", null, InputOption::VALUE_OPTIONAL, "Compile JS"),
            new InputOption("css", null, InputOption::VALUE_OPTIONAL, "Compile LESS/CSS"),
        ));
    }

    private static $path_resource;
    private static $path_cache;
    private static $path_resource_map;
    private static $path_css;
    private static $path_js;

    private $resMap = array(
        "css" => [],
        "js" => [],
        "pck" => [],
    );

    private $input;
    private $output;

    public function __construct()
    {
        parent::__construct();
        self::$path_resource = ModHub\get_root_super() . "resources" . DIRECTORY_SEPARATOR;
        self::$path_cache = ModHub\get_root_super() . "cache" . DIRECTORY_SEPARATOR;
        self::$path_resource_map = ModHub\get_root() . "__resource_map__.php";
        self::$path_css = self::$path_resource . "css" . DIRECTORY_SEPARATOR;
        self::$path_js = self::$path_resource . "js" . DIRECTORY_SEPARATOR;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $js = $input->getOption("js");
        $css = $input->getOption("css");

        if (!($js && $css)) {
            $js = true;
            $css = true;
        }

        $this->input = $input;
        $this->output = $output;

        $output->writeln("Static resource compiler");
        $output->writeln("");

        $output->writeln("Resource path:\t" . self::$path_resource);
        $output->writeln("Cache path:\t" . self::$path_cache);
        $output->writeln("Resource map path:\t" . self::$path_resource_map);
        $output->writeln("CSS path:\t" . self::$path_css);
        $output->writeln("JS path:\t" . self::$path_js);
        $output->writeln("");

        if (!file_exists(self::$path_cache)) {
            $this->output->writeln("Cache dir does not exist, creating.");
            mkdir(self::$path_cache);
        }

        if ($css) {
            $this->output->writeln("Will now process LESS/CSS");

            $cssBuilderClass = 'YamwLibs\Infrastructure\ResMgmt\Builders\CssBuilder';
            $this->genericBuild(self::$path_css, "\\.(less|css)", $cssBuilderClass);
        }

        if ($js) {
            $this->output->writeln("Skipping on JS resources, since they are not supported yet.");
            $this->output->writeln("");
        }

        $this->buildPackFiles();

        $this->printResMap($this->resMap, self::$path_resource_map);
    }

    private function genericBuild($dir, $ext, $builder)
    {
        // First get all interesting resource files
        $rawFiles = FileFunc::recursiveScanForDirectories($dir, "$ext");
        $files = FileFunc::sanitizeStringsFromPrefix($rawFiles, $dir);

        $prependFiles = preg_grep("/^prepend\\//", $files);
        foreach ($prependFiles as $prependFile) {
            unset($files[array_search($prependFile, $files)]);
        }

        $this->output->writeln("Found " . count($rawFiles) . " in total for processing.");
        $this->output->writeln("Of these " . count($prependFiles) . " are prepend files.");
        $this->output->writeln("");

        $this->output->writeln("Using the following prepend files:");
        $prependContents = [];
        foreach ($prependFiles as $prependFile) {
            $this->output->writeln("  - " . $prependFile);
            $prependContents[] = file_get_contents($dir . $prependFile);
        }
        $prependContents = implode("\n\n", $prependContents);
        $this->output->writeln("");

        $this->output->writeln("Status:");

        foreach ($files as $fileName) {
            $cssPath = $dir . $fileName;
            $resName = preg_replace("/{$ext}$/", "", $fileName);
            $resName = preg_replace(
                "/([\\|\/])+/",
                "-",
                $resName
            );

            $cssEntry = array(
                "name" => $resName,
                "path" => $fileName,
                "hash" => hash_file("crc32", $cssPath),
            );

            // TODO: Replace file URIs to CDN URIs
            $resContents = file_get_contents($cssPath);
            $resContents = $builder::buildString(
                $prependContents . $resContents
            );

            if (file_put_contents(self::$path_cache . $resName, $resContents)) {
                $this->output->writeln("  - " . str_pad($resName, 40) . " [x]");
            } else {
                $this->output->writeln("  - " . str_pad($resName, 40) . " [ ]");
            }

            $this->resMap["css"][$resName] = $cssEntry;
        }

        $this->output->writeln("");
    }

    private function buildPackFiles()
    {
        $rawPckFiles = FileFunc::recursiveScanForDirectories(self::$path_resource, "\\.json");
        $pckFiles = preg_grep(
            "/json$/",
            FileFunc::sanitizeStringsFromPrefix($rawPckFiles, self::$path_resource)
        );
        if ($pckFiles) {
            $this->output->writeln("Will now process pack files.");
            foreach ($pckFiles as $pck) {
                $pckFile = json_decode(file_get_contents(self::$path_resource . $pck));

                if (!$pckFile) {
                    $this->output->writeln("Skipping " . $pck);
                    continue;
                }
                $pckFile = (array)$pckFile;
                $name = $pckFile["name"];

                $contents = [];
                $fileContents = [];
                foreach ($pckFile["contents"] as $contentName) {
                    $contents[$contentName] = $this->resMap[$pckFile["type"]][$contentName]["hash"];
                    $fileContents[] = file_get_contents(self::$path_cache . $contentName);
                }
                file_put_contents(
                    self::$path_cache . $name,
                    implode("\n\n", $fileContents)
                );

                $this->resMap["pck"][$name] = array(
                    "name" => $name,
                    "type" => "pck",
                    "hash" => hash_hmac(
                        "crc32",
                        implode("", $contents),
                        "random string"
                    ),
                    "contents" => $contents,
                );
                $this->output->writeln("  - " . $name);
            }
        }
        $this->output->writeln("");
    }

    private function printResMap(array $resMap, $path)
    {
        ob_start();
        echo <<<EOT
<?php
// -----------------------------------------------------------------------------
/**
 *  This file was generated for the static resource management system
 *  Would be cool if you wouldn"t edit it, as that would sure break things
 *
 *  To re-generate this file, run `php -f scripts/generate_resource_map.php`
 *
 *  Thank you
 *  @love Anh Nhan <anhnhan@outlook.com>
 *
 *  @generated
 */
// -----------------------------------------------------------------------------
EOT;

        $arrayPrinter = new ArrayPrinter();
        $resMapString = ob_get_clean();
        $resMapString .= $arrayPrinter->printForFile($resMap);

        $result = file_put_contents(
            $path,
            $resMapString
        );

        if ($result) {
            $this->output->writeln("Successfully wrote to disk!");
        } else {
            $this->output->writeln("Saving the resource map failed!");
        }
    }
}
