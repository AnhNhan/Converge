<?php

require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\Converge as cv;

use AnhNhan\Converge\Modules\Tag\TagApplication;
use AnhNhan\Converge\Modules\Tag\Storage\Tag;
use AnhNhan\Converge\Modules\Tag\Storage\TagTransaction;
use AnhNhan\Converge\Modules\Tag\Transaction\TagTransactionEditor;

use AnhNhan\Converge\Modules\User\UserApplication;

use AnhNhan\Converge\Modules\Tag\TagQuery;
use AnhNhan\Converge\Modules\User\Query\UserQuery;

use AnhNhan\Converge\Storage\Transaction\TransactionEntity;
use AnhNhan\Converge\Storage\Types\UID;

use Symfony\Component\Yaml\Yaml;

$container = \AnhNhan\Converge\Web\Core::loadSfDIContainer();

$tagApp = new TagApplication;
$tagApp->setContainer($container);
$tagEm  = $tagApp->getEntityManager();
$tagRepo = $tagEm->getRepository('AnhNhan\Converge\Modules\Tag\Storage\Tag');

$defaultTagsConfigPath = cv\get_root_super() . 'resources/default.tags.yml';
$parsed = Yaml::parse($defaultTagsConfigPath);
$defaultTags = $parsed['tags'];

$userQuery = new UserQuery(id(new UserApplication)->setContainer($container));
$anh_nhan  = $userQuery->retrieveUsersForCanonicalNames(['anhnhan']);
$anh_nhan  = head($anh_nhan);
if (!$anh_nhan)
{
    throw new Exception('User Anh Nhan does not exist.');
}

echo "Using: {$defaultTagsConfigPath}\n\n";

foreach ($defaultTags as $tagValues) {
    $tagName = $tagValues['name'];
    $tag = $tagRepo->findOneBy(array('label' => $tagName));
    if (!$tag) {
        $tag = new Tag;

        $editor = TagTransactionEditor::create($tagEm)
            ->setActor($anh_nhan->uid)
            ->setEntity($tag)
            ->setBehaviourOnNoEffect(TagTransactionEditor::NO_EFFECT_SKIP)
            ->addTransaction(
                TagTransaction::create(TransactionEntity::TYPE_CREATE)
            )
            ->addTransaction(
                TagTransaction::create(TagTransaction::TYPE_EDIT_LABEL, $tagName)
            )
        ;

        if ($color = idx($tagValues, 'color'))
        {
            $editor->addTransaction(
                TagTransaction::create(TagTransaction::TYPE_EDIT_COLOR, $color)
            );
        }

        if ($description = idx($tagValues, 'description'))
        {
            $editor->addTransaction(
                TagTransaction::create(TagTransaction::TYPE_EDIT_DESC, $description)
            );
        }

        if ($displayOrder = idx($tagValues, 'displayOrder'))
        {
            $editor->addTransaction(
                TagTransaction::create(TagTransaction::TYPE_EDIT_ORDER, $displayOrder)
            );
        }

        $editor->apply();
        echo " [I] - Inserted '{$tagName}'\n";
    } else {
        echo " [S] - Found '{$tag->label}', doing nothing\n";
    }
}

echo "\nDone.\n";
