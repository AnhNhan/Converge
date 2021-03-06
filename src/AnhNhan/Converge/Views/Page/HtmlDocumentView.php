<?php
namespace AnhNhan\Converge\Views\Page;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\AbstractView;
use AnhNhan\Converge\Modules\StaticResources\ResMgr;
use YamwLibs\Libs\Html\Markup\HtmlTag;

use Symfony\Component\HttpFoundation\Request;

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
        $reqObj = Request::createFromGlobals();
        $baseUrl = $reqObj->getBaseUrl();
        return Converge\safeHtml(sprintf(<<<EOT
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>%s</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<base href="%s/" />
%s

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->

<script>
$(function () {
    $('[data-toggle=tooltip]').tooltip({container: 'body'});
    $('[data-toggle=tooltip-bottom]').tooltip({placement: 'bottom', container: 'body'});
});
</script>
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
        ));
        } catch(\Exception $e) {
            var_dump($e);
        }
    }

    private function renderHead()
    {
        $head = array();
        foreach ($this->getResMgr()->fetchRequiredCSSResources() as $css) {
            list($name, $hash) = $css;
            $head[] = new HtmlTag(
                'link',
                null,
                array(
                    'rel'     => 'stylesheet',
                    'type'    => 'text/css',
                    'charset' => 'utf-8',
                    'href'    => sprintf('/rsrc/css/%s.%s.css', $name, $hash),
                )
            );
        }
        foreach ($this->getResMgr()->fetchRequiredJSResources() as $js) {
            list($name, $hash) = $js;
            $head[] = new HtmlTag(
                'script',
                '',
                array(
                    'src' => sprintf('/rsrc/js/%s.%s.js', $name, $hash),
                )
            );
        }

        $head[] = $this->head;

        return implode("\n", $head);
    }

    public function renderRequireJs()
    {
        $tag = Converge\ht("script");
        return $tag;
    }
}
