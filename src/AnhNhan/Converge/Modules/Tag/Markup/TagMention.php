<?php
namespace AnhNhan\Converge\Modules\Tag\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\MarkupRule;
use AnhNhan\Converge\Modules\Tag\TagApplication;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class TagMention extends MarkupRule
{
    // Tags may use dashes, underscores and periods regardless what we tell
    // them, so match them anyway.
    const Regex = '/(?<!#|\w)#([\w-]+[\w])/';

    private $query;

    public function __construct(TagApplication $app)
    {
        $this->query = create_tag_query($app);
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
        $original = $matches[1];
        $tagname = strtolower($original);
        $metadata = [
            'original' => $original,
            'tagname'  => $tagname,
        ];
        $token = $this->storage->store($original);
        $this->storage->addTokenToSet('tag-mention', $token, $metadata);
        return $token;
    }

    public function didMarkupText()
    {
        $token_set = $this->storage->getTokenSet('tag-mention');
        if (!$token_set)
        {
            return;
        }

        $tagnames = ipull($token_set, 'tagname');
        $tags = $this->query->retrieveTagsForLabels($tagnames);
        $tags     = mkey($tags, 'label');
        $tags     = array_map(function ($tag)
            {
                return link_tag($tag)->addClass('hashtag');
            }, $tags);
        foreach ($token_set as $token => $metadata)
        {
            $this->storage->overwrite($token, idx($tags, $metadata['tagname'], tooltip('span', $metadata['original'], 'tag not found')->addClass('bad-hashtag')));
        }
    }
}
