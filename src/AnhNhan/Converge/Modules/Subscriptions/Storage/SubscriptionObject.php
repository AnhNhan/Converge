<?php
namespace AnhNhan\Converge\Modules\Forum\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Cache("NONSTRICT_READ_WRITE")
 * @Table
 */
class SubscriptionEntry extends EntityDefinition
{
    /**
     * @Id
     * @Column(type="string")
     */
    private $subscriber_uid;

    /**
     * @var \AnhNhan\Converge\Modules\User\Storage\User
     */
    private $subscriber_object;

    /**
     * @Id
     * @Column(type="string")
     */
    private $object_uid;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $createdAt;

    public function __construct() {
        $this->createdAt = new \DateTime;
    }

    public function subscriberId()
    {
        return $this->subscriber_uid;
    }

    public function subscriber()
    {
        return $this->subscriber_object;
    }

    public function setSubscriber(\AnhNhan\Converge\Modules\User\Storage\User $subscriber_object)
    {
        $this->subscriber_object = $subscriber_object;
        return $this;
    }

    public function object_uid()
    {
        return $this->object_uid;
    }

    public function createdAt()
    {
        return $this->createdAt;
    }
}
