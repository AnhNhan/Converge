<?php
namespace AnhNhan\Converge\Modules\Subscription;

use AnhNhan\Converge\Storage\Query;
use Doctrine\ORM\Query as DoctrineQuery;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class SubscriptionQuery extends Query
{
    const ENTITY_SUBSCRIPTION = "AnhNhan\\Converge\\Modules\\Subscription\\Storage\\SubscriptionObject";

    public function retrieveSubscriptionsForUsers(array $ids, $limit = null, $offset = null)
    {
        return $this
            ->repository(self::ENTITY_SUBSCRIPTION)
            ->findBy(['subscriber_uid' => $ids], [], $limit, $offset)
        ;
    }

    public function retrieveSubscriptionObject($subscriber_uid, $object_uid)
    {
        return head($this
            ->repository(self::ENTITY_SUBSCRIPTION)
            ->findBy(['subscriber_uid' => $subscriber_uid, 'object_uid' => $object_uid])
        );
    }
}
