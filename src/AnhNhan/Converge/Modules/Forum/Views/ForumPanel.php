<?php
namespace AnhNhan\Converge\Modules\Forum\Views;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\Forum\Storage\ForumComment;
use AnhNhan\Converge\Modules\Tag\Storage\Tag;
use AnhNhan\Converge\Modules\Tag\Views\TagView;
use AnhNhan\Converge\Views\Panel\Panel;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class ForumPanel extends Panel
{
    private $uid;
    private $comments = [];

    public function setId($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    public function addComment(ForumComment $comment)
    {
        $this->comments[] = $comment;
        return $this;
    }

    public function render()
    {
        $panel = parent::render()
            ->setId($this->uid)
        ;

        $inner = head($panel->getContent()->getMarkupData());
        $panel_body = last($inner->getContent()->getMarkupData());

        $panel_body->append(div()->setId($this->uid . '-comments'));

        foreach ($this->comments as $comment)
        {
            $panel_body->append(panel()
                ->setId($comment->uid)
                ->addClass('forum-tail-object forum-tail-object-comment')
                ->append(cv\ht('img')
                    ->addOption('src', $comment->author->getGravatarImagePath(34))
                    ->addClass('user-profile-image')
                )
                ->append(div('comment-date pull-right hidden-phone', $comment->createdAt->format("D, d M 'y")))
                ->append(div('forum-comment', strong(link_user($comment->author)))->append(' ')->append($comment->rawText))
            );
        }

        $panel_body->append(panel()
            ->addClass('forum-tail-object')
            ->append(div('forum-tail-comment-form-container', form(null, 'forum/' . $this->uid . '/comment', 'POST')
                ->setDualColumnMode(false)
                ->addClass('forum-tail-comment-form')
                ->append(form_textcontrol(null, 'comment_text')
                    ->addOption('placeholder', 'Suggest improvements, request clarifications, point out flaws, or just insult the author here ;)')
                )
                ->append(form_submitcontrol(null, ' '))
            ))
        );

        return $panel;
    }
}
