<?php
namespace AnhNhan\Converge\Views\Form\Controls;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class PasswordControl extends AbstractFormControl
{
    public function __construct()
    {
        parent::__construct();
        $this->addClass('form-control-password');
    }

    protected function getType()
    {
        return 'password';
    }
}
