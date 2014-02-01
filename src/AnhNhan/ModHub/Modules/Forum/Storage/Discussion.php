<?php
namespace AnhNhan\ModHub\Modules\Forum\Storage;

use AnhNhan\ModHub\Storage\EntityDefinition;
use AnhNhan\ModHub\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
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
     * @Column(type="string")
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="AnhNhan\ModHub\Storage\Doctrine\UIDGenerator")
     */
    private $id;

    /**
     * @Column(type="string")
     */
    private $label;

    /**
     * @Column(type="string")
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
    private $lastActivity;

    /**
     * Mapping with tags done separately, not by Doctrine. Still using entity
     * object(s) though to keep track of references
     *
     * @OneToMany(targetEntity="DiscussionTag", fetch="EAGER", mappedBy="disq")
     */
    private $tags;

    /**
     * Mapping with posts
     *
     * // Extra lazy fetching since we *could* have a lot of posts in a discussion
     * @OneToMany(targetEntity="Post", fetch="EXTRA_LAZY", mappedBy="disq")
     * @OrderBy({"createdAt"="ASC"})
     */
    private $posts;

    /**
     * @OneToMany(targetEntity="DiscussionTransaction", mappedBy="object", fetch="EXTRA_LAZY")
     * @OrderBy({"createdAt"="ASC"})
     */
    private $xacts;

    public function __construct() {
        $this->createdAt = new \DateTime;
        $this->lastActivity = new \DateTime;
    }

    public function uid()
    {
        return $this->id;
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

    public function text($text = null)
    {
        if ($text === null) {
            return $this->rawText;
        } else {
            $this->rawText = $text;
            return $this;
        }
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
