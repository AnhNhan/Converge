<?php

require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\ModHub\Modules\Forum\ForumApplication;
use AnhNhan\ModHub\Modules\Forum\Storage\Discussion;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTag;
use AnhNhan\ModHub\Modules\Forum\Storage\Post;

use AnhNhan\ModHub\Modules\Tag\TagApplication;
use AnhNhan\ModHub\Modules\Tag\Storage\Tag;

use AnhNhan\ModHub\Modules\User\UserApplication;
use AnhNhan\ModHub\Modules\User\Storage\User;

use AnhNhan\ModHub\Storage\Types\UID;

// De-register libphutil autoloader, can't be used with Faker
spl_autoload_unregister('__phutil_autoload');

$container = \AnhNhan\ModHub\Web\Core::loadSfDIContainer();

$forumApp = new ForumApplication();
$forumApp->setContainer($container);
$forumEm = $forumApp->getEntityManager();

$tagApp = new TagApplication;
$tagApp->setContainer($container);
$tagEm = $tagApp->getEntityManager();

$userApp = new UserApplication;
$userApp->setContainer($container);
$userEm  = $userApp->getEntityManager();

$faker = \Faker\Factory::create();

$num_tags = 12;
$num_discussions = 15;
$num_posts = 200;
$num_users = 40;

function generateContent($faker)
{
    $paragraphCount = mt_rand(1, 4);

    $paragraphs = array();
    for ($ii = 0; $ii < $paragraphCount; $ii++) {
        $paragraphLength = mt_rand(100, 1000);
        $paragraphs[] = $faker->text($paragraphLength);
    }

    return implode("\n\n", $paragraphs);
}

$tags = array();
for ($ii = 0; $ii < $num_tags; $ii++) {
    $tag = new Tag($faker->unique()->city);
    $tagEm->persist($tag);
    $tags[] = $tag;
}
$tagEm->flush();

$users = array();
for ($ii = 0; $ii < $num_users; $ii++) {
    $users[] = UID::generate("USER");
}

$discussions = array();
for ($ii = 0; $ii < $num_discussions; $ii++) {
    $discussion = new Discussion($users[array_rand($users)], $faker->unique()->catchPhrase, generateContent($faker), $faker->dateTime, $faker->dateTime);

    $discussions[] = $discussion;
}

$posts = array();
for ($ii = 0; $ii < $num_posts; $ii++) {
    $posts[] = new Post($discussions[array_rand($discussions)], $users[array_rand($users)], generateContent($faker), $faker->dateTime, $faker->dateTime);
}

$discussion_tags = array();
for ($ii = 0; $ii < $num_discussions; $ii++) {
    $derp = array();
    for ($jj = 0; $jj < 7; $jj++) {
        // That int is the probability n/10 of skipping
        if ($faker->randomDigit < 5) {
            continue;
        }

        $chosenTag = $tags[array_rand($tags)];
        $chosenDisq = $discussions[$ii];

        if (isset($derp[$chosenTag->uid()])) {
            continue;
        }

        $derp += array($chosenTag->uid() => true);
        $discussion_tags[] = new DiscussionTag($chosenDisq, $chosenTag);
    }
}

foreach ($discussions as $derp) {
    $forumEm->persist($derp);
}

foreach ($posts as $derp) {
    $forumEm->persist($derp);
}

foreach ($discussion_tags as $derp) {
    $forumEm->persist($derp);
}

$forumEm->flush();
