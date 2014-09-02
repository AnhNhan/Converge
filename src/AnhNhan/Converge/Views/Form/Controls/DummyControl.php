<?php
namespace AnhNhan\Converge\Views\Form\Controls;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class DummyControl extends AbstractFormControl
{
    private $object;
    public function __construct($object)
    {
        parent::__construct();
        $this->addClass('form-control-dummy');
        $this->setTagName('div');
        $this->append($object);
        $this->object;
    }

    public function render()
    {
        $rendered = parent::render();

        $contents = $rendered->getContent();
        $popped = $contents->pop();
        $popped_contents = $popped->getContent();
        head($popped_contents->getMarkupData())->removeClass('form-control');

        $contents->push($popped);

        return $rendered;
    }

    protected function getType()
    {
        return 'dummy';
    }
}
