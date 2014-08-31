<?php
namespace AnhNhan\Converge\Modules\Markup\Markup;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Markup\TemplateMarkupRule;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class SoundCloudEmbedTrack extends TemplateMarkupRule
{
    public function get_key()
    {
        return 'soundcloud-embed-track';
    }

    public function apply_occurence($matches)
    {
        return cv\hsprintf('<iframe width="100%%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https://api.soundcloud.com/tracks/%s"></iframe>', trim($matches[1]));
    }
}
