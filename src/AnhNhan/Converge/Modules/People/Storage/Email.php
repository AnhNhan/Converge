<?php
namespace AnhNhan\Converge\Modules\People\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Transaction\TransactionAwareEntityInterface;

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
class Email extends EntityDefinition
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Column(type="string", unique=true)
     */
    public $email;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="emails")
     */
    public $user;

    /**
     * @Column(type="boolean")
     */
    public $is_verified = false;

    /**
     * @Column(type="boolean")
     */
    public $is_primary = false;

    /**
     * @Column(type="boolean")
     */
    public $is_deleted = false;

    /**
     * @Column(type="string", nullable=true)
     */
    public $verificationCode;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    public $modifiedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime;
        $this->modifiedAt = new \DateTime;
    }

    public function getUIDType()
    {
        return "EADR";
    }
}
