<?php

require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\ModHub\Modules\Forum\ForumApplication;
use AnhNhan\ModHub\Modules\Forum\Storage\Discussion;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTag;
use AnhNhan\ModHub\Modules\Forum\Storage\Post;
use AnhNhan\ModHub\Storage\Types\UID;

// De-register libphutil autoloader, can't be used with Faker
spl_autoload_unregister('__phutil_autoload');

$forumApp = new ForumApplication();
$forumEm = $forumApp->getEntityManager();
$faker = \Faker\Factory::create();

$num_tags = 12;
$num_discussions = 15;
$num_posts = 200;
$num_users = 40;

$tags = array();
for ($ii = 0; $ii < $num_tags; $ii++) {
    $tags[] = UID::generate("TTAG");
}

$users = array();
for ($ii = 0; $ii < $num_users; $ii++) {
    $users[] = UID::generate("USER");
}

$discussions = array();
$discussion_firstPosts = array();
for ($ii = 0; $ii < $num_discussions; $ii++) {
    $discussion = new Discussion($faker->unique()->catchPhrase, $faker->dateTime, $faker->dateTime);
    $firstPost = new Post($discussion, $users[array_rand($users)], $faker->text, $faker->dateTime, $faker->dateTime);
    $discussion->firstPost($firstPost);

    $discussions[] = $discussion;
    $discussion_firstPosts[] = $firstPost;
}

$posts = array();
for ($ii = 0; $ii < $num_posts; $ii++) {
    $posts[] = new Post($discussions[array_rand($discussions)], $users[array_rand($users)], $faker->text, $faker->dateTime, $faker->dateTime);
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

        if (isset($derp[$chosenTag])) {
            continue;
        }

        $derp += array($chosenTag => true);
        $discussion_tags[] = new DiscussionTag($chosenDisq, $chosenTag);
    }
}

foreach ($discussions as $derp) {
    $forumEm->persist($derp);
}

foreach ($discussion_firstPosts as $derp) {
    $forumEm->persist($derp);
}

foreach ($posts as $derp) {
    $forumEm->persist($derp);
}

foreach ($discussion_tags as $derp) {
    $forumEm->persist($derp);
}

$forumEm->flush();
