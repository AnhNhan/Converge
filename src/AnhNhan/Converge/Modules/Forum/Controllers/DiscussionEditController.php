<?php
namespace AnhNhan\Converge\Modules\Forum\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\Forum\Activity\DiscussionRecorder;
use AnhNhan\Converge\Modules\Forum\Storage\Discussion;
use AnhNhan\Converge\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\Converge\Modules\Forum\Transaction\DiscussionTransactionEditor;
use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Views\Form\FormView;
use AnhNhan\Converge\Views\Form\Controls\SubmitControl;
use AnhNhan\Converge\Views\Form\Controls\TextAreaControl;
use AnhNhan\Converge\Modules\Tag\Views\FormControls\TagSelector;
use AnhNhan\Converge\Views\Form\Controls\TextControl;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionEditController extends AbstractForumController
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
        $requestMethod = $request->getMethod();

        $container = new MarkupContainer;
        $payload = $this->payload_html;
        $payload->setPayloadContents($container);

        $errors = array();

        if ($disqId = $request->request->get('id')) {
            $query = $this->buildQuery();
            $discussion = $query->retrieveDiscussion("DISQ-" . $disqId);

            if (!$discussion) {
                return $payload->setPayloadContents(Converge\ht("h1", "Discussion does not exist"));
            }

            $query->fetchExternalsForDiscussions(array($discussion));
        } else {
            $discussion = new Discussion;
        }

        $uid = $discussion->uid;
        $label = $discussion->label;
        $text  = $discussion->text;
        $origTags = mpull($discussion->tags ? $discussion->tags->toArray() : array(), "tag");
        $tags = mpull($origTags, "label");

        if ($requestMethod == "POST") {
            $label = trim($request->request->get("label"));
            $text = trim($request->request->get("text"));
            $_tags = $request->request->get("tags");
            $text = Converge\normalize_newlines($text);

            if (empty($label)) {
                $errors[] = "We need a label for the discussion";
            }

            if (empty($text)) {
                $errors[] = "Please write a text so other people know how to respond";
            }

            $tag_result = validate_tags_from_form_input($_tags, $this->externalApp('tag'));
            $tag_result or $tag_result = "We can't create a discussion without any tags";

            if (is_array($tag_result))
            {
                $tagObjects = $tag_result;
                $tags = mpull($tagObjects, 'label');
            }
            else
            {
                $errors[] = $tag_result;
            }

            if (!$errors) {
                $app = $this->app;
                $em = $app->getEntityManager();

                $origTagIds = mpull($origTags, "uid");
                $currTagIds = mpull($tagObjects, "uid");

                $newTagIds = array_diff($currTagIds, $origTagIds);
                $delTagIds = array_diff($origTagIds, $currTagIds);

                $editor = DiscussionTransactionEditor::create($em)
                    ->setActor($this->user->uid)
                    ->setEntity($discussion)
                ;

                if (!$disqId) {
                    $editor->addTransaction(
                        DiscussionTransaction::create(TransactionEntity::TYPE_CREATE)
                    );
                }

                $editor
                    ->addTransaction(
                        DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_LABEL, $label)
                    )
                    ->addTransaction(
                        DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_TEXT, $text)
                    )
                ;

                foreach ($newTagIds as $tag_id) {
                    $editor->addTransaction(
                        DiscussionTransaction::create(DiscussionTransaction::TYPE_ADD_TAG, $tag_id)
                    );
                }

                foreach ($delTagIds as $tag_id) {
                    $editor->addTransaction(
                        DiscussionTransaction::create(DiscussionTransaction::TYPE_REMOVE_TAG, $tag_id)
                    );
                }

                $activityRecorder = new DiscussionRecorder($this->externalApp('activity'));
                $activityRecorder->record($editor->apply());

                $targetURI = "/disq/" . $discussion->cleanId;
                return new RedirectResponse($targetURI);
            }
        }

        if ($errors) {
            $panel = panel(h2("Oops, something looks wrong"));
            $panel->append(Converge\ht("p", "We can't continue until these issue(s) have been resolved:"));

            $list = Converge\ht("ul");
            foreach ($errors as $e) {
                $list->append(Converge\ht("li", $e));
            }
            $panel->append($list);

            $container->push($panel);
        }

        if (is_array($tags)) {
            $tags = implode(", ", $tags);
        }

        $page_title = ($uid ? "Edit Discussion '$label'" : "New discussion") . " - Converge Discussions";
        $payload->setTitle($page_title);

        $form = new FormView;
        $form->setDualColumnMode(false);
        $form
            ->setTitle($page_title)
            ->setAction($request->getPathInfo())
            ->setMethod("POST");

        $form->append(id(new TextControl)
            ->setLabel("Label")
            ->setName("label")
            ->setValue($label));

        $form->append(id(new TagSelector)
            ->addClass("disq-tag-selector")
            ->setLabel("Tags")
            ->setValue($tags)
            ->setName("tags"));

        $form->append(id(new TextAreaControl)
            ->addClass("forum-markup-processing-form")
            ->setLabel("Text")
            ->setName("text")
            ->setValue($text));

        $form->append(id(new SubmitControl)
            ->addCancelButton("/disq/" . ($discussion->cleanId ?: null))
            ->addSubmitButton("Hasta la vista!"));

        $container->push($form);
        $container->push(Converge\ht("div", "Foo")->addClass("markup-preview-output"));

        $this->app->getService("resource_manager")
            ->requireJS("application-forum-tag-selector")
            ->requireJs("application-forum-markup-preview");

        return $payload;
    }
}
