<?php
namespace AnhNhan\ModHub\Modules\Forum\Storage;

use AnhNhan\ModHub\Modules\Tag\Storage\Tag;
use AnhNhan\ModHub\Storage\EntityDefinition;
use AnhNhan\ModHub\Storage\Types\UID;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table
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

    public function __construct(Discussion $disq, $tag)
    {
        $this->disq = $disq;
        if (is_object($tag)) {
            $this->t_id = $tag->uid();
            $this->t_obj = $tag;
        } else {
            // We only received a UID string
            UID::checkValidity($tag);
            $this->t_id = $tag;
        }
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
