<?php
namespace AnhNhan\ModHub\Modules\Markup;

use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TOCExtractor
{
    public function parseAndExtract($text)
    {
        return $this->extractFromHtml(MarkupEngine::fastParse($text));
    }

    public function extractFromHtml($html)
    {
        $crawler = new Crawler($html);

        $r = array();
        $crawler->filter('h1, h2, h3, h4, h5, h6')->each(function (Crawler $node, $i) use (&$r) {
            foreach ($node as $n) {
                $entry = array(
                    "type" => $n->nodeName,
                    "text" => str_replace("\n", " ", $n->nodeValue),
                );
                $r[] = $entry;
            }
        });

        $this->calculateLevels($r);

        return $r;
    }

    private function calculateLevels(&$result)
    {
        if (!$result) {
            return;
        }

        $tmp = ipull($result, "type");
        $tmp = array_map(function ($mm) {
            // h1 -> 1
            return substr($mm, 1);
        }, $tmp);
        $min = min($tmp) - 1;

        foreach ($result as $ii => &$val) {
            $val["level"] = $tmp[$ii] - $min;
        }
    }
}
