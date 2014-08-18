<?php
namespace AnhNhan\Converge\Views\Property;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Views\AbstractView;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class Entry extends AbstractView
{
    private $name;
    private $detail;

    public function __construct($name, $detail)
    {
        parent::__construct();
        $this->name = $name;
        $this->detail = $detail;
    }

    public function render()
    {
        $entry = div('property-list-entry');
        $entry->append(div('property-list-entry-name', $this->name));
        $entry->append(div('property-list-entry-detail', $this->detail));
        return $entry;
    }
}
