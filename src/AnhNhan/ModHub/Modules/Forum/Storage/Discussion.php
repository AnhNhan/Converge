<?php
namespace AnhNhan\ModHub\Modules\Forum\Storage;

use AnhNhan\ModHub\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity @Table(name="discussions")
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
     * @OneToOne(targetEntity="Post", fetch="EAGER")
     * @var Post
     */
    private $firstPost;

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
        $label,
        \DateTime $createdAt,
        \DateTime $lastActivity
    ) {
        $this->label = $label;
        $this->createdAt = $createdAt;
        $this->lastActivity = $lastActivity;
    }

    public function uid()
    {
        return $this->id;
    }

    public function label()
    {
        return $this->label;
    }

    public function firstPost(Post $firstPost = null)
    {
        if ($firstPost === null) {
            return $this->firstPost;
        } else {
            $this->firstPost = $firstPost;
            return $this;
        }
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
