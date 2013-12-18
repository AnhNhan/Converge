<?php
namespace AnhNhan\ModHub\Views\Page;

use AnhNhan\ModHub\Views\AbstractView;
use YamwLibs\Infrastructure\ResMgmt\ResMgr;
use YamwLibs\Libs\Html\Markup\HtmlTag;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class HtmlDocumentView extends AbstractView
{
    private $title;
    private $content;
    private $head;

    public function __construct($title, $content, $head)
    {
        $this->title = $title;
        $this->content = $content;
        $this->head = $head;
    }

    public function render()
    {
        try {
        // TODO: Edit this in the future. A lot.
        $reqObj = new \YamwLibs\Libs\Http\Request;
        $reqObj->populateFromServer(array(
            'HTTP_HOST'
        ));
        $baseUrl = 'http://'.$reqObj->getServerValue('http_host', 'localhost');
        return sprintf(<<<EOT
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>%s</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<base href="%s/" />
%s
</head>
<body>
    %s
</body>
</html>
EOT
            ,
            $this->title,
            $baseUrl,
            $this->renderHead(),
            $this->content
        );
        } catch(\Exception $e) {
            var_dump($e);
        }
    }

    private function renderHead()
    {
        $head = array();
        foreach (ResMgr::getInstance()->fetchRequiredCSSResources() as $css) {
            $head[] = new HtmlTag(
                'link',
                null,
                array(
                    'rel'     => 'stylesheet',
                    'type'    => 'text/css',
                    'charset' => 'utf-8',
                    'href'    => '/rsrc/css/' . $css,
                )
            );
        }
        foreach (ResMgr::getInstance()->fetchRequiredJSResources() as $js) {
            $head[] = new HtmlTag(
                'script',
                '',
                array(
                    'src' => '/rsrc/js/' . $js,
                )
            );
        }

        $head[] = $this->head;

        return implode("\n", $head);
    }
}
