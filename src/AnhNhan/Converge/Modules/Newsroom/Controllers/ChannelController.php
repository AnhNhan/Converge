<?php
namespace AnhNhan\Converge\Modules\Newsroom\Controllers;

use AnhNhan\Converge\Modules\Newsroom\Query\ArticleQuery;
use AnhNhan\Converge\Modules\Newsroom\Storage\Channel;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
abstract class ChannelController extends NewsroomController
{
    final protected function buildQuery()
    {
        $query = new ArticleQuery($this->app);
        return $query;
    }

    final protected function retrieveChannelObject($request, ArticleQuery $query)
    {
        if ($channel_id = $request->request->get('channel'))
        {
            $channel = head($query->searchChannels([$channel_id]));
        } else
        {
            $channel = new Channel;
        }

        return $channel;
    }
}
