<?php
namespace AnhNhan\Converge\Modules\Forum\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Cache("NONSTRICT_READ_WRITE")
 * @Table
 */
class ForumComment extends EntityDefinition implements TransactionAwareEntityInterface
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
    private $parent_uid;

    /**
     * @Column(type="string")
     * @var string
     */
    private $author;

    /**
     * @var \AnhNhan\Converge\Modules\People\Storage\User
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
     * @OneToMany(targetEntity="ForumCommentTransaction", mappedBy="object", fetch="LAZY")
     * @var \Doctrine\ORM\PersistentCollection
     */
    private $xacts;

    public function __construct() {
        $this->createdAt = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    public function uid()
    {
        return $this->uid;
    }

    public function authorId()
    {
        return $this->author;
    }

    public function author()
    {
        return $this->author_object;
    }

    public function setAuthor(\AnhNhan\Converge\Modules\People\Storage\User $author_object)
    {
        $this->author_object = $author_object;
        return $this;
    }

    public function rawText()
    {
        return $this->rawText;
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
        return "FORU-CMNT";
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function transactions()
    {
        return $this->xacts;
    }

    public function toDictionary()
    {
        if ($this->deleted)
        {
            return [
                "deleted" => true,
            ];
        }

        $dict = [
            "uid" => $this->uid,
            "parentUid" => $this->parent_uid,
            "rawText" => $this->rawText,
            "deleted" => false,
            "createdAt" => (int) $this->createdAt->getTimestamp(),
            "modifiedAt" => (int) $this->modifiedAt->getTimestamp(),
            "createdAtRendered"    => $this->createdAt->format("D, d M 'y"),
            "lastActivityRendered" => $this->modifiedAt->format("D, d M 'y"),
        ];

        $dict = array_merge($dict, $this->author_object->toDictionary('author'));

        return $dict;
    }
}
