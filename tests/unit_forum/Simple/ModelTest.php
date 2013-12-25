<?php
namespace Simple;
use Codeception\Util\Stub;
use AnhNhan\ModHub\Modules\Forum\Storage\Discussion;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTag;

class ModelTest extends \Codeception\TestCase\Test
{
    const ENTITY_NAME_DISCUSSION = 'AnhNhan\ModHub\Modules\Forum\Storage\Discussion';
    const ENTITY_NAME_DISCUSSION_TAG = 'AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTag';
    const ENTITY_NAME_DISCUSSION_TRANSACTION = 'AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction';

    const ENTITY_NAME_POST = 'AnhNhan\ModHub\Modules\Forum\Storage\Post';
    const ENTITY_NAME_POST_DRAFT = 'AnhNhan\ModHub\Modules\Forum\Storage\PostDraft';
    const ENTITY_NAME_POST_TRANSACTION = 'AnhNhan\ModHub\Modules\Forum\Storage\PostTransaction';

   /**
    * @var \ForumGuy
    */
    protected $forumGuy;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testNewlyCreatedDiscussionsDontHaveTags()
    {
        $this->forumGuy->dontSeeInDatabase("Discussion", array("label" => "foo"));

        $entityManager = $this->forumGuy->getEntityManager();
        $discussion = new Discussion($this->forumGuy->generateAuthorId(), "foo", "sometext");
        $entityManager->persist($discussion);
        $entityManager->flush();

        $this->forumGuy->seeInDatabase("Discussion", array("label" => "foo"));
        $discussion = $this->forumGuy->getRepository(self::ENTITY_NAME_DISCUSSION)->findOneBy(array("label" => "foo"));
        $this->assertEquals("foo", $discussion->label());
        $this->assertSame(0, $discussion->tags()->count());
    }

}
