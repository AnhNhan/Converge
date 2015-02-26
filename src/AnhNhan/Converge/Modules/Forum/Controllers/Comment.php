<?php
namespace AnhNhan\Converge\Modules\Forum\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Views\Web\Response\ResponseHtml404;
use AnhNhan\Converge\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use AnhNhan\Converge\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\Converge\Modules\Forum\Storage\ForumComment;
use AnhNhan\Converge\Modules\Forum\Storage\ForumCommentTransaction;
use AnhNhan\Converge\Modules\Forum\Storage\PostTransaction;
use AnhNhan\Converge\Modules\Forum\Transaction\DiscussionTransactionEditor;
use AnhNhan\Converge\Modules\Forum\Transaction\ForumCommentEditor;
use AnhNhan\Converge\Modules\Forum\Transaction\PostTransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Transaction\TransactionEditor;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class Comment extends AbstractForumController
{
    public function requiredUserRoles($request)
    {
        return [
            'ROLE_USER',
        ];
    }

    public function handle()
    {
        $request = $this->request;
        $request_method = $request->getMethod();
        assert($request_method == 'POST');

        $query = $this->buildQuery();
        $object_id = $request->request->get('id');
        $object_type = uid_get_type($object_id);

        $enum_object_types = [
            'POST' => true,
            'DISQ' => true,
        ];
        $map_retrieval_method = [
            'POST' => [$query, 'retrievePost'],
            'DISQ' => [$query, 'retrieveDiscussion'],
        ];
        $map_editor_type = [
            'POST' => 'AnhNhan\Converge\Modules\Forum\Transaction\PostTransactionEditor',
            'DISQ' => 'AnhNhan\Converge\Modules\Forum\Transaction\DiscussionTransactionEditor',
        ];
        $map_xact_type = [
            'POST' => 'AnhNhan\Converge\Modules\Forum\Storage\PostTransaction',
            'DISQ' => 'AnhNhan\Converge\Modules\Forum\Storage\DiscussionTransaction',
        ];
        $record_event_type = [
            'POST' => Event_PostTransaction_Record,
            'DISQ' => Event_DiscussionTransaction_Record,
        ];
        assert(isset($enum_object_types[$object_type]));

        $object = $map_retrieval_method[$object_type]($object_id);
        if (!$object)
        {
            return (new ResponseHtml404)->setText("Object $object_id does not exist.");
        }

        $text = trim($request->request->get('comment_text'));
        $text = Converge\normalize_newlines($text);

        if (!strlen($text))
        {
            throw new \Exception('Text can\'t be empty!');
        }

        $editor_t = $map_editor_type[$object_type];
        $xact_t = $map_xact_type[$object_type];

        $em = $this->app->getEntityManager();
        $em->beginTransaction();
        try
        {
            $comment = new ForumComment;
            $comment_editor = ForumCommentEditor::create($this->app)
                ->setActor($this->user->uid)
                ->setEntity($comment)
                ->addTransaction(
                    ForumCommentTransaction::create(TransactionEntity::TYPE_CREATE, $object_id)
                )
                ->addTransaction(
                    ForumCommentTransaction::create(ForumCommentTransaction::TYPE_EDIT_TEXT, $text)
                )
                ->apply()
            ;
            $em->flush();

            $object->comments->add($comment);
            $editor = $editor_t::create($this->app)
                ->setActor($this->user->uid)
                ->setEntity($object)
                ->addTransaction(
                    $xact_t::create($xact_t::TYPE_ADD_COMMENT, $comment->uid)
                )
            ;
            $xacts = $editor->apply();
            $this->dispatchEvent($record_event_type[$object_type], arrayDataEvent($xacts));
            $em->flush();
            $em->commit();
        }
        catch (\Exception $e)
        {
            $em->rollback();
            throw $e;
        }

        $discussion = $object_type == 'DISQ' ? $object : $object->parentDisq;
        $post = $object_type == 'DISQ' ? null : $object;

        $target_uri = '/disq/' . $discussion->cleanId . '#' . $comment->uid;
        return new RedirectResponse($target_uri);
    }
}
