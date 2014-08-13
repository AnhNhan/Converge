<?php
namespace AnhNhan\Converge\Modules\Symbols\Generator;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class SymbolGenerator
{
    private $files = array();
    private $basePath;

    /**
     * @var SymbolTree
     */
    private $tree;

    /**
     * @var \PHPParser_Parser
     */
    private $php_parser;
    /**
     * @var \PHPParser_NodeTraverser
     */
    private $php_parser_traverser;

    /**
     * @var NodeVisitor\SymbolEmitter
     */
    private $symbol_emitter;

    public function __construct($basePath = "", $output = "__symbol_map__.php")
    {
        $this->basePath = $basePath;

        $this->tree = new SymbolTree;

        $this->php_parser = new \PHPParser_Parser(new \PHPParser_Lexer_Emulative);
        $this->php_parser_traverser = new \PHPParser_NodeTraverser;

        $this->php_parser_traverser->addVisitor(new \PHPParser_NodeVisitor_NameResolver);
        $this->symbol_emitter = new NodeVisitor\SymbolEmitter($this->tree);
        $this->php_parser_traverser->addVisitor($this->symbol_emitter);
    }

    public function addFiles(array $files)
    {
        $this->files += $files;
    }

    public function start()
    {
        foreach ($this->files as $fileName) {
            try {
                $contents = file_get_contents($this->basePath . $fileName);
                $nodes = $this->php_parser->parse($contents);

                $this->symbol_emitter->setCurrentFile($fileName);

                $this->php_parser_traverser->traverse($nodes);
                $this->onFileTraverseExec($fileName);
            } catch (\PHPParser_Error $exc) {
                $this->onFileTraverseErrorExec($fileName, $exc);
            }
        }
    }

    public function getTree()
    {
        return $this->tree;
    }

    // Events
    private $onFileTraverseEvents = array();
    public function onFileTraverse($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException();
        }

        $this->onFileTraverseEvents[] = $callable;
    }
    private function onFileTraverseExec($fileName)
    {
        foreach ($this->onFileTraverseEvents as $callable) {
            $callable($fileName);
        }
    }

    private $onFileTraverseErrorEvents = array();
    public function onFileTraverseError($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException();
        }

        $this->onFileTraverseErrorEvents[] = $callable;
    }
    private function onFileTraverseErrorExec($fileName, \PHPParser_Error $error)
    {
        foreach ($this->onFileTraverseErrorEvents as $callable) {
            $callable($fileName, $error);
        }
    }
}
