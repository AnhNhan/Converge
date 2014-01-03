<?php
namespace Transaction;
use Codeception\Util\Stub;

use AnhNhan\ModHub\Modules\Tag\Storage\Tag;
use AnhNhan\ModHub\Modules\Tag\Storage\TagTransaction;
use AnhNhan\ModHub\Modules\Tag\Transaction\TagTransactionEditor;
use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;
use AnhNhan\ModHub\Storage\Types\UID;

class TransactionEditorTest extends \Codeception\TestCase\Test
{
   /**
    * @var \TagTestGuy
    */
    protected $tagTestGuy;

    private $editor;

    private $user;

    private $em;

    const ENTITY_TAG = 'AnhNhan\ModHub\Modules\Tag\Storage\Tag';

    const LABEL  = 'foo label text';
    const LABEL2 = 'blurb label text';

    const DESCRIPTION = 'Bar description';

    protected function _before()
    {
        $this->em     = \Codeception\Module\Doctrine2::$em;

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
        $this->editor = new TagTransactionEditor($this->em);
        $this->editor->setActor($this->user);
    }

    public function testCreateNewTag()
    {
        $tag = new Tag;

        $this->tagTestGuy->dontSeeInRepository(self::ENTITY_TAG, array("label" => self::LABEL));

        $editor = $this->editor;
        $editor->setEntity($tag)
            ->addTransaction(
                TagTransaction::create(TransactionEntity::TYPE_CREATE)
            )
            ->addTransaction(
                TagTransaction::create(TagTransaction::TYPE_EDIT_LABEL, self::LABEL)
            )
        ;

        self::assertEquals(null, $tag->label());
        $result = $editor->apply();
        self::assertCount(2, $result, print_r(mpull($result, "type"), true));
        self::assertEquals(TransactionEntity::TYPE_CREATE, $result[0]->type());
        self::assertEquals(TagTransaction::TYPE_EDIT_LABEL, $result[1]->type());
        self::assertEquals(self::LABEL, $tag->label());

        $tagId = $tag->uid();

        $this->em->clear();

        $this->tagTestGuy->seeInRepository(self::ENTITY_TAG, array("id" => $tagId));
        $repo = $this->em->getRepository(self::ENTITY_TAG);
        $tag = $repo->find($tagId);
        self::assertEquals(self::LABEL, $tag->label());
        $xacts = $tag->transactions()->toArray();
        self::assertCount(2, $xacts, print_r(mpull($xacts, "type"), true));
        self::assertEquals($this->user, $xacts[0]->actorId());
        self::assertEquals($this->user, $xacts[1]->actorId());
    }

    public function testEditTag()
    {
        // Continuation from previous test
        $this->testCreateNewTag();
        $this->em->clear();
        $oldUser = $this->user;
        $this->initUser(); // Testing with another user
        $newUser = $this->user;
        $this->initEditor();

        $repo = $this->em->getRepository(self::ENTITY_TAG);
        $tag = $repo->findOneBy(array("label" => self::LABEL));

        $editor = $this->editor;
        $editor->setEntity($tag)
            ->addTransaction(
                TagTransaction::create(TagTransaction::TYPE_EDIT_LABEL, self::LABEL2)
            )
            ->addTransaction(
                TagTransaction::create(TagTransaction::TYPE_EDIT_DESC, self::DESCRIPTION)
            )
        ;

        $result = $editor->apply();

        $this->tagTestGuy->dontSeeInRepository(self::ENTITY_TAG, array("label" => self::LABEL));
        $this->tagTestGuy->seeInRepository(self::ENTITY_TAG, array("label" => self::LABEL2));

        $tagId = $tag->uid();

        $this->em->clear();
        $repo = $this->em->getRepository(self::ENTITY_TAG);
        $tag = $repo->find($tagId);
        self::assertEquals(self::LABEL2, $tag->label());
        self::assertEquals(self::DESCRIPTION, $tag->description());

        $xacts = $tag->transactions()->toArray();
        self::assertCount(4, $xacts, print_r(mpull($xacts, "type"), true));

        /* We are mangling up this test here, since their order is quite messed up
        self::assertEquals($oldUser, $xacts[0]->actorId());
        self::assertEquals($oldUser, $xacts[1]->actorId());

        self::assertEquals($newUser, $xacts[2]->actorId());
        self::assertEquals($newUser, $xacts[3]->actorId());

        self::assertEquals(self::LABEL, $xacts[2]->oldValue());
        self::assertEquals(self::LABEL2, $xacts[2]->newValue());
        */

        $sorted = mgroup($xacts, "actorId");
        self::assertCount(2, $sorted);
        foreach ($sorted as $xx) {
            self::assertCount(2, $xx); // Each author had two xacts
        }

        $xact = null;
        foreach ($xacts as $xx) { // Picking up
            if ($xx->type() == TagTransaction::TYPE_EDIT_LABEL && $xx->actorId() == $newUser) {
                $xact = $xx;
                break;
            }
        }
        self::assertEquals(self::LABEL, $xact->oldValue());
        self::assertEquals(self::LABEL2, $xact->newValue());
    }

}
