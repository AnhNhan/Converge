<?php

require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\Converge\Modules\Forum\ForumApplication;
use AnhNhan\Converge\Modules\Forum\Storage\Discussion;
use AnhNhan\Converge\Modules\Forum\Storage\Post;
use AnhNhan\Converge\Modules\Forum\Storage\DiscussionTransaction;
use AnhNhan\Converge\Modules\Forum\Storage\PostTransaction;
use AnhNhan\Converge\Modules\Forum\Transaction\DiscussionTransactionEditor;
use AnhNhan\Converge\Modules\Forum\Transaction\PostTransactionEditor;

use AnhNhan\Converge\Modules\Tag\TagApplication;
use AnhNhan\Converge\Modules\Tag\Storage\Tag;
use AnhNhan\Converge\Modules\Tag\Storage\TagTransaction;
use AnhNhan\Converge\Modules\Tag\Transaction\TagTransactionEditor;

use AnhNhan\Converge\Modules\User\UserApplication;
use AnhNhan\Converge\Modules\User\Query\RoleQuery;
use AnhNhan\Converge\Modules\User\Storage\Email;
use AnhNhan\Converge\Modules\User\Storage\User;
use AnhNhan\Converge\Modules\User\Storage\UserTransaction;
use AnhNhan\Converge\Modules\User\Transaction\UserTransactionEditor;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Types\UID;

use Doctrine\ORM\Query as DoctrineQuery;

// De-register libphutil autoloader, can't be used with Faker
spl_autoload_unregister('__phutil_autoload');

$container = \AnhNhan\Converge\Web\Core::loadSfDIContainer();

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

$pwEncoderFactory = $userApp->getService('security.encoder.factory');
$pwEncoder        = $pwEncoderFactory->getEncoder($obj_user);

$users = array();
$emails = array();
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
        $pw      = $pwEncoder->encodePassword($password, $salt);
        $xact_pw = $salt . UserTransactionEditor::SALT_PW_SEPARATOR . $pw;

        $editor = UserTransactionEditor::create($userEm)
            ->setActor(User::USER_UID_NONE)
            ->setFlushBehaviour(DiscussionTransactionEditor::FLUSH_DONT_FLUSH)
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

        $users[] = $obj_user;
        $emails[] = $obj_email;
    } catch (Exception $e) {
        echo "F";
        //throw $e;
        // <ignore>
    }
}
$userEm->flush();
foreach ($emails as $email)
{
    $userEm->persist($email);
}
$userEm->flush();

$randomUser = function () use ($users) {
    return $users[array_rand($users)]->uid;
};

$tags = ipull($tagEm->createQuery("SELECT t.uid FROM AnhNhan\Converge\Modules\Tag\Storage\Tag t")->getResult(DoctrineQuery::HYDRATE_ARRAY), 'uid');

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
