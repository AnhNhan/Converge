<?php
$I = new ForumWebGuy($scenario);
$I->wantTo('simply create a new discussion');
$I->lookForwardTo('am able to take part in the never ending discussion machine without having to feed the admin trolls');

$I->am('some random user (hopefully logged in)');
$I->amOnPage('/disq/');
$I->see('Forum Listing');

$label = "Some random discussion";
$text  = "Blurby text";
$I->dontSee($label);
$I->click("Create new discussion");

$I->canSeeInCurrentUrl("/disq/create");
$I->see("new discussion");
$I->fillField("label", $label);
$I->fillField("text", $text);
$I->click("Hasta la vista"); // Warning! May be changed!

// Will be changed soon
$I->see("Successfully inserted discussion");
$I->click("Link");

$I->see($label);
$I->see($text);

// Will be changed
$I->click("Disq listing");
$I->see($label);
