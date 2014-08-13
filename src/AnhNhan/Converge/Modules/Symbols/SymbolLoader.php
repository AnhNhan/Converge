<?php
namespace AnhNhan\Converge\Modules\Symbols;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class SymbolLoader
{
    /**
     * @var SymbolLoader
     */
    private static $instance;
    private static $staticRootDir;

    private $classes;

    /**
     * @var array
     */
    private $locClasses;

    /**
     * @var array
     */
    private $locFunctions;

    /**
     * @var array
     */
    private $treeDerivs;

    /**
     * @var array
     */
    private $treeImpls;

    private $rootDir;

    private $allSymbols;
    private $allLocations;
    private $allFunctions;
    private $allClasses;

    /**
     * @return SymbolLoader
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            $class = __CLASS__;
            self::$instance = new $class(self::$staticRootDir);
        }
        return self::$instance;
    }

    public static function setStaticRootDir($rootDir)
    {
        self::$staticRootDir = $rootDir;
    }

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir . DIRECTORY_SEPARATOR;
        $symbolMap = include $this->rootDir . '__symbol_map__.php';

        $this->classes = $symbolMap["classes"];
        $this->locClasses = ipull($this->classes, "file");
        $this->locFunctions = $symbolMap["functions"];
        $this->treeDerivs = $symbolMap["xmap"];
        $this->treeImpls = $symbolMap["implementations"];

        $this->allFunctions = array_unique(array_values($this->locFunctions));
        $this->allClasses = array_unique(array_values($this->locClasses));

        $this->allLocations = array_unique(
            array_merge($this->allClasses, $this->allFunctions)
        );
    }

    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'), true, true);
    }

    public function loadAllSymbols()
    {
        if (!$this->allSymbols) {
            $this->allSymbols = array_unique(
                array_merge($this->locClasses, $this->locFunctions)
            );
        }

        if (!$this->allLocations) {
            $this->allLocations = array_unique($this->allSymbols);
        }

        foreach ($this->allLocations as $location) {
            include_once $this->rootDir . $location;
        }
    }

    public function loadAllFunctions()
    {
        foreach ($this->allFunctions as $function) {
            include_once $this->rootDir . $function;
        }
    }

    public function loadAllClasses()
    {
        foreach ($this->allClasses as $class) {
            include_once $this->rootDir . $class;
        }
    }

    public function loadClass($fqClassName)
    {
        // Don't throw, since we may have other autoloaders in the stack
        if (isset($this->locClasses[$fqClassName])) {
            $classLocation = $this->rootDir . $this->locClasses[$fqClassName];
            if (file_exists($classLocation)) {
                include_once $classLocation;
                return true;
            }
        }
    }

    public function getClassesThatDeriveFromThisOne($parentClass)
    {
        return idx($this->treeDerivs, $parentClass);
    }

    public function getClassesThatImplement($parentClass)
    {
        return idx($this->treeImpls, $parentClass);
    }

    public function getConcreteClassesThatDeriveFromThisOne($parentClass)
    {
        $derivs = $this->getClassesThatDeriveFromThisOne($parentClass);
        $concreteClasses = array();

        foreach ($derivs as $deriv) {
            if (!idx($this->classes[$deriv], "abstr")) {
                $concreteClasses[] = $deriv;
            }
        }

        return $concreteClasses;
    }

    public function getObjectsThatDeriveFrom($parentClass)
    {
        foreach ($this->getConcreteClassesThatDeriveFromThisOne($parentClass) as $class) {
            $objects[] = new $class;
        }
        return $objects;
    }

    public function getConcreteClassesThatImplement($parentClass)
    {
        $impls = $this->getClassesThatImplement($parentClass);
        $concreteClasses = array();

        foreach ($impls as $impl) {
            if (!idx($this->classes[$impl], "abstr")) {
                $concreteClasses[] = $impl;
            }
        }

        return $concreteClasses;
    }

    public function getObjectsThatImplements($parentClass)
    {
        foreach ($this->getConcreteClassesThatImplement($parentClass) as $class) {
            $objects[] = new $class;
        }
        return $objects;
    }
}
