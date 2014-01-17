<?php
namespace AnhNhan\ModHub\Modules\Forum\Transform;

use AnhNhan\ModHub\Modules\Forum\Storage\Discussion;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\ModHub\Modules\Forum\Storage\Post;
use AnhNhan\ModHub\Modules\Forum\Storage\PostTransaction;
use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;

use League\Fractal\TransformerAbstract;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to embed via this transformer
     *
     * @var array
     */
    protected $availableEmbeds = array(
        'posts',
        'transactions',
        'rawText',
        'renderedText',
    );

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Discussion $disq)
    {
        $tags = mpull($disq->tags->toArray(), "tagId");
        $author = $disq->author;
        $authorName = $author ? $author->dispname : null;

        return array(
            "uid"          => $disq->uid,
            "label"        => $disq->label,
            "authorId"     => $disq->authorId,
            "authorName"   => $authorName,
            // "rawText"   => $this->rawText,
            "postCount"    => $disq->posts->count(),
            "createdAt"    => (int) $disq->createdAt->getTimestamp(),
            "lastActivity" => (int) $disq->lastActivity->getTimestamp(),
            "tagIds"         => $tags,
        );
    }

    public function embedRawText(Discussion $disq)
    {
        return $this->item(array("rawText" => $disq->rawText), function () { });
    }

    public function embedRenderedText(Discussion $disq)
    {
        return $this->item(array("rawText" => $disq->rawText), function () { });
    }
}
