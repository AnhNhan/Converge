<?php
namespace AnhNhan\ModHub\Modules\Forum\Storage;

use AnhNhan\ModHub\Storage\EntityDefinition;
use AnhNhan\ModHub\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table
 */
class Post extends EntityDefinition implements TransactionAwareEntityInterface
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
     * @Column(type="boolean")
     */
    private $deleted = false;

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

    public function __construct() {
        $this->createdAt = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    public static function initializeForDiscussion(Discussion $disq)
    {
        $post = new static;
        $post->disq = $disq;
        return $post;
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

    public function setRawText($text)
    {
        $this->rawText = $text;
        return $this;
    }

    public function deleted()
    {
        return $this->deleted;
    }

    public function createdAt()
    {
        return $this->createdAt;
    }

    public function modifiedAt()
    {
        return $this->modifiedAt;
    }

    public function updateModifiedAt()
    {
        $this->modifiedAt = new \DateTime;
        return $this;
    }

    public function getUIDType()
    {
        return "POST";
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function transactions()
    {
        return $this->xacts;
    }

    public function getTransactionClass()
    {
        return 'AnhNhan\ModHub\Modules\Forum\Transaction\PostTransaction';
    }

    public function getTransactionEntityClass()
    {
        return 'AnhNhan\ModHub\Modules\Forum\Storage\PostTransaction';
    }
}
