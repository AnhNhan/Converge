<?php
namespace AnhNhan\ModHub\Modules\Forum\Storage;

use AnhNhan\ModHub\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table
 */
class Discussion extends EntityDefinition
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
     * @Column(type="string")
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
    private $tags = array();

    /**
     * Mapping with posts
     *
     * // Extra lazy fetching since we *could* have a lot of posts in a discussion
     * @OneToMany(targetEntity="Post", fetch="EXTRA_LAZY", mappedBy="disq")
     */
    private $posts = array();

    public function __construct(
        $author,
        $label,
        $rawText,
        \DateTime $createdAt = null,
        \DateTime $lastActivity = null
    ) {
        $this->author = $author;
        $this->label = $label;
        $this->rawText = $rawText;
        $this->createdAt = $createdAt ?: new \DateTime;
        $this->lastActivity = $lastActivity ?: new \DateTime;
    }

    public function uid()
    {
        return $this->id;
    }

    public function label()
    {
        return $this->label;
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

    public function lastActivity()
    {
        return $this->lastActivity;
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
}
