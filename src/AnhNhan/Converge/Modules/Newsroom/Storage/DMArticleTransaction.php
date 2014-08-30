<?php
namespace AnhNhan\Converge\Modules\Newsroom\Storage;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table
 * @Cache
 */
class DMArticleTransaction extends ArticleTransaction
{
    const TYPE_EDIT_TEXT = 'dmarticle.edit.text';
    const TYPE_EDIT_SETTING = 'dmarticle.edit.setting';

    /**
     * @ManyToOne(targetEntity="DumbMarkdownArticle", inversedBy="xacts", fetch="EAGER")
     */
    protected $object;

    /**
     * @return Role
     */
    public function task()
    {
        return $this->object;
    }

    protected function getUIDSubType()
    {
        return "DMAR";
    }
}
