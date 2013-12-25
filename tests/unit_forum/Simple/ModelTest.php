<?php
namespace Simple;
use Codeception\Util\Stub;
use AnhNhan\ModHub\Modules\Forum\Storage\Discussion;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTag;

require_once __DIR__ . "/../ForumTestCase.php";

class ModelTest extends \ForumTestCase
{
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

        $entityManager = $this->getEntityManager();
        $discussion = new Discussion($this->generateAuthorId(), "foo", "sometext");
        $entityManager->persist($discussion);
        $entityManager->flush();

        $this->forumGuy->seeInDatabase("Discussion", array("label" => "foo"));
        $discussion = $this->getRepository(self::ENTITY_NAME_DISCUSSION)->findOneBy(array("label" => "foo"));
        $this->assertEquals("foo", $discussion->label());
        $this->assertSame(0, $discussion->tags()->count());
    }

}
