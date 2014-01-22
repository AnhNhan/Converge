<?php
namespace Codeception\Module;

class FixtureHelper extends \Codeception\Module
{
    // Adapted from erusev\Parsedown test
    function provider($dir, $ext, $exp = null)
    {
        $provider = array();

        $path = substr($dir, -1) == DIRECTORY_SEPARATOR ? $dir : $dir . '/';

        $DirectoryIterator = new \DirectoryIterator($path);

        foreach ($DirectoryIterator as $Item)
        {
            if ($Item->isFile())
            {
                $filename = $Item->getFilename();

                $extension = pathinfo($filename, PATHINFO_EXTENSION);

                if ($extension !== $ext)
                    continue;

                $basename = $Item->getBasename('.' . $ext);

                $markdown = file_get_contents($path . '.' . $ext);

                if (!$markdown)
                    continue;

                if ($exp) {
                    $expected_markup = file_get_contents($path . $basename . '.' . $exp);
                    $expected_markup = str_replace("\r\n", "\n", $expected_markup);
                    $expected_markup = str_replace("\r", "\n", $expected_markup);
                } else {
                    $parts = preg_split("/\n@@[-]+@@\n/", $markdown);
                    if (count($parts)) {
                        throw new \Exception("File {$filename} is in the wrong format!");
                    }

                    $markdown = $parts[0];
                    $expected_markup = $parts[1];
                }

                $provider [] = array($markdown, $expected_markup);
            }
        }

        return $provider;
    }
}
