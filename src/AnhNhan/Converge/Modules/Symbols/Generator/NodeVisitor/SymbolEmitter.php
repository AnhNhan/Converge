<?php
namespace AnhNhan\Converge\Modules\Symbols\Generator\NodeVisitor;

use AnhNhan\Converge\Modules\Symbols\Generator\SymbolTree;

use \PHPParser_Node_Stmt_Class as PP_Class;
use \PHPParser_Node_Stmt_Interface as PP_Interface;
use \PHPParser_Node_Stmt_Function as PP_Function;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class SymbolEmitter extends \PHPParser_NodeVisitorAbstract
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

    public function leaveNode(\PHPParser_Node $node)
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
