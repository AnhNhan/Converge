<?php
namespace AnhNhan\Converge\Modules\Markup;

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
        $r = array();
        preg_replace_callback('/<(h\\d)>(.*?)<\\/\\1>/s', function ($matches) use (&$r) {
            $type = $matches[1];
            $text = $matches[2];

            $entry = array(
                "type" => $type,
                "text" => str_replace("\n", " ", $text),
            );
            $r[] = $entry;
        }, $html);

        $this->calculateLevels($r);

        return $r;
    }

    public function extractAndProcessFromHtml($html)
    {
        $base_hash = hash_hmac("crc32", $html, "key?");

        $r = array();

        $ii = 0;
        $processed_html = preg_replace_callback('/<(h\\d)>(.*?)<\\/\\1>/s', function ($matches) use (&$ii, &$r, $base_hash) {
            $type = $matches[1];
            $text = $matches[2];
            $h_hash = hash_hmac("crc32", $base_hash . $ii . $type . $text, "key?"); // Add stuff to make it mostly-unique

            $entry = array(
                "type" => $type,
                "text" => str_replace("\n", " ", $text),
                "hash" => sprintf("HEADER-%s-%s", $base_hash, $h_hash),
            );
            $r[] = $entry;

            return sprintf(
                "<%s id=\"%s\">%s</%1\$s>",
                $type,
                $entry["hash"],
                $text
            );
        }, $html);

        $this->calculateLevels($r);

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
