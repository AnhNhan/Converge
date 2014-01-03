<?php
namespace Transaction;
use Codeception\Util\Stub;

use AnhNhan\ModHub\Modules\Forum\Storage\Discussion;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\ModHub\Modules\Forum\Transaction\DiscussionTransactionEditor;
use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;
use AnhNhan\ModHub\Storage\Types\UID;

class DiscussionTransactionEditorTest extends \Codeception\TestCase\Test
{
   /**
    * @var \ForumTestGuy
    */
    protected $forumTestGuy;

    const LABEL = "Discussion label";
    const TEXT  = "Discussion text";

    const ENTITY_DISCUSSION = 'AnhNhan\ModHub\Modules\Forum\Storage\Discussion';

    protected function _before()
    {
        $this->em = \Codeception\Module\Doctrine2::$em;

        $this->initUser();
        $this->initEditor();
    }

    protected function _after()
    {
    }

    private function initUser()
    {
        $this->user   = UID::generate("USER");
    }

    private function initEditor()
    {
        $this->editor = new DiscussionTransactionEditor($this->em);
        $this->editor->setActor($this->user);
        return $this->editor;
    }

    public function testCreateNewDiscussion()
    {
        $this->forumTestGuy->dontSeeInRepository(self::ENTITY_DISCUSSION, array("label" => self::LABEL));

        $disq = new Discussion;

        $editor = $this->editor;
        $editor->setEntity($disq)
            ->addTransaction(
                DiscussionTransaction::create(TransactionEntity::TYPE_CREATE)
            )
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_LABEL, self::LABEL)
            )
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_TEXT, self::TEXT)
            )
        ;

        self::assertEquals(null, $disq->label());
        $result = $editor->apply();
        self::assertEquals(self::LABEL, $disq->label());

        self::assertCount(3, $result, print_r(mpull($result, "type"), true));
        self::assertEquals(TransactionEntity::TYPE_CREATE, $result[0]->type());
        self::assertEquals(DiscussionTransaction::TYPE_EDIT_LABEL, $result[1]->type());

        $disqId = $disq->uid();
        $this->em->clear();

        $this->forumTestGuy->seeInRepository(self::ENTITY_DISCUSSION, array("id" => $disqId));
        $repo = $this->em->getRepository(self::ENTITY_DISCUSSION);
        $disq = $repo->find($disqId);
        self::assertEquals(self::LABEL, $disq->label());
        self::assertEquals(self::TEXT, $disq->text());
        self::assertEquals($this->user, $disq->authorId());
        self::assertCount(0, $disq->tags());
        $xacts = $disq->transactions()->toArray();
        self::assertCount(3, $xacts, print_r(mpull($xacts, "type"), true));
        self::assertEquals($this->user, $xacts[0]->actorId());
        self::assertEquals($this->user, $xacts[1]->actorId());
        self::assertEquals($this->user, $xacts[2]->actorId());
    }

    public function testTagFumbling()
    {
        $this->forumTestGuy->dontSeeInRepository(self::ENTITY_DISCUSSION, array("label" => self::LABEL));

        $disq = new Discussion;
        $tagId1 = UID::generate("TTAG");
        $tagId2 = UID::generate("TTAG");

        $editor = $this->editor;
        $editor->setEntity($disq)
            ->addTransaction(
                DiscussionTransaction::create(TransactionEntity::TYPE_CREATE)
            )
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_LABEL, self::LABEL)
            )
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_TEXT, self::TEXT)
            )
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_ADD_TAG, $tagId1)
            )
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_ADD_TAG, $tagId2)
            )
            ->apply()
        ;
        $this->em->clear();

        $repo = $this->em->getRepository(self::ENTITY_DISCUSSION);
        $disq = $repo->findOneBy(array("label" => self::LABEL));
        self::assertCount(2, $disq->tags());

        $editor = $this->initEditor()
            ->setEntity($disq)
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_REMOVE_TAG, $tagId2)
            )
            ->apply()
        ;
        $this->em->clear();

        $repo = $this->em->getRepository(self::ENTITY_DISCUSSION);
        $disq = $repo->findOneBy(array("label" => self::LABEL));
        self::assertCount(1, $disq->tags());
        $tag = $disq->tags()->first();
        self::assertEquals($tagId1, $tag->tagId());
    }

}
