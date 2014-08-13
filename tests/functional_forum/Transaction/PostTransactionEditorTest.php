<?php
namespace Transaction;
use Codeception\Util\Stub;

use AnhNhan\Converge\Modules\Forum\Storage\Discussion;
use AnhNhan\Converge\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\Converge\Modules\Forum\Storage\Post;
use AnhNhan\Converge\Modules\Forum\Storage\PostTransaction;
use AnhNhan\Converge\Modules\Forum\Transaction\DiscussionTransactionEditor;
use AnhNhan\Converge\Modules\Forum\Transaction\PostTransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Types\UID;

class PostTransactionEditorTest extends \Codeception\TestCase\Test
{
   /**
    * @var \ForumTestGuy
    */
    protected $forumTestGuy;

    const TEXT1 = "Post text";
    const TEXT2 = "Some other post text";

    const DISQ_LABEL = "some foo label";

    const ENTITY_DISCUSSION = 'AnhNhan\Converge\Modules\Forum\Storage\Discussion';
    const ENTITY_POST = 'AnhNhan\Converge\Modules\Forum\Storage\Post';

    protected function _before()
    {
        $this->em = \Codeception\Module\Doctrine2::$em;

        $this->initUser();
    }

    protected function _after()
    {
    }

    private function initUser()
    {
        $this->user = UID::generate("USER");
    }

    private function initEditor()
    {
        $this->editor = new PostTransactionEditor($this->em);
        $this->editor->setActor($this->user);
        return $this->editor;
    }

    public function testCreatePost()
    {
        $this->forumTestGuy->dontSeeInRepository(self::ENTITY_DISCUSSION, array("label" => self::DISQ_LABEL));
        $this->createDiscussion();
        $this->forumTestGuy->seeInRepository(self::ENTITY_DISCUSSION, array("label" => self::DISQ_LABEL));

        $disq = $this->retrieveDiscussion();
        self::assertCount(0, $disq->posts());
        self::assertCount(3, $disq->transactions());

        $post1 = Post::initializeForDiscussion($disq);
        $post2 = Post::initializeForDiscussion($disq);

        $editor = $this->initEditor()
            ->setEntity($post1)
            ->addTransaction(
                PostTransaction::create(TransactionEntity::TYPE_CREATE, $disq->uid())
            )
            ->addTransaction(
                PostTransaction::create(PostTransaction::TYPE_EDIT_POST, self::TEXT1)
            )
            ->apply()
        ;

        $editor = $this->initEditor()
            ->setEntity($post2)
            ->addTransaction(
                PostTransaction::create(TransactionEntity::TYPE_CREATE, $disq->uid())
            )
            ->addTransaction(
                PostTransaction::create(PostTransaction::TYPE_EDIT_POST, self::TEXT2)
            )
            ->apply()
        ;
        $this->em->clear();

        $disq = $this->retrieveDiscussion();
        self::assertCount(2, $disq->posts());
        self::assertCount(5, $disq->transactions());
    }

    private function retrieveDiscussion()
    {
        $repo = $this->em->getRepository(self::ENTITY_DISCUSSION);
        $disq = $repo->findOneBy(array("label" => self::DISQ_LABEL));
        return $disq;
    }

    private function createDiscussion()
    {
        $discussion = new Discussion();

        $editor = DiscussionTransactionEditor::create($this->em)
            ->setActor(\AnhNhan\Converge\Storage\Types\UID::generate("USER"))
            ->setEntity($discussion)
            ->addTransaction(
                DiscussionTransaction::create(TransactionEntity::TYPE_CREATE)
            )
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_LABEL, self::DISQ_LABEL)
            )
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_TEXT, "some bar text")
            )
        ;

        $editor->apply();
        $this->em->clear();
        return $discussion;
    }
}
