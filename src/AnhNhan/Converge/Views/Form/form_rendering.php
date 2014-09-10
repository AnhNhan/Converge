<?php

use AnhNhan\Converge\Views\Form\FormView;
use AnhNhan\Converge\Views\Form\Controls\HiddenControl;
use AnhNhan\Converge\Views\Form\Controls\SelectControl;
use AnhNhan\Converge\Views\Form\Controls\SubmitControl;
use AnhNhan\Converge\Views\Form\Controls\TextAreaControl;
use AnhNhan\Converge\Views\Form\Controls\TextControl;

function form($title = '', $action = null, $method = null)
{
    return (new FormView)
        ->setTitle($title)
        ->setAction($action)
        ->setMethod($method)
    ;
}

function form_textcontrol($label, $name = null, $value = null)
{
    return (new TextControl)
        ->setLabel($label)
        ->setName($name)
        ->setValue($value)
    ;
}

function form_textareacontrol($label, $name = null, $value = null)
{
    return (new TextAreaControl)
        ->setLabel($label)
        ->setName($name)
        ->setValue($value)
    ;
}

function form_submitcontrol($cancel_uri, $submit_text = 'Submit', $cancel_text = 'Cancel')
{
    return (new SubmitControl)
        ->addCancelButton($cancel_uri, $cancel_text)
        ->addSubmitButton($submit_text)
    ;
}

function form_hidden($name, $value)
{
    return (new HiddenControl)
        ->setName($name)
        ->setValue($value)
    ;
}
