<?php
namespace AnhNhan\Converge\Modules\Forum\Transform;

use AnhNhan\Converge\Modules\Forum\Storage\Discussion;
use AnhNhan\Converge\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\Converge\Modules\Forum\Storage\Post;
use AnhNhan\Converge\Modules\Forum\Storage\PostTransaction;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;

use League\Fractal\TransformerAbstract;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionTransformer extends TransformerAbstract
{
    private $tags = [];

    public function __construct($tags = [])
    {
        $this->tags = $tags;
    }

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Discussion $disq)
    {
        $tags = mpull($disq->tags->toArray(), "tagId");

        return array(
            "uid"          => $disq->uid,
            "label"        => $disq->label,
            "authorId"     => $disq->authorId,
            "authorName"   => $disq->author ? $disq->author->name : null,
            "authorNameCanonical" => $disq->author ? $disq->author->canonical_name : null,
            "authorImage"  => $disq->author ? $disq->author->getGravatarImagePath() : null,
            "postCount"    => $disq->posts->count(),
            "createdAt"    => (int) $disq->createdAt->getTimestamp(),
            "lastActivity" => (int) $disq->lastActivity->getTimestamp(),
            "createdAtRendered"    => $disq->createdAt->format("D, d M 'y"),
            "lastActivityRendered" => $disq->lastActivity->format("D, d M 'y"),
            "tagIds"       => $tags,
            "tagObjects"   => array_select_keys($this->tags, $tags),
        );
    }
}
