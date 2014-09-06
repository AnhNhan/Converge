<?php
namespace AnhNhan\Converge\Modules\Forum\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Cache("NONSTRICT_READ_WRITE")
 * @Table(indexes={
 *   @Index(name="label", columns={"label"}),
 *   @Index(name="author_disq_rel", columns={"id", "author"}),
 *   @Index(name="activity", columns={"lastActivity"})
 * })
 */
class Discussion extends EntityDefinition implements TransactionAwareEntityInterface
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
     * @Column(type="string")
     */
    private $label;

    /**
     * @Column(type="string")
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
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $lastActivity;

    /**
     * Mapping with tags done separately, not by Doctrine. Still using entity
     * object(s) though to keep track of references
     *
     * @OneToMany(targetEntity="DiscussionTag", fetch="EAGER", mappedBy="disq")
     * @Cache("NONSTRICT_READ_WRITE")
     */
    private $tags;

    /**
     * // Abusing ManyToMany so we don't need a mappedBy field
     * @ManyToMany(targetEntity="ForumComment", fetch="LAZY")
     * @Cache("NONSTRICT_READ_WRITE")
     */
    private $comments;

    /**
     * Mapping with posts
     *
     * // Extra lazy fetching since we *could* have a lot of posts in a discussion
     * @OneToMany(targetEntity="Post", fetch="EXTRA_LAZY", mappedBy="disq")
     */
    private $posts;

    /**
     * @OneToMany(targetEntity="DiscussionTransaction", mappedBy="object", fetch="EXTRA_LAZY")
     */
    private $xacts;

    public function __construct() {
        $this->createdAt = new \DateTime;
        $this->lastActivity = new \DateTime;
    }

    public function uid()
    {
        return $this->uid;
    }

    public function label()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
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

    public function text($text = null)
    {
        if ($text === null) {
            return $this->rawText;
        } else {
            $this->rawText = $text;
            return $this;
        }
    }

    public function rawText()
    {
        return $this->rawText;
    }

    public function createdAt()
    {
        return $this->createdAt;
    }

    public function lastActivity()
    {
        return $this->lastActivity;
    }

    public function updateLastActivity()
    {
        $this->lastActivity = new \DateTime;
    }

    public function tags()
    {
        return $this->tags;
    }

    public function comments()
    {
        return $this->comments;
    }

    public function posts()
    {
        return $this->posts;
    }

    public function getUIDType()
    {
        return "DISQ";
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function transactions()
    {
        return $this->xacts;
    }
}
