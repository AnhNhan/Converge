<?php
namespace AnhNhan\ModHub\Modules\Forum\Storage;

use AnhNhan\ModHub\Modules\Tag\Storage\Tag;
use AnhNhan\ModHub\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table(name="discussion_tags")
 */
class DiscussionTag extends EntityDefinition
{
    /**
     * @Id
     * @ManyToOne(targetEntity="Discussion", fetch="EAGER", inversedBy="tags")
     * @var Discussion
     */
    private $disq;

    /**
     * @Id
     * @Column(type="string")
     *
     * @var string
     */
    private $t_id;

    /**
     * Will be loaded from Donctrine event subscriber
     *
     * @var Tag
     */
    private $t_obj;

    public function __construct(Discussion $disq, Tag $tag)
    {
        $this->disq = $disq;
        $this->t_id = $tag->uid();
        $this->t_obj = $tag;
    }

    public function discussion()
    {
        return $this->disq;
    }

    public function discussionId()
    {
        return $this->disq->uid();
    }

    public function tag()
    {
        if (!$this->t_obj) {
            throw new \Exception("This object hasn't been initialized with a tag yet!");
        }
        return $this->t_obj;
    }

    public function tagId()
    {
        return $this->t_id;
    }
}
