<?php
namespace AnhNhan\Converge\Modules\Tag\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupRule;
use AnhNhan\Converge\Modules\Tag\TagApplication;
use AnhNhan\Converge\Modules\Tag\TagQuery;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class Hashtag extends MarkupRule
{
    // Tags may use dashes, underscores and periods regardless what we tell
    // them, so match them anyway.
    const Regex = '/(?<!#|\w)#([\w-]+[\w])/';

    private $query;

    public function __construct(TagApplication $app)
    {
        $this->query = new TagQuery($app);
    }

    public function apply($text)
    {
        return preg_replace_callback(
            self::Regex,
            [$this, 'applyHashtag'],
            $text
        );
    }

    public function applyHashtag($matches)
    {
        $tagname = strtolower($matches[1]);
        $tag     = idx($this->query->retrieveTagsForLabels([$tagname], 1), 0);
        if (!$tag)
        {
            return tooltip('span', $matches[0], 'tag not found')->addClass('bad-hashtag');
        }

        return link_tag($tag)->addClass('hashtag');
    }
}
