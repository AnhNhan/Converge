<?php

require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\ModHub\Modules\Forum\ForumApplication;
use AnhNhan\ModHub\Modules\Forum\Storage\Discussion;
use AnhNhan\ModHub\Modules\Forum\Storage\Post;
use AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\ModHub\Modules\Forum\Storage\PostTransaction;
use AnhNhan\ModHub\Modules\Forum\Transaction\DiscussionTransactionEditor;
use AnhNhan\ModHub\Modules\Forum\Transaction\PostTransactionEditor;

use AnhNhan\ModHub\Modules\Tag\TagApplication;
use AnhNhan\ModHub\Modules\Tag\Storage\Tag;
use AnhNhan\ModHub\Modules\Tag\Storage\TagTransaction;
use AnhNhan\ModHub\Modules\Tag\Transaction\TagTransactionEditor;

use AnhNhan\ModHub\Modules\User\UserApplication;
use AnhNhan\ModHub\Modules\User\Storage\User;

use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;
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
$stopwatch = $container->get("stopwatch");

$num_tags = 12;
$num_discussions = 15;
$num_posts = 200;
$num_users = 40;

function generateContent($faker)
{
    $paragraphCount = mt_rand(1, 4);

    $paragraphs = array();
    for ($ii = 0; $ii < $paragraphCount; $ii++) {
        $paragraphLength = mt_rand(100, 500);
        $paragraphs[] = $faker->text($paragraphLength);
    }

    return implode("\n\n", $paragraphs);
}

$timer = $stopwatch->start("db-seeding");

$users = array();
for ($ii = 0; $ii < $num_users; $ii++) {
    $users[] = UID::generate("USER");
}

$randomUser = function () use ($users) {
    return $users[array_rand($users)];
};

$tags = array();
for ($ii = 0; $ii < $num_tags; $ii++) {
    $tag = new Tag;
    $editor = TagTransactionEditor::create($tagEm)
        ->setEntity($tag)
        ->setActor($randomUser())
        ->setFlushBehaviour(TagTransactionEditor::FLUSH_DONT_FLUSH)
        ->addTransaction(
            TagTransaction::create(TransactionEntity::TYPE_CREATE)
        )
        ->addTransaction(
            TagTransaction::create(TagTransaction::TYPE_EDIT_LABEL, $faker->unique()->city)
        )
    ;
    $editor->apply();

    $tags[] = $tag;
}
$tagEm->flush();

$randomTag = function () use ($tags) {
    return $tags[array_rand($tags)];
};

$discussions = array();
for ($ii = 0; $ii < $num_discussions; $ii++) {
    $discussion = new Discussion();

    $editor = DiscussionTransactionEditor::create($forumEm)
        ->setActor($randomUser())
        ->setEntity($discussion)
        ->setFlushBehaviour(DiscussionTransactionEditor::FLUSH_DONT_FLUSH)
        ->addTransaction(
            DiscussionTransaction::create(TransactionEntity::TYPE_CREATE)
        )
        ->addTransaction(
            DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_LABEL, $faker->unique()->catchPhrase)
        )
        ->addTransaction(
            DiscussionTransaction::create(DiscussionTransaction::TYPE_EDIT_TEXT, generateContent($faker))
        )
    ;

    $editor->apply();

    $discussions[] = $discussion;
}
$forumEm->flush();

foreach ($discussions as $disq) {
    $derp = array();
    for ($jj = 0; $jj < 7; $jj++) {
        // That int is the probability n/10 of skipping
        if ($faker->randomDigit < 5) {
            continue;
        }

        $chosenTag = $randomTag();

        if (isset($derp[$chosenTag->uid()])) {
            continue;
        }

        $derp += array($chosenTag->uid() => true);

        $editor = DiscussionTransactionEditor::create($forumEm)
            ->setActor($disq->authorId)
            ->setEntity($disq)
            ->setFlushBehaviour(DiscussionTransactionEditor::FLUSH_DONT_FLUSH)
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_ADD_TAG, $chosenTag->uid())
            )
            ->apply()
        ;
    }
}
$forumEm->flush();

$randomDisq = function () use ($discussions) {
    return $discussions[array_rand($discussions)];
};

$posts = array();
for ($ii = 0; $ii < $num_posts; $ii++) {
    $disq = $randomDisq();
    $post = Post::initializeForDiscussion($disq);
    $editor = PostTransactionEditor::create($forumEm)
        ->setActor($randomUser())
        ->setEntity($post)
        ->setFlushBehaviour(PostTransactionEditor::FLUSH_DONT_FLUSH)
        ->addTransaction(
            PostTransaction::create(TransactionEntity::TYPE_CREATE, $disq->uid())
        )
        ->addTransaction(
            PostTransaction::create(PostTransaction::TYPE_EDIT_POST, generateContent($faker))
        )
        ->apply()
    ;
    $posts[] = $post;
}
$forumEm->flush();

$randomPost = function () use ($posts) {
    return $posts[array_rand($posts)];
};

$timer->stop();

echo str_repeat(PHP_EOL, 2);
echo sprintf("Done. Took me %dms.", $timer->getDuration()) . PHP_EOL;
