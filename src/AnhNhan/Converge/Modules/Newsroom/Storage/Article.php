<?php
namespace AnhNhan\Converge\Modules\Newsroom\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="article_type", type="string")
 */
abstract class Article extends EntityDefinition implements TransactionAwareEntityInterface
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string", unique=true)
     */
    protected $uid;

    /**
     * @Column(type="string")
     */
    protected $title;

    /**
     * @Column(type="string", unique=true)
     */
    protected $slug;

    /**
     * @Column(type="string")
     */
    protected $byline;

    /**
     * @ManyToOne(targetEntity="Channel", fetch="EAGER")
     *
     * @var Channel
     */
    protected $channel;

    /**
     * @OneToMany(targetEntity="ArticleAuthor", fetch="EAGER", mappedBy="article")
     * @Cache("NONSTRICT_READ_WRITE")
     *
     * @var \Doctrine\ORM\PersistentCollection
     */
    protected $authors;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    protected $modifiedAt;

    /**
     * @OneToMany(targetEntity="ArticleTag", fetch="EAGER", mappedBy="article")
     * @Cache("NONSTRICT_READ_WRITE")
     */
    private $tags;

    public function __construct() {
        $this->createdAt = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    public function uid()
    {
        return $this->uid;
    }

    public function authors()
    {
        return $this->authors;
    }

    public function title()
    {
        return $this->title;
    }

    public function slug()
    {
        return $this->slug;
    }

    public function byline()
    {
        return $this->byline;
    }

    public function channel()
    {
        return $this->channel;
    }

    public function tags()
    {
        return $this->tags;
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
    }
}
