<?php
namespace AnhNhan\Converge\Modules\Newsroom\Storage;

use AnhNhan\Converge\Modules\Tag\Storage\Tag;
use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Types\UID;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Cache("NONSTRICT_READ_WRITE")
 * @Table
 */
class ArticleTag extends EntityDefinition
{
    /**
     * @Id
     * @ManyToOne(targetEntity="Article", fetch="EAGER", inversedBy="tags")
     * @var Article
     */
    private $article;

    /**
     * @Id
     * @Column(type="string")
     *
     * @var string
     */
    private $tag_uid;

    /**
     * @var Tag
     */
    private $t_obj;

    /**
     * @Column(type="float")
     * @var float
     */
    public $strength = 1.0;

    public function __construct(Article $article, $tag)
    {
        $this->article = $article;
        if (is_object($tag)) {
            $this->tag_uid = $tag->uid();
            $this->t_obj = $tag;
        } else {
            // We only received a UID string
            UID::checkValidity($tag);
            $this->tag_uid = $tag;
        }
    }

    public function article()
    {
        return $this->article;
    }

    public function articleId()
    {
        return $this->article->uid();
    }

    public function tag()
    {
        if (!$this->t_obj) {
            throw new \Exception("This object hasn't been initialized with a tag yet!");
        }
        return $this->t_obj;
    }

    public function setTag(Tag $tag)
    {
        if ($tag->uid !== $this->tag_uid) {
            throw new \InvalidArgumentException("UIDs do not match!");
        }
        $this->t_obj = $tag;
        return $this;
    }

    public function tagId()
    {
        return $this->tag_uid;
    }
}
