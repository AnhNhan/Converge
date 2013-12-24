<?php
namespace AnhNhan\ModHub\Modules\Forum\Storage;

use AnhNhan\ModHub\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table(name="posts")
 */
class Post extends EntityDefinition
{
    /**
     * @Id
     * @Column(type="string")
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="AnhNhan\ModHub\Storage\Doctrine\UIDGenerator")
     */
    private $id;

    /**
     * The UID of the discussion this post is contained in
     *
     * @ManyToOne(targetEntity="Discussion", fetch="EAGER", inversedBy="posts")
     * @var Discussion
     */
    private $disq;

    /**
     * @Column(type="string")
     * @var string
     */
    private $author;

    /**
     * @var \AnhNhan\ModHub\Modules\User\Storage\User
     */
    private $author_object;

    /**
     * @Column(type="text")
     */
    private $rawText;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $modifiedAt;

    public function __construct(
        Discussion $discussion,
        $author_uid,
        $rawText,
        \DateTime $createdAt = null,
        \DateTime $modifiedAt = null
    ) {
        $this->disq = $discussion;
        $this->author = $author_uid;
        $this->rawText = $rawText;
        $this->createdAt = $createdAt ?: new \DateTime;
        $this->modifiedAt = $modifiedAt ?: new \DateTime;
    }

    public function uid()
    {
        return $this->id;
    }

    public function parentDisq()
    {
        return $this->disq;
    }

    public function parentDisqId()
    {
        return $this->parentDisq()->uid();
    }

    public function authorId()
    {
        return $this->author;
    }

    public function author()
    {
        return $this->author_object;
    }

    public function rawText()
    {
        return $this->rawText;
    }

    public function getUIDType()
    {
        return "POST";
    }
}
