<?php
namespace AnhNhan\ModHub\Modules\User\Storage;

use AnhNhan\ModHub\Storage\EntityDefinition;
use AnhNhan\ModHub\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table{
 * uniqueConstraints={
 *   @Unique(name="email_assoc_unq", columns={"email", "user"})
 * },
 * indexes={
 *   @Index(name="email_user_idx", columns={"user"}),
 *   @Index(name="email_assoc_idx", columns={"email", "user"})
 * }
 * }
 */
class Email extends EntityDefinition implements TransactionAwareEntityInterface
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
     * @Column(type="string", unique=true)
     */
    private $email;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="emails")
     */
    private $user;

    /**
     * @Column(type="boolean")
     */
    private $verified = false;

    /**
     * @Column(type="boolean")
     */
    private $primary = false;

    /**
     * @Column(type="boolean")
     */
    private $deleted = false;

    /**
     * @Column(type="string")
     */
    private $verificationCode;

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
     * @OneToMany(targetEntity="UserTransaction", mappedBy="object", fetch="LAZY")
     * @OrderBy({"createdAt"="ASC"})
     */
    private $xacts;

    public function __construct()
    {
        $this->createdAt = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    public function uid()
    {
        return $this->uid;
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
        return "MAIL";
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function transactions()
    {
        return $this->xacts;
    }
}
