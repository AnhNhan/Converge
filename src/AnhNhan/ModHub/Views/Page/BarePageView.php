<?php
namespace AnhNhan\ModHub\Views\Page;

use YamwLibs\Libs\Html\Markup\SafeTextNode;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class BarePageView extends PageView
{
    public function __construct($content)
    {
        parent::__construct('', new SafeTextNode($content));
    }
}
