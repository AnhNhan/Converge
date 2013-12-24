<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub\Views\AbstractView;
use YamwLibs\Libs\View\ViewInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class PageView extends AbstractView
{
    private $title;
    private $content;

    /**
     * @param string $title
     * @param ViewInterface $content
     */
    public function __construct($title, ViewInterface $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * Gets the page title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Gets the page content
     *
     * @return ViewInterface $content
     */
    public function getContent()
    {
        return $this->content;
    }
}
