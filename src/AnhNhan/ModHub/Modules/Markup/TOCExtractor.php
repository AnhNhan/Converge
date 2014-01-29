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

    public function parseExtractAndProcess($text)
    {
        return $this->extractAndProcessFromHtml(MarkupEngine::fastParse($text));
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

    public function extractAndProcessFromHtml($html)
    {
        $base_hash = hash_hmac("crc32", $html, "key?");
        $crawler = new Crawler($html);

        $r = array();
        $crawler->filter('h1, h2, h3, h4, h5, h6')->each(function (Crawler $node, $i) use (&$r, $base_hash) {
            foreach ($node as $key => $n) {
                $h_hash = hash_hmac("crc32", $base_hash . $i . $key . $n->nodeValue, "key?"); // Add $base_hash, $i and $key to make it mostly-unique
                $h_id   = sprintf("HEADER-%s-%s", $base_hash, $h_hash);
                $entry = array(
                    "type" => $n->nodeName,
                    "text" => str_replace("\n", " ", $n->nodeValue),
                    "hash" => $h_id,
                );
                $r[] = $entry;

                if ($n instanceof \DOMElement) {
                    // For now, simply overwrite - our markup shouldn't have id's, anyway
                    $n->setAttribute("id", $h_id);
                    //mh_var_dump($n->getAttribute("id"));
                    $newN = $n->cloneNode(true);
                    $newN->setAttribute("id", $h_id);
                    //$node->filter("*")->reduce(function () { return true; });
                    //$n->insertBefore($newN);
                }
            }
        });

        $this->calculateLevels($r);

        $ii = 0;
        $processed_html = preg_replace_callback('/<(h\\d)>/', function ($matches) use (&$ii, $r) {
            return sprintf("<%s id=\"%s\">", $matches[1], $r[$ii++]["hash"]);
        }, $html);

        return array($r, $processed_html);
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
