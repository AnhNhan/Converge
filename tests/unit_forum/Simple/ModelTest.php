<?php
namespace Simple;
use Codeception\Util\Stub;
use AnhNhan\ModHub\Modules\Forum\Storage\Discussion;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTag;

use AnhNhan\ModHub\Modules\Tag\TagApplication;
use AnhNhan\ModHub\Modules\Tag\Storage\Tag;

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
        $this->forumGuy->getEntityManager()->clear();
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

    public function testAddTagToDiscussion()
    {
        // Repeat previous test, create discussion without tags
        $this->testNewlyCreatedDiscussionsDontHaveTags();

        $entityManager = $this->forumGuy->getEntityManager();
        $entityManager->clear();

        $repository = $this->forumGuy->getRepository(self::ENTITY_NAME_DISCUSSION);
        $discussion = $repository->findOneBy(array("label" => "foo"));

        $tagApp = new TagApplication;
        $tagEm  = $tagApp->getEntityManager();

        $tag1 = new Tag(\Filesystem::readRandomCharacters(4), "green", null, -1);
        $tag2 = new Tag(\Filesystem::readRandomCharacters(4), "blue", null, 1);
        $tagEm->persist($tag1);
        $tagEm->persist($tag2);
        $tagEm->flush();

        $disqTag1 = new DiscussionTag($discussion, $tag1);
        $disqTag2 = new DiscussionTag($discussion, $tag2);
        $entityManager->persist($disqTag1);
        $entityManager->persist($disqTag2);
        $entityManager->flush();
        $entityManager->clear();

        $this->forumGuy->canSeeInDatabase("DiscussionTag", array("t_id" => $tag1->uid()));
        $this->forumGuy->canSeeInDatabase("DiscussionTag", array("t_id" => $tag2->uid()));

        $discussion = $repository->findOneBy(array("label" => "foo"));
        $this->assertEquals(2, $discussion->tags()->count());
    }

}
