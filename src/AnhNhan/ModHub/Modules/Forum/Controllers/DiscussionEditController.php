<?php
namespace AnhNhan\ModHub\Modules\Forum\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\Forum\Query\DiscussionQuery;
use AnhNhan\ModHub\Modules\Forum\Storage\Discussion;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\ModHub\Modules\Forum\Storage\Post;
use AnhNhan\ModHub\Modules\Forum\Transaction\DiscussionTransactionEditor;
use AnhNhan\ModHub\Modules\Tag\TagQuery;
use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;
use AnhNhan\ModHub\Views\Form\FormView;
use AnhNhan\ModHub\Views\Form\Controls\SubmitControl;
use AnhNhan\ModHub\Views\Form\Controls\TextAreaControl;
use AnhNhan\ModHub\Modules\Tag\Views\FormControls\TagSelector;
use AnhNhan\ModHub\Views\Form\Controls\TextControl;
use AnhNhan\ModHub\Views\Panel\Panel;
use AnhNhan\ModHub\Web\Application\HtmlPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class DiscussionEditController extends AbstractForumController
{
    public function handle()
    {
        $request = $this->request;
        $requestMethod = $request->getMethod();

        $container = new MarkupContainer;
        $payload = new HtmlPayload;
        $payload->setPayloadContents($container);

        $errors = array();

        if ($disqId = $request->request->get('id')) {
            $query = $this->buildQuery();
            $discussion = $query->retrieveDiscussion("DISQ-" . $disqId);

            if (!$discussion) {
                return $payload->setPayloadContents(ModHub\ht("h1", "Discussion does not exist"));
            }

            $query->fetchExternalsForDiscussions(array($discussion));
        } else {
            $discussion = new Discussion;
        }

        $label = $discussion->label;
        $text  = $discussion->text;
        $origTags = mpull($discussion->tags ? $discussion->tags->toArray() : array(), "tag");
        $tags = mpull($origTags, "label");

        if ($requestMethod == "POST") {
            $label = trim($request->request->get("label"));
            $text = trim($request->request->get("text"));
            $_tags = $request->request->get("tags");

            if (empty($label)) {
                $errors[] = "We need a label for the discussion";
            }

            if (empty($text)) {
                $errors[] = "Please write a text so other people know how to respond";
            }

            // Parse tags
            if (preg_match('/^\\[.*\\]$/', $_tags)) {
                // It's a Json array
                $tags = json_decode($_tags);

                // Validate JSON structure
                try {
                    foreach ($tags as $_) {
                        assert_stringlike($_);
                        if (empty($_)) {
                            $errors[] = "Somehow you could sneak up an empty tag?...";
                            throw new \InvalidArgumentException;
                        }
                    }
                } catch (\InvalidArgumentException $e) {
                    // <ignore>

                    $errors[] = "Invalid tags";
                }
            } else {
                // A, B, C
                $tags = explode(',', $_tags);
                array_walk($tags, function ($string) { return trim($string); });
            }

            // Tags empty?
            if (!$tags) {
                $errors[] = "We can't create an discussion without any tags";
            }

            // Load tags
            $tagApp = $this->app->getService("app.list")->app("tag");
            $tagQuery  = new TagQuery($tagApp->getEntityManager());
            $tagObjects = $tagQuery->retrieveTagsForLabels($tags);

            // Validate tags
            // Far-future TODO: Put suggestions there?
            if (count($tagObjects) != count($tags)) {
                $tabOjectLabels = mpull($tagObjects, "label");
                $diffTags = array_diff($tags, $tabOjectLabels);
                $errors[] = sprintf("The following tags are invalid: '%s'", implode("', '", $diffTags));
            }

            if (!$errors) {
                $app = $this->app;
                $em = $app->getEntityManager();

                $origTagIds = mpull($origTags, "uid");
                $currTagIds = mpull($tagObjects, "uid");

                $newTagIds = array_diff($currTagIds, $origTagIds);
                $delTagIds = array_diff($origTagIds, $currTagIds);

                $editor = DiscussionTransactionEditor::create($em)
                    ->setActor(\AnhNhan\ModHub\Storage\Types\UID::generate("USER"))
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

                $editor->apply();

                $targetURI = "/disq/" . $discussion->cleanId;
                return new RedirectResponse($targetURI);
            }
        }

        if ($errors) {
            $panel = new Panel;
            $panel->setHeader(h2("Oops, something looks wrong"));
            $panel->append(ModHub\ht("p", "We can't continue until these issue(s) have been resolved:"));

            $list = ModHub\ht("ul");
            foreach ($errors as $e) {
                $list->appendContent(ModHub\ht("li", $e));
            }
            $panel->append($list);

            $container->push($panel);
        }

        if (is_array($tags)) {
            $tags = implode(", ", $tags);
        }

        $form = new FormView;
        $form->setDualColumnMode(false);
        $form
            ->setTitle("New discussion")
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
        $container->push(ModHub\ht("div", "Foo")->addClass("markup-preview-output"));

        $this->app->getService("resource_manager")
            ->requireJS("application-forum-tag-selector")
            ->requireJs("application-forum-markup-preview");

        return $payload;
    }
}
