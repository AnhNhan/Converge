<?php

use AnhNhan\ModHub\Storage\Types\UID;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class ForumTestCase extends \Codeception\TestCase\Test
{
    const ENTITY_NAME_DISCUSSION = 'AnhNhan\ModHub\Modules\Forum\Storage\Discussion';
    const ENTITY_NAME_DISCUSSION_TAG = 'AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTag';
    const ENTITY_NAME_DISCUSSION_TRANSACTION = 'AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction';

    const ENTITY_NAME_POST = 'AnhNhan\ModHub\Modules\Forum\Storage\Post';
    const ENTITY_NAME_POST_DRAFT = 'AnhNhan\ModHub\Modules\Forum\Storage\PostDraft';
    const ENTITY_NAME_POST_TRANSACTION = 'AnhNhan\ModHub\Modules\Forum\Storage\PostTransaction';

    protected function getApplication()
    {
        static $app;
        if (!$app) {
            $app = new AnhNhan\ModHub\Modules\Forum\ForumApplication;
        }
        return $app;
    }

    protected function getEntityManager()
    {
        static $em;
        if (!$em) {
            $em = $this->getApplication()->getEntityManager();
        }
        return $em;
    }

    protected function getRepository($entity)
    {
        return $this->getEntityManager()->getRepository($entity);
    }

    protected function generateAuthorId()
    {
        return UID::generate("USER");
    }

    protected function generateTagId()
    {
        return UID::generate("TTAG");
    }
}
