<?php

require_once __DIR__ . '/../src/__init__.php';

use AnhNhan\ModHub as mh;

use AnhNhan\ModHub\Modules\Tag\TagApplication;
use AnhNhan\ModHub\Modules\Tag\Storage\Tag;
use AnhNhan\ModHub\Modules\Tag\Storage\TagTransaction;
use AnhNhan\ModHub\Modules\Tag\Transaction\TagTransactionEditor;

use AnhNhan\ModHub\Modules\Tag\TagQuery;

use AnhNhan\ModHub\Storage\Transaction\TransactionEntity;
use AnhNhan\ModHub\Storage\Types\UID;

use Symfony\Component\Yaml\Yaml;

$container = \AnhNhan\ModHub\Web\Core::loadSfDIContainer();

$tagApp = new TagApplication;
$tagApp->setContainer($container);
$tagEm  = $tagApp->getEntityManager();
$tagRepo = $tagEm->getRepository('AnhNhan\ModHub\Modules\Tag\Storage\Tag');

$defaultTagsConfigPath = mh\get_root_super() . 'resources/default.tags.yml';
$parsed = Yaml::parse($defaultTagsConfigPath);
$defaultTags = $parsed['tags'];

echo "Using: {$defaultTagsConfigPath}\n\n";

foreach ($defaultTags as $tagValues) {
    $tagName = $tagValues['name'];
    $tag = $tagRepo->findOneBy(array('label' => $tagName));
    if (!$tag) {
        $tag = new Tag;

        $editor = TagTransactionEditor::create($tagEm)
            ->setActor(UID::generate())
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
