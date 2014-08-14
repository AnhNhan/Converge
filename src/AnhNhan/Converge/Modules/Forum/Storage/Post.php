<?php
namespace AnhNhan\Converge\Modules\Forum\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Cache("NONSTRICT_READ_WRITE")
 * @Table(indexes={
 *   @Index(name="creation_order", columns={"createdAt"}),
 *   @Index(name="author_disq_rel", columns={"disq_id", "author"}),
 *   @Index(name="deleted_disq_rel", columns={"disq_id", "deleted"}),
 *   @Index(name="deleted_flag", columns={"deleted"})
 * })
 */
class Post extends EntityDefinition implements TransactionAwareEntityInterface
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", unique=true)
     */
    private $uid;

    /**
     * The UID of the discussion this post is contained in
     *
     * @ManyToOne(targetEntity="Discussion", fetch="EAGER", inversedBy="posts")
     * @Cache("NONSTRICT_READ_WRITE")
     * @var Discussion
     */
    private $disq;

    /**
     * @Column(type="string")
     * @var string
     */
    private $author;

    /**
     * @var \AnhNhan\Converge\Modules\User\Storage\User
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

    /**
     * @OneToMany(targetEntity="PostTransaction", mappedBy="object", fetch="LAZY")
     * @OrderBy({"createdAt"="ASC"})
     * @var \Doctrine\ORM\PersistentCollection
     */
    private $xacts;

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
        return $this->uid;
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

    public function setAuthor(\AnhNhan\Converge\Modules\User\Storage\User $author_object)
    {
        $this->author_object = $author_object;
        return $this;
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
}