<?php
$I = new TagAcceptanceGuy($scenario);
$I->wantTo('create a new tag');
$I->am('some random user (currently anonymous)');
$I->amGoingTo('use the form to create a new tag');

$label = "SomeRandomTag";

$I->amOnPage('/tag/');
$I->see('Tags');
$I->dontSee($label);

$I->click('Create new tag!');
$I->canSeeInCurrentUrl('create');
$I->see('Create new tag');
$I->submitForm('#tag-creation', array('label' => $label));

// Wait for redirect
sleep(2);
$I->see($label);

// See in listing
$I->amOnPage('/tag/');
$I->see($label);
