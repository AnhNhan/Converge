<?php
namespace AnhNhan\Converge\Modules\Markup;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class MarkupTokenStorage
{
    /**
     * @var \diff\utils\MarkupDiffBlockStorage
     */
    private $block_storage;
    private $token_sets = [];

    public function __construct()
    {
        $this->block_storage = new \diff\utils\MarkupDiffBlockStorage;
    }

    public function __call($method_name, array $args)
    {
        return call_user_method_array($method_name, $this->block_storage, $args);
    }

    public function addTokenToSet($set_name, $token, array $metadata = [])
    {
        if (!isset($this->token_sets[$set_name]))
        {
            $this->token_sets[$set_name] = [];
        }

        $this->token_sets[$set_name][$token] = $metadata;
        return $this;
    }

    public function getTokenSet($set_name)
    {
        return idx($this->token_sets, $set_name, []);
    }
}
