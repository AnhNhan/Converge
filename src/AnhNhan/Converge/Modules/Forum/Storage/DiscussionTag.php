<?php
namespace AnhNhan\Converge\Modules\Forum\Storage;

use AnhNhan\Converge\Modules\Tag\Storage\Tag;
use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Types\UID;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Cache("NONSTRICT_READ_WRITE")
 * @Table
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
     * @var Tag
     */
    private $t_obj;

    /**
     * @C//olumn(type="float")
     * @var float
     */
    //public $strength = 1.0;

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

    public function setTag(Tag $tag)
    {
        if ($tag->uid !== $this->t_id) {
            throw new \InvalidArgumentException("UIDs do not match!");
        }
        $this->t_obj = $tag;
        return $this;
    }

    public function tagId()
    {
        return $this->t_id;
    }
}
