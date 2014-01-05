<?php
namespace AnhNhan\ModHub\Views\Panel;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Views\AbstractView;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Panel extends AbstractView
{
    const COLOR_NONE    = "none";
    const COLOR_DANGER  = "danger";
    const COLOR_WARNING = "warning";
    const COLOR_INFO    = "info";
    const COLOR_SUCCESS = "success";
    const COLOR_DARK    = "dark";

    private $header;
    private $color = self::COLOR_NONE;
    private $midriff;
    private $midriffRight;

    public function __construct()
    {
        parent::__construct();
        $this->midriff = new MarkupContainer;
    }

    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    public function midriff()
    {
        // Return the object for direct interface
        // Not a public property, since they could change it to something else
        return $this->midriff;
    }

    public function setMidriffRight($midriffRight) {
        $this->midriffRight = $midriffRight;
        return $this;
    }

    private function getValidColors()
    {
        return array(
            self::COLOR_NONE    => true,
            self::COLOR_DANGER  => true,
            self::COLOR_WARNING => true,
            self::COLOR_INFO    => true,
            self::COLOR_SUCCESS => true,
            self::COLOR_DARK    => true,
        );
    }

    public function setColor($color = self::COLOR_NONE)
    {
        if (!idx($this->getValidColors(), $color)) {
            throw new \InvalidArgumentException("Invalid color '{$color}'.");
        }
        $this->color = $color;
        return $this;
    }

    public function render()
    {
        $panelTag = ModHub\ht("div")->addClass("panel-container-inner");
        $container = ModHub\ht("div", $panelTag)->addClass("panel-container");

        if ($this->color != self::COLOR_NONE) {
            $container->addClass($this->color);
        }

        if ($this->header) {
            $panelTag->appendContent(
                ModHub\ht("div", $this->header)
                    ->addClass("panel-header")
            );
        }

        if (count($this->midriff)) {
            $midriffRight = null;
            if ($this->midriffRight) {
                $midriffRight = ModHub\ht("div", $this->midriffRight)
                    ->addClass("panel-midriff-right")
                ;
            }
            $panelTag->appendContent(
                ModHub\ht("div", $midriffRight)
                    ->appendContent($this->midriff)
                    ->addClass("panel-midriff")
            );
        }

        $children = $this->retrieveChilds();
        if ($children->count()) {
            $body = ModHub\ht("div")->addClass("panel-body");
            foreach ($children->getMarkupData() as $child) {
                $body->appendContent($child);
            }
            $panelTag->appendContent($body);
        }

        return $container;
    }
}
