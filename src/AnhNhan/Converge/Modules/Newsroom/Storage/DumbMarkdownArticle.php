<?php
namespace AnhNhan\Converge\Modules\Newsroom\Storage;

use AnhNhan\Converge\Storage\EntityDefinition;
use AnhNhan\Converge\Storage\Transaction\TransactionAwareEntityInterface;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 * @Entity
 * @Table(indexes={
 *   @Index(name="idx_object", columns={"id", "object_id"})
 * })
 * @Cache
 */
class DumbMarkdownArticle extends Article
{

    /**
     * JSON string with settings.
     *
     * @Column(type="json_object_array")
     */
    protected $settings = [];

    /**
     * @Column(type="text")
     */
    protected $rawText = '';

    /**
     * @OneToMany(targetEntity="DMArticleTransaction", mappedBy="object", fetch="LAZY")
     * @var \Doctrine\ORM\PersistentCollection
     */
    private $xacts;

    public function settings()
    {
        return $this->settings;
    }

    public function get_setting($key, $default = null)
    {
        return idx($this->settings, $key, $default);
    }

    public function rawText()
    {
        return $this->rawText;
    }

    public function transactions()
    {
        return $this->xacts;
    }

    public function getUIDType()
    {
        return "ARTL-DMAR";
    }
}
