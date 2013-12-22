<?php
namespace AnhNhan\ModHub\Modules\Forum\Storage;

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

    public function __construct(Discussion $disq, $tag_uid)
    {
        $this->disq = $disq;
        $this->t_id = $tag_uid;
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
        throw new \Exception("Method not supported yet!");
    }

    public function tagId()
    {
        return $this->t_id;
    }
}
