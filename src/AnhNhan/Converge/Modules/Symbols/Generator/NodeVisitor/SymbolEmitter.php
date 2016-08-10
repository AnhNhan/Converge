<?php
namespace AnhNhan\Converge\Modules\Symbols\Generator\NodeVisitor;

use AnhNhan\Converge\Modules\Symbols\Generator\SymbolTree;

use PhpParser\Node\Stmt\Class_ as PP_Class;
use PhpParser\Node\Stmt\Interface_ as PP_Interface;
use PhpParser\Node\Stmt\Function_ as PP_Function;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class SymbolEmitter extends \PhpParser\NodeVisitorAbstract
{
    /**
     * @var SymbolTree
     */
    private $tree;
    private $currentFile;

    public function __construct(SymbolTree $tree)
    {
        $this->tree = $tree;
    }

    public function setCurrentFile($file)
    {
        $this->currentFile = $file;
    }

    public function leaveNode(\PhpParser\Node $node)
    {
        if ($node instanceof PP_Class) {
            $impls = array();
            if ($node->implements) {
                foreach ($node->implements as $interfaceName) {
                    $impls[] = (string)$interfaceName;
                }
            }

            $this->tree->addClass(
                $node->namespacedName->toString(),
                $this->currentFile,
                $node->extends ? $node->extends->toString() : null,
                $node->implements ? $impls : array(),
                $node->isAbstract()
            );
        } else if ($node instanceof PP_Interface) {
            $this->tree->addInterface(
                $node->namespacedName->toString(),
                $this->currentFile,
                $node->extends ? $node->extends->toString() : null
            );
        } else if ($node instanceof PP_Function) {
            $this->tree->addFunction(
                $node->namespacedName->toString(),
                $this->currentFile
            );
        }
    }
}
