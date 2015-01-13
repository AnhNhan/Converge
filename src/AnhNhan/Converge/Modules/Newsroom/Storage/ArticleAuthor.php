<?php
namespace AnhNhan\Converge\Modules\Newsroom\Storage;

use AnhNhan\Converge\Modules\People\Storage\User;
use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Types\UID;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table
 */
class ArticleAuthor extends EntityDefinition
{
    /**
     * @Id
     * @ManyToOne(targetEntity="Article", fetch="EAGER", inversedBy="authors")
     * @var Article
     */
    private $article;

    /**
     * @Id
     * @Column(type="string")
     * @var string
     */
    private $user;

    /**
     * @var User
     */
    private $user_object;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $name;

    public function __construct(Article $article, $user)
    {
        $this->article = $article;
        $this->setUser($user);
    }

    public function article()
    {
        return $this->article;
    }

    public function articleId()
    {
        return $this->article->uid;
    }

    public function user()
    {
        return $this->user_object;
    }

    public function userId()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        if (is_object($user) && $user instanceof User) {
            $this->user = $user->uid;
            $this->user_object = $user;
        } else {
            // We only received a UID string
            UID::checkValidity($user);
            $this->user = $user;
        }
    }
}
