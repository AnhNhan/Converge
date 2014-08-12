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
use AnhNhan\ModHub\Modules\User\Query\RoleQuery;
use AnhNhan\ModHub\Modules\User\Storage\Email;
use AnhNhan\ModHub\Modules\User\Storage\User;
use AnhNhan\ModHub\Modules\User\Storage\UserTransaction;
use AnhNhan\ModHub\Modules\User\Transaction\UserTransactionEditor;

use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;
use AnhNhan\ModHub\Storage\Types\UID;

use Doctrine\ORM\Query as DoctrineQuery;

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
$num_discussions = 50;
$num_posts = 800;
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

$roleQuery = new RoleQuery($userEm);
$role      = idx($roleQuery->retrieveRolesForNames(['ROLE_USER'], 1), 0);
if (!$role)
{
    throw new \LogicException('Something\'s terribly wrong here. Seeded default roles?');
}

$users = array();
for ($ii = 0; $ii < $num_users; $ii++) {
    try {
        $obj_user = new User;
        $obj_email = new Email;

        $username = $faker->unique->userName;
        $password = 'dummypw';
        $email    = $faker->unique->email;

        // Just saving us some elaborated code
        // TODO: Do this properly
        $obj_email->email = $email;
        $obj_email->user  = $obj_user;
        $obj_email->is_verified = true;
        $obj_email->is_primary  = true;

        // Set primary email of user object
        $userReflProp = $userEm->getClassMetadata(get_class($obj_user))
            ->reflClass->getProperty('primary_email');
        $userReflProp->setAccessible(true);
        $userReflProp->setValue(
            $obj_user, $email
        );

        $salt = User::generateSalt();
        $pwEncoderFactory = $userApp->getService('security.encoder.factory');
        $pwEncoder        = $pwEncoderFactory->getEncoder($obj_user);
        $pw               = $pwEncoder->encodePassword($password, $salt);
        $xact_pw          = $salt . UserTransactionEditor::SALT_PW_SEPARATOR . $pw;

        $editor = UserTransactionEditor::create($userEm)
            ->setActor(User::USER_UID_NONE)
            ->setEntity($obj_user)
        ;

        $editor
            ->addTransaction(
                UserTransaction::create(TransactionEntity::TYPE_CREATE, $username)
            )
            ->addTransaction(
                UserTransaction::create(UserTransaction::TYPE_EDIT_PASSWORD, $xact_pw)
            )
            ->addTransaction(
                UserTransaction::create(UserTransaction::TYPE_ADD_EMAIL, $email)
            )
            ->addTransaction(
                UserTransaction::create(UserTransaction::TYPE_ADD_ROLE, $role->uid)
            )
        ;

        $editor->apply();
        $userEm->flush();
        $userEm->persist($obj_email);
        $userEm->flush();
        $users[] = $obj_user->uid;
    } catch (Exception $e) {
        echo "F";
        //throw $e;
        // <ignore>
    }
}

$randomUser = function () use ($users) {
    return $users[array_rand($users)];
};

$tags = ipull($tagEm->createQuery("SELECT t.uid FROM AnhNhan\ModHub\Modules\Tag\Storage\Tag t")->getResult(DoctrineQuery::HYDRATE_ARRAY), 'uid');

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

        if (isset($derp[$chosenTag])) {
            continue;
        }

        $derp += array($chosenTag => true);

        $editor = DiscussionTransactionEditor::create($forumEm)
            ->setActor($disq->authorId)
            ->setEntity($disq)
            ->setFlushBehaviour(DiscussionTransactionEditor::FLUSH_DONT_FLUSH)
            ->addTransaction(
                DiscussionTransaction::create(DiscussionTransaction::TYPE_ADD_TAG, $chosenTag)
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
