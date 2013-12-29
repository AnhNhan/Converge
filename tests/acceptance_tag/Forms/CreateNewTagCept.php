<?php
$I = new TagAcceptanceGuy($scenario);
$I->wantTo('create a new tag');
$I->am('some random user (currently anonymous)');
$I->amGoingTo('use the form to create a new tag');

$label = "Foo";

$I->amOnPage('/tag/');
$I->see('Tag listing');
$I->dontSee($label);

$I->click('Create new tag!');
$I->canSeeInCurrentUrl('create');
$I->see('Create new tag');
$I->submitForm('#tag-creation', array('label' => $label));

$I->amOnPage('/tag/');
$I->see($label);
