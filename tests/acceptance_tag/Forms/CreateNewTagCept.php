<?php
$I = new TagAcceptanceGuy($scenario);
$I->wantTo('create a new tag');

$label = "Foo";

$I->amOnPage('/tag/');
$I->dontSee($label);

$I->click('Create new tag!');
$I->canSeeInCurrentUrl('create');
$I->submitForm('#tag-creation', array('label' => $label));

$I->amOnPage('/tag/');
$I->see($label);
