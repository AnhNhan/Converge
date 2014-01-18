<?php
// -----------------------------------------------------------------------------
/**
 *  This file was generated by the SymbolGenerator(tm)
 *  Would be cool if you wouldn't edit it, as that would sure break things
 *
 *  To re-generate this file, run `php -f scripts/generate_symbol_list.php`
 *
 *  Thank you
 *  @love Anh Nhan <anhnhan@outlook.com>
 *
 *  @generated
 */
// -----------------------------------------------------------------------------

return array(
  "classes" => array(
    "AnhNhan\ModHub\Console\ConsoleCommand" => array(
      "file" => "AnhNhan/ModHub/Console/ConsoleCommand.php",
      "deriv" => "Symfony\Component\Console\Command\Command",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Modules\Database\Command\AbstractDbCommand" => array(
      "file" => "AnhNhan/ModHub/Modules/Database/Command/AbstractDbCommand.php",
      "deriv" => "AnhNhan\ModHub\Console\ConsoleCommand",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Modules\Database\Command\DoctrineManager" => array(
      "file" => "AnhNhan/ModHub/Modules/Database/Command/DoctrineManager.php",
      "deriv" => "AnhNhan\ModHub\Modules\Database\Command\AbstractDbCommand",
    ),
    "AnhNhan\ModHub\Modules\Examples\Controllers\StandardExamplesController" => array(
      "file" => "AnhNhan/ModHub/Modules/Examples/Controllers/StandardExamplesController.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplicationController",
    ),
    "AnhNhan\ModHub\Modules\Examples\Examples\AbstractExample" => array(
      "file" => "AnhNhan/ModHub/Modules/Examples/Examples/AbstractExample.php",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Modules\Examples\Examples\BootstrapExample" => array(
      "file" => "AnhNhan/ModHub/Modules/Examples/Examples/BootstrapExample.php",
      "deriv" => "AnhNhan\ModHub\Modules\Examples\Examples\AbstractExample",
    ),
    "AnhNhan\ModHub\Modules\Examples\Examples\FormExample" => array(
      "file" => "AnhNhan/ModHub/Modules/Examples/Examples/FormExample.php",
      "deriv" => "AnhNhan\ModHub\Modules\Examples\Examples\AbstractExample",
    ),
    "AnhNhan\ModHub\Modules\Examples\Examples\ForumListingExample" => array(
      "file" => "AnhNhan/ModHub/Modules/Examples/Examples/ForumListingExample.php",
      "deriv" => "AnhNhan\ModHub\Modules\Examples\Examples\AbstractExample",
    ),
    "AnhNhan\ModHub\Modules\Examples\Examples\PanelExample" => array(
      "file" => "AnhNhan/ModHub/Modules/Examples/Examples/PanelExample.php",
      "deriv" => "AnhNhan\ModHub\Modules\Examples\Examples\AbstractExample",
    ),
    "AnhNhan\ModHub\Modules\Examples\ExamplesApplication" => array(
      "file" => "AnhNhan/ModHub/Modules/Examples/ExamplesApplication.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplication",
    ),
    "AnhNhan\ModHub\Modules\Forum\Controllers\AbstractForumController" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Controllers/AbstractForumController.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplicationController",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Modules\Forum\Controllers\DiscussionDisplayController" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Controllers/DiscussionDisplayController.php",
      "deriv" => "AnhNhan\ModHub\Modules\Forum\Controllers\AbstractForumController",
    ),
    "AnhNhan\ModHub\Modules\Forum\Controllers\DiscussionEditController" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Controllers/DiscussionEditController.php",
      "deriv" => "AnhNhan\ModHub\Modules\Forum\Controllers\AbstractForumController",
    ),
    "AnhNhan\ModHub\Modules\Forum\Controllers\DiscussionListingController" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Controllers/DiscussionListingController.php",
      "deriv" => "AnhNhan\ModHub\Modules\Forum\Controllers\AbstractForumController",
    ),
    "AnhNhan\ModHub\Modules\Forum\Controllers\PostEditController" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Controllers/PostEditController.php",
      "deriv" => "AnhNhan\ModHub\Modules\Forum\Controllers\AbstractForumController",
    ),
    "AnhNhan\ModHub\Modules\Forum\Events\DiscussionTagExternalEntityLoader" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Events/DiscussionTagExternalEntityLoader.php",
    ),
    "AnhNhan\ModHub\Modules\Forum\ForumApplication" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/ForumApplication.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplication",
    ),
    "AnhNhan\ModHub\Modules\Forum\Query\DiscussionQuery" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Query/DiscussionQuery.php",
      "deriv" => "AnhNhan\ModHub\Storage\Query",
    ),
    "AnhNhan\ModHub\Modules\Forum\Storage\Discussion" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Storage/Discussion.php",
      "deriv" => "AnhNhan\ModHub\Storage\EntityDefinition",
      "impls" => array(
        "AnhNhan\ModHub\Storage\Transaction\TransactionAwareEntityInterface",
      ),
    ),
    "AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTag" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Storage/DiscussionTag.php",
      "deriv" => "AnhNhan\ModHub\Storage\EntityDefinition",
    ),
    "AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Storage/DiscussionTransaction.php",
      "deriv" => "AnhNhan\ModHub\Storage\Transaction\TransactionEntity",
    ),
    "AnhNhan\ModHub\Modules\Forum\Storage\Post" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Storage/Post.php",
      "deriv" => "AnhNhan\ModHub\Storage\EntityDefinition",
      "impls" => array(
        "AnhNhan\ModHub\Storage\Transaction\TransactionAwareEntityInterface",
      ),
    ),
    "AnhNhan\ModHub\Modules\Forum\Storage\PostTransaction" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Storage/PostTransaction.php",
      "deriv" => "AnhNhan\ModHub\Storage\Transaction\TransactionEntity",
    ),
    "AnhNhan\ModHub\Modules\Forum\Transaction\DiscussionTransactionEditor" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Transaction/DiscussionTransactionEditor.php",
      "deriv" => "AnhNhan\ModHub\Storage\Transaction\TransactionEditor",
    ),
    "AnhNhan\ModHub\Modules\Forum\Transaction\PostTransactionEditor" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Transaction/PostTransactionEditor.php",
      "deriv" => "AnhNhan\ModHub\Storage\Transaction\TransactionEditor",
    ),
    "AnhNhan\ModHub\Modules\Forum\Transform\DiscussionTransformer" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Transform/DiscussionTransformer.php",
      "deriv" => "League\Fractal\TransformerAbstract",
    ),
    "AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumListing" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Views/Objects/ForumListing.php",
      "deriv" => "AnhNhan\ModHub\Views\Objects\Listing",
    ),
    "AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumObject" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Views/Objects/ForumObject.php",
      "deriv" => "AnhNhan\ModHub\Views\Objects\Object",
    ),
    "AnhNhan\ModHub\Modules\Forum\Views\Objects\PaneledForumListing" => array(
      "file" => "AnhNhan/ModHub/Modules/Forum/Views/Objects/PaneledForumListing.php",
      "deriv" => "AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumListing",
    ),
    "AnhNhan\ModHub\Modules\Front\Controllers\StandardFrontController" => array(
      "file" => "AnhNhan/ModHub/Modules/Front/Controllers/StandardFrontController.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplicationController",
    ),
    "AnhNhan\ModHub\Modules\Front\FrontApplication" => array(
      "file" => "AnhNhan/ModHub/Modules/Front/FrontApplication.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplication",
    ),
    "AnhNhan\ModHub\Modules\Markup\Controllers\AbstractMarkupController" => array(
      "file" => "AnhNhan/ModHub/Modules/Markup/Controllers/AbstractMarkupController.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplicationController",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Modules\Markup\Controllers\MarkupProcessingController" => array(
      "file" => "AnhNhan/ModHub/Modules/Markup/Controllers/MarkupProcessingController.php",
      "deriv" => "AnhNhan\ModHub\Modules\Markup\Controllers\AbstractMarkupController",
    ),
    "AnhNhan\ModHub\Modules\Markup\Controllers\MarkupTestingController" => array(
      "file" => "AnhNhan/ModHub/Modules/Markup/Controllers/MarkupTestingController.php",
      "deriv" => "AnhNhan\ModHub\Modules\Markup\Controllers\AbstractMarkupController",
    ),
    "AnhNhan\ModHub\Modules\Markup\MarkupApplication" => array(
      "file" => "AnhNhan/ModHub/Modules/Markup/MarkupApplication.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplication",
    ),
    "AnhNhan\ModHub\Modules\Markup\MarkupEngine" => array(
      "file" => "AnhNhan/ModHub/Modules/Markup/MarkupEngine.php",
    ),
    "AnhNhan\ModHub\Modules\StaticResources\Builders\JsBuilder" => array(
      "file" => "AnhNhan/ModHub/Modules/StaticResources/Builders/JsBuilder.php",
    ),
    "AnhNhan\ModHub\Modules\StaticResources\Builders\TemplateBuilder" => array(
      "file" => "AnhNhan/ModHub/Modules/StaticResources/Builders/TemplateBuilder.php",
    ),
    "AnhNhan\ModHub\Modules\StaticResources\Console\CompileCommand" => array(
      "file" => "AnhNhan/ModHub/Modules/StaticResources/Console/CompileCommand.php",
      "deriv" => "AnhNhan\ModHub\Console\ConsoleCommand",
    ),
    "AnhNhan\ModHub\Modules\StaticResources\Controllers\AbstractStaticResourceController" => array(
      "file" => "AnhNhan/ModHub/Modules/StaticResources/Controllers/AbstractStaticResourceController.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplicationController",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Modules\StaticResources\Controllers\StaticResourceController" => array(
      "file" => "AnhNhan/ModHub/Modules/StaticResources/Controllers/StaticResourceController.php",
      "deriv" => "AnhNhan\ModHub\Modules\StaticResources\Controllers\AbstractStaticResourceController",
    ),
    "AnhNhan\ModHub\Modules\StaticResources\ResMgr" => array(
      "file" => "AnhNhan/ModHub/Modules/StaticResources/ResMgr.php",
    ),
    "AnhNhan\ModHub\Modules\StaticResources\StaticResourcesApplication" => array(
      "file" => "AnhNhan/ModHub/Modules/StaticResources/StaticResourcesApplication.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplication",
    ),
    "AnhNhan\ModHub\Modules\StaticResources\Console\AbstractSymbolsCommand" => array(
      "file" => "AnhNhan/ModHub/Modules/Symbols/Console/AbstractSymbolsCommand.php",
      "deriv" => "AnhNhan\ModHub\Console\ConsoleCommand",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Modules\StaticResources\Console\SymbolGenerationCommand" => array(
      "file" => "AnhNhan/ModHub/Modules/Symbols/Console/SymbolGenerationCommand.php",
      "deriv" => "AnhNhan\ModHub\Modules\StaticResources\Console\AbstractSymbolsCommand",
    ),
    "AnhNhan\ModHub\Modules\Symbols\Generator\NodeVisitor\SymbolEmitter" => array(
      "file" => "AnhNhan/ModHub/Modules/Symbols/Generator/NodeVisitor/SymbolEmitter.php",
      "deriv" => "PHPParser_NodeVisitorAbstract",
    ),
    "AnhNhan\ModHub\Modules\Symbols\Generator\SymbolGenerator" => array(
      "file" => "AnhNhan/ModHub/Modules/Symbols/Generator/SymbolGenerator.php",
    ),
    "AnhNhan\ModHub\Modules\Symbols\Generator\SymbolTree" => array(
      "file" => "AnhNhan/ModHub/Modules/Symbols/Generator/SymbolTree.php",
    ),
    "AnhNhan\ModHub\Modules\Symbols\SymbolLoader" => array(
      "file" => "AnhNhan/ModHub/Modules/Symbols/SymbolLoader.php",
    ),
    "AnhNhan\ModHub\Modules\Tag\Controllers\AbstractTagController" => array(
      "file" => "AnhNhan/ModHub/Modules/Tag/Controllers/AbstractTagController.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplicationController",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Modules\Tag\Controllers\TagCreationController" => array(
      "file" => "AnhNhan/ModHub/Modules/Tag/Controllers/TagCreationController.php",
      "deriv" => "AnhNhan\ModHub\Modules\Tag\Controllers\AbstractTagController",
    ),
    "AnhNhan\ModHub\Modules\Tag\Controllers\TagDisplayController" => array(
      "file" => "AnhNhan/ModHub/Modules/Tag/Controllers/TagDisplayController.php",
      "deriv" => "AnhNhan\ModHub\Modules\Tag\Controllers\AbstractTagController",
    ),
    "AnhNhan\ModHub\Modules\Tag\Controllers\TagListingController" => array(
      "file" => "AnhNhan/ModHub/Modules/Tag/Controllers/TagListingController.php",
      "deriv" => "AnhNhan\ModHub\Modules\Tag\Controllers\AbstractTagController",
    ),
    "AnhNhan\ModHub\Modules\Tag\Storage\Tag" => array(
      "file" => "AnhNhan/ModHub/Modules/Tag/Storage/Tag.php",
      "deriv" => "AnhNhan\ModHub\Storage\EntityDefinition",
      "impls" => array(
        "AnhNhan\ModHub\Storage\Transaction\TransactionAwareEntityInterface",
      ),
    ),
    "AnhNhan\ModHub\Modules\Tag\Storage\TagTransaction" => array(
      "file" => "AnhNhan/ModHub/Modules/Tag/Storage/TagTransaction.php",
      "deriv" => "AnhNhan\ModHub\Storage\Transaction\TransactionEntity",
    ),
    "AnhNhan\ModHub\Modules\Tag\TagApplication" => array(
      "file" => "AnhNhan/ModHub/Modules/Tag/TagApplication.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplication",
    ),
    "AnhNhan\ModHub\Modules\Tag\TagQuery" => array(
      "file" => "AnhNhan/ModHub/Modules/Tag/TagQuery.php",
      "deriv" => "AnhNhan\ModHub\Storage\Query",
    ),
    "AnhNhan\ModHub\Modules\Tag\Transaction\TagTransactionEditor" => array(
      "file" => "AnhNhan/ModHub/Modules/Tag/Transaction/TagTransactionEditor.php",
      "deriv" => "AnhNhan\ModHub\Storage\Transaction\TransactionEditor",
    ),
    "AnhNhan\ModHub\Modules\Tag\Views\TagView" => array(
      "file" => "AnhNhan/ModHub/Modules/Tag/Views/TagView.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
    ),
    "AnhNhan\ModHub\Modules\User\Controllers\AbstractUserController" => array(
      "file" => "AnhNhan/ModHub/Modules/User/Controllers/AbstractUserController.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplicationController",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Modules\User\Controllers\RoleEditController" => array(
      "file" => "AnhNhan/ModHub/Modules/User/Controllers/RoleEditController.php",
      "deriv" => "AnhNhan\ModHub\Modules\User\Controllers\AbstractUserController",
    ),
    "AnhNhan\ModHub\Modules\User\Controllers\RoleListingController" => array(
      "file" => "AnhNhan/ModHub/Modules/User/Controllers/RoleListingController.php",
      "deriv" => "AnhNhan\ModHub\Modules\User\Controllers\AbstractUserController",
    ),
    "AnhNhan\ModHub\Modules\User\DependencyInjection\SecurityExtension" => array(
      "file" => "AnhNhan/ModHub/Modules/User/DependencyInjection/SecurityExtension.php",
      "impls" => array(
        "Symfony\Component\DependencyInjection\Extension\ExtensionInterface",
      ),
    ),
    "AnhNhan\ModHub\Modules\User\DependencyInjection\UserExtension" => array(
      "file" => "AnhNhan/ModHub/Modules/User/DependencyInjection/UserExtension.php",
      "impls" => array(
        "Symfony\Component\DependencyInjection\Extension\ExtensionInterface",
      ),
    ),
    "AnhNhan\ModHub\Modules\User\Providers\DefaultUserProvider" => array(
      "file" => "AnhNhan/ModHub/Modules/User/Providers/DefaultUserProvider.php",
      "impls" => array(
        "Symfony\Component\Security\Core\User\UserProviderInterface",
      ),
    ),
    "AnhNhan\ModHub\Modules\User\Providers\UserAuthenticationProvider" => array(
      "file" => "AnhNhan/ModHub/Modules/User/Providers/UserAuthenticationProvider.php",
      "deriv" => "Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider",
    ),
    "AnhNhan\ModHub\Modules\User\Query\RoleQuery" => array(
      "file" => "AnhNhan/ModHub/Modules/User/Query/RoleQuery.php",
      "deriv" => "AnhNhan\ModHub\Storage\Query",
    ),
    "AnhNhan\ModHub\Modules\User\Storage\Role" => array(
      "file" => "AnhNhan/ModHub/Modules/User/Storage/Role.php",
      "deriv" => "AnhNhan\ModHub\Storage\EntityDefinition",
      "impls" => array(
        "Symfony\Component\Security\Core\Role\RoleInterface",
        "AnhNhan\ModHub\Storage\Transaction\TransactionAwareEntityInterface",
      ),
    ),
    "AnhNhan\ModHub\Modules\User\Storage\RoleTransaction" => array(
      "file" => "AnhNhan/ModHub/Modules/User/Storage/RoleTransaction.php",
      "deriv" => "AnhNhan\ModHub\Storage\Transaction\TransactionEntity",
    ),
    "AnhNhan\ModHub\Modules\User\Storage\User" => array(
      "file" => "AnhNhan/ModHub/Modules/User/Storage/User.php",
      "deriv" => "AnhNhan\ModHub\Storage\EntityDefinition",
      "impls" => array(
        "Symfony\Component\Security\Core\User\AdvancedUserInterface",
      ),
    ),
    "AnhNhan\ModHub\Modules\User\Transaction\RoleTransactionEditor" => array(
      "file" => "AnhNhan/ModHub/Modules/User/Transaction/RoleTransactionEditor.php",
      "deriv" => "AnhNhan\ModHub\Storage\Transaction\TransactionEditor",
    ),
    "AnhNhan\ModHub\Modules\User\UserApplication" => array(
      "file" => "AnhNhan/ModHub/Modules/User/UserApplication.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\BaseApplication",
    ),
    "AnhNhan\ModHub\Modules\User\Views\UserPlateView" => array(
      "file" => "AnhNhan/ModHub/Modules/User/Views/UserPlateView.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
    ),
    "AnhNhan\ModHub\Storage\Doctrine\UIDGenerator" => array(
      "file" => "AnhNhan/ModHub/Storage/Doctrine/UIDGenerator.php",
      "deriv" => "Doctrine\ORM\Id\AbstractIdGenerator",
    ),
    "AnhNhan\ModHub\Storage\EntityDefinition" => array(
      "file" => "AnhNhan/ModHub/Storage/EntityDefinition.php",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Storage\Query" => array(
      "file" => "AnhNhan/ModHub/Storage/Query.php",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Storage\Transaction\TransactionEditor" => array(
      "file" => "AnhNhan/ModHub/Storage/Transaction/TransactionEditor.php",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Storage\Transaction\TransactionEntity" => array(
      "file" => "AnhNhan/ModHub/Storage/Transaction/TransactionEntity.php",
      "deriv" => "AnhNhan\ModHub\Storage\EntityDefinition",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Storage\Types\UID" => array(
      "file" => "AnhNhan/ModHub/Storage/Types/UID.php",
    ),
    "AnhNhan\ModHub\Views\AbstractView" => array(
      "file" => "AnhNhan/ModHub/Views/AbstractView.php",
      "impls" => array(
        "YamwLibs\Libs\View\ViewInterface",
        "YamwLibs\Libs\Html\Interfaces\YamwMarkupInterface",
      ),
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Views\Form\Controls\AbstractFormControl" => array(
      "file" => "AnhNhan/ModHub/Views/Form/Controls/AbstractFormControl.php",
      "deriv" => "YamwLibs\Libs\Html\Markup\HtmlTag",
      "impls" => array(
        "YamwLibs\Libs\View\ViewInterface",
      ),
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Views\Form\Controls\HiddenControl" => array(
      "file" => "AnhNhan/ModHub/Views/Form/Controls/HiddenControl.php",
      "deriv" => "AnhNhan\ModHub\Views\Form\Controls\AbstractFormControl",
    ),
    "AnhNhan\ModHub\Views\Form\Controls\SubmitControl" => array(
      "file" => "AnhNhan/ModHub/Views/Form/Controls/SubmitControl.php",
      "impls" => array(
        "YamwLibs\Libs\View\ViewInterface",
      ),
    ),
    "AnhNhan\ModHub\Views\Form\Controls\TextAreaControl" => array(
      "file" => "AnhNhan/ModHub/Views/Form/Controls/TextAreaControl.php",
      "deriv" => "AnhNhan\ModHub\Views\Form\Controls\AbstractFormControl",
    ),
    "AnhNhan\ModHub\Views\Form\Controls\TextControl" => array(
      "file" => "AnhNhan/ModHub/Views/Form/Controls/TextControl.php",
      "deriv" => "AnhNhan\ModHub\Views\Form\Controls\AbstractFormControl",
    ),
    "AnhNhan\ModHub\Views\Form\FormView" => array(
      "file" => "AnhNhan/ModHub/Views/Form/FormView.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
    ),
    "AnhNhan\ModHub\Views\Grid\Column" => array(
      "file" => "AnhNhan/ModHub/Views/Grid/Column.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
    ),
    "AnhNhan\ModHub\Views\Grid\Grid" => array(
      "file" => "AnhNhan/ModHub/Views/Grid/Grid.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
    ),
    "AnhNhan\ModHub\Views\Grid\Row" => array(
      "file" => "AnhNhan/ModHub/Views/Grid/Row.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
    ),
    "AnhNhan\ModHub\Views\Objects\AbstractObject" => array(
      "file" => "AnhNhan/ModHub/Views/Objects/AbstractObject.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Views\Objects\Listing" => array(
      "file" => "AnhNhan/ModHub/Views/Objects/Listing.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
    ),
    "AnhNhan\ModHub\Views\Objects\Object" => array(
      "file" => "AnhNhan/ModHub/Views/Objects/Object.php",
      "deriv" => "AnhNhan\ModHub\Views\Objects\AbstractObject",
    ),
    "AnhNhan\ModHub\Views\Page\BarePageView" => array(
      "file" => "AnhNhan/ModHub/Views/Page/BarePageView.php",
      "deriv" => "AnhNhan\ModHub\Views\Page\PageView",
    ),
    "AnhNhan\ModHub\Views\Page\DefaultTemplateView" => array(
      "file" => "AnhNhan/ModHub/Views/Page/DefaultTemplateView.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
    ),
    "AnhNhan\ModHub\Views\Page\FooterView" => array(
      "file" => "AnhNhan/ModHub/Views/Page/FooterView.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
      "impls" => array(
        "YamwLibs\Libs\View\ViewInterface",
      ),
    ),
    "AnhNhan\ModHub\Views\Page\HeaderView" => array(
      "file" => "AnhNhan/ModHub/Views/Page/HeaderView.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
    ),
    "AnhNhan\ModHub\Views\Page\HtmlDocumentView" => array(
      "file" => "AnhNhan/ModHub/Views/Page/HtmlDocumentView.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
    ),
    "AnhNhan\ModHub\Views\Page\PageView" => array(
      "file" => "AnhNhan/ModHub/Views/Page/PageView.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Views\Page\SidebarView" => array(
      "file" => "AnhNhan/ModHub/Views/Page/SidebarView.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
    ),
    "AnhNhan\ModHub\Views\Panel\Panel" => array(
      "file" => "AnhNhan/ModHub/Views/Panel/Panel.php",
      "deriv" => "AnhNhan\ModHub\Views\AbstractView",
    ),
    "AnhNhan\ModHub\Web\AppRouting" => array(
      "file" => "AnhNhan/ModHub/Web/AppRouting.php",
      "impls" => array(
        "Symfony\Component\Routing\Matcher\RequestMatcherInterface",
        "Symfony\Component\Routing\Generator\UrlGeneratorInterface",
      ),
    ),
    "AnhNhan\ModHub\Web\Application\AbstractPayload" => array(
      "file" => "AnhNhan/ModHub/Web/Application/AbstractPayload.php",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Web\Application\BaseApplication" => array(
      "file" => "AnhNhan/ModHub/Web/Application/BaseApplication.php",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Web\Application\BaseApplicationController" => array(
      "file" => "AnhNhan/ModHub/Web/Application/BaseApplicationController.php",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Web\Application\HtmlPayload" => array(
      "file" => "AnhNhan/ModHub/Web/Application/HtmlPayload.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\HttpPayload",
    ),
    "AnhNhan\ModHub\Web\Application\HttpPayload" => array(
      "file" => "AnhNhan/ModHub/Web/Application/HttpPayload.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\AbstractPayload",
      "abstr" => "1",
    ),
    "AnhNhan\ModHub\Web\Application\JsonPayload" => array(
      "file" => "AnhNhan/ModHub/Web/Application/JsonPayload.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\HttpPayload",
    ),
    "AnhNhan\ModHub\Web\Application\RawHttpPayload" => array(
      "file" => "AnhNhan/ModHub/Web/Application/RawHttpPayload.php",
      "deriv" => "AnhNhan\ModHub\Web\Application\HttpPayload",
    ),
    "AnhNhan\ModHub\Web\Core" => array(
      "file" => "AnhNhan/ModHub/Web/Core.php",
    ),
    "AnhNhan\ModHub\Web\HttpKernel" => array(
      "file" => "AnhNhan/ModHub/Web/HttpKernel.php",
      "impls" => array(
        "Symfony\Component\HttpKernel\HttpKernelInterface",
        "Symfony\Component\DependencyInjection\ContainerAwareInterface",
      ),
    ),
  ),
  "functions" => array(
    "AnhNhan\ModHub\get_root" => "AnhNhan/ModHub/functions.php",
    "AnhNhan\ModHub\get_root_super" => "AnhNhan/ModHub/functions.php",
    "AnhNhan\ModHub\path" => "AnhNhan/ModHub/functions.php",
    "AnhNhan\ModHub\is_cli" => "AnhNhan/ModHub/functions.php",
    "AnhNhan\ModHub\is_windows" => "AnhNhan/ModHub/functions.php",
    "AnhNhan\ModHub\println" => "AnhNhan/ModHub/functions.php",
    "AnhNhan\ModHub\sdx" => "AnhNhan/ModHub/functions.php",
    "AnhNhan\ModHub\pdx" => "AnhNhan/ModHub/functions.php",
    "AnhNhan\ModHub\safeHtml" => "AnhNhan/ModHub/functions_html.php",
    "AnhNhan\ModHub\ht" => "AnhNhan/ModHub/functions_html.php",
    "AnhNhan\ModHub\icon_text" => "AnhNhan/ModHub/functions_html.php",
  ),
  "xmap" => array(
    "Symfony\Component\Console\Command\Command" => array(
      "AnhNhan\ModHub\Console\ConsoleCommand",
      "AnhNhan\ModHub\Modules\Database\Command\AbstractDbCommand",
      "AnhNhan\ModHub\Modules\StaticResources\Console\CompileCommand",
      "AnhNhan\ModHub\Modules\StaticResources\Console\AbstractSymbolsCommand",
    ),
    "AnhNhan\ModHub\Console\ConsoleCommand" => array(
      "AnhNhan\ModHub\Modules\Database\Command\AbstractDbCommand",
      "AnhNhan\ModHub\Modules\StaticResources\Console\CompileCommand",
      "AnhNhan\ModHub\Modules\StaticResources\Console\AbstractSymbolsCommand",
      "AnhNhan\ModHub\Modules\Database\Command\DoctrineManager",
      "AnhNhan\ModHub\Modules\StaticResources\Console\SymbolGenerationCommand",
    ),
    "AnhNhan\ModHub\Modules\Database\Command\AbstractDbCommand" => array(
      "AnhNhan\ModHub\Modules\Database\Command\DoctrineManager",
    ),
    "AnhNhan\ModHub\Web\Application\BaseApplicationController" => array(
      "AnhNhan\ModHub\Modules\Examples\Controllers\StandardExamplesController",
      "AnhNhan\ModHub\Modules\Forum\Controllers\AbstractForumController",
      "AnhNhan\ModHub\Modules\Front\Controllers\StandardFrontController",
      "AnhNhan\ModHub\Modules\Markup\Controllers\AbstractMarkupController",
      "AnhNhan\ModHub\Modules\StaticResources\Controllers\AbstractStaticResourceController",
      "AnhNhan\ModHub\Modules\Tag\Controllers\AbstractTagController",
      "AnhNhan\ModHub\Modules\User\Controllers\AbstractUserController",
      "AnhNhan\ModHub\Modules\Forum\Controllers\DiscussionDisplayController",
      "AnhNhan\ModHub\Modules\Forum\Controllers\DiscussionEditController",
      "AnhNhan\ModHub\Modules\Forum\Controllers\DiscussionListingController",
      "AnhNhan\ModHub\Modules\Forum\Controllers\PostEditController",
      "AnhNhan\ModHub\Modules\Markup\Controllers\MarkupProcessingController",
      "AnhNhan\ModHub\Modules\Markup\Controllers\MarkupTestingController",
      "AnhNhan\ModHub\Modules\StaticResources\Controllers\StaticResourceController",
      "AnhNhan\ModHub\Modules\Tag\Controllers\TagCreationController",
      "AnhNhan\ModHub\Modules\Tag\Controllers\TagDisplayController",
      "AnhNhan\ModHub\Modules\Tag\Controllers\TagListingController",
      "AnhNhan\ModHub\Modules\User\Controllers\RoleEditController",
      "AnhNhan\ModHub\Modules\User\Controllers\RoleListingController",
    ),
    "AnhNhan\ModHub\Modules\Examples\Examples\AbstractExample" => array(
      "AnhNhan\ModHub\Modules\Examples\Examples\BootstrapExample",
      "AnhNhan\ModHub\Modules\Examples\Examples\FormExample",
      "AnhNhan\ModHub\Modules\Examples\Examples\ForumListingExample",
      "AnhNhan\ModHub\Modules\Examples\Examples\PanelExample",
    ),
    "AnhNhan\ModHub\Web\Application\BaseApplication" => array(
      "AnhNhan\ModHub\Modules\Examples\ExamplesApplication",
      "AnhNhan\ModHub\Modules\Forum\ForumApplication",
      "AnhNhan\ModHub\Modules\Front\FrontApplication",
      "AnhNhan\ModHub\Modules\Markup\MarkupApplication",
      "AnhNhan\ModHub\Modules\StaticResources\StaticResourcesApplication",
      "AnhNhan\ModHub\Modules\Tag\TagApplication",
      "AnhNhan\ModHub\Modules\User\UserApplication",
    ),
    "AnhNhan\ModHub\Modules\Forum\Controllers\AbstractForumController" => array(
      "AnhNhan\ModHub\Modules\Forum\Controllers\DiscussionDisplayController",
      "AnhNhan\ModHub\Modules\Forum\Controllers\DiscussionEditController",
      "AnhNhan\ModHub\Modules\Forum\Controllers\DiscussionListingController",
      "AnhNhan\ModHub\Modules\Forum\Controllers\PostEditController",
    ),
    "AnhNhan\ModHub\Storage\Query" => array(
      "AnhNhan\ModHub\Modules\Forum\Query\DiscussionQuery",
      "AnhNhan\ModHub\Modules\Tag\TagQuery",
      "AnhNhan\ModHub\Modules\User\Query\RoleQuery",
    ),
    "AnhNhan\ModHub\Storage\EntityDefinition" => array(
      "AnhNhan\ModHub\Modules\Forum\Storage\Discussion",
      "AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTag",
      "AnhNhan\ModHub\Modules\Forum\Storage\Post",
      "AnhNhan\ModHub\Modules\Tag\Storage\Tag",
      "AnhNhan\ModHub\Modules\User\Storage\Role",
      "AnhNhan\ModHub\Modules\User\Storage\User",
      "AnhNhan\ModHub\Storage\Transaction\TransactionEntity",
      "AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction",
      "AnhNhan\ModHub\Modules\Forum\Storage\PostTransaction",
      "AnhNhan\ModHub\Modules\Tag\Storage\TagTransaction",
      "AnhNhan\ModHub\Modules\User\Storage\RoleTransaction",
    ),
    "AnhNhan\ModHub\Storage\Transaction\TransactionEntity" => array(
      "AnhNhan\ModHub\Modules\Forum\Storage\DiscussionTransaction",
      "AnhNhan\ModHub\Modules\Forum\Storage\PostTransaction",
      "AnhNhan\ModHub\Modules\Tag\Storage\TagTransaction",
      "AnhNhan\ModHub\Modules\User\Storage\RoleTransaction",
    ),
    "AnhNhan\ModHub\Storage\Transaction\TransactionEditor" => array(
      "AnhNhan\ModHub\Modules\Forum\Transaction\DiscussionTransactionEditor",
      "AnhNhan\ModHub\Modules\Forum\Transaction\PostTransactionEditor",
      "AnhNhan\ModHub\Modules\Tag\Transaction\TagTransactionEditor",
      "AnhNhan\ModHub\Modules\User\Transaction\RoleTransactionEditor",
    ),
    "League\Fractal\TransformerAbstract" => array(
      "AnhNhan\ModHub\Modules\Forum\Transform\DiscussionTransformer",
    ),
    "AnhNhan\ModHub\Views\Objects\Listing" => array(
      "AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumListing",
      "AnhNhan\ModHub\Modules\Forum\Views\Objects\PaneledForumListing",
    ),
    "AnhNhan\ModHub\Views\Objects\Object" => array(
      "AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumObject",
    ),
    "AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumListing" => array(
      "AnhNhan\ModHub\Modules\Forum\Views\Objects\PaneledForumListing",
    ),
    "AnhNhan\ModHub\Modules\Markup\Controllers\AbstractMarkupController" => array(
      "AnhNhan\ModHub\Modules\Markup\Controllers\MarkupProcessingController",
      "AnhNhan\ModHub\Modules\Markup\Controllers\MarkupTestingController",
    ),
    "AnhNhan\ModHub\Modules\StaticResources\Controllers\AbstractStaticResourceController" => array(
      "AnhNhan\ModHub\Modules\StaticResources\Controllers\StaticResourceController",
    ),
    "AnhNhan\ModHub\Modules\StaticResources\Console\AbstractSymbolsCommand" => array(
      "AnhNhan\ModHub\Modules\StaticResources\Console\SymbolGenerationCommand",
    ),
    "PHPParser_NodeVisitorAbstract" => array(
      "AnhNhan\ModHub\Modules\Symbols\Generator\NodeVisitor\SymbolEmitter",
    ),
    "AnhNhan\ModHub\Modules\Tag\Controllers\AbstractTagController" => array(
      "AnhNhan\ModHub\Modules\Tag\Controllers\TagCreationController",
      "AnhNhan\ModHub\Modules\Tag\Controllers\TagDisplayController",
      "AnhNhan\ModHub\Modules\Tag\Controllers\TagListingController",
    ),
    "AnhNhan\ModHub\Views\AbstractView" => array(
      "AnhNhan\ModHub\Modules\Tag\Views\TagView",
      "AnhNhan\ModHub\Modules\User\Views\UserPlateView",
      "AnhNhan\ModHub\Views\Form\FormView",
      "AnhNhan\ModHub\Views\Grid\Column",
      "AnhNhan\ModHub\Views\Grid\Grid",
      "AnhNhan\ModHub\Views\Grid\Row",
      "AnhNhan\ModHub\Views\Objects\AbstractObject",
      "AnhNhan\ModHub\Views\Objects\Listing",
      "AnhNhan\ModHub\Views\Page\DefaultTemplateView",
      "AnhNhan\ModHub\Views\Page\FooterView",
      "AnhNhan\ModHub\Views\Page\HeaderView",
      "AnhNhan\ModHub\Views\Page\HtmlDocumentView",
      "AnhNhan\ModHub\Views\Page\PageView",
      "AnhNhan\ModHub\Views\Page\SidebarView",
      "AnhNhan\ModHub\Views\Panel\Panel",
      "AnhNhan\ModHub\Views\Objects\Object",
      "AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumListing",
      "AnhNhan\ModHub\Modules\Forum\Views\Objects\PaneledForumListing",
      "AnhNhan\ModHub\Views\Page\BarePageView",
    ),
    "AnhNhan\ModHub\Modules\User\Controllers\AbstractUserController" => array(
      "AnhNhan\ModHub\Modules\User\Controllers\RoleEditController",
      "AnhNhan\ModHub\Modules\User\Controllers\RoleListingController",
    ),
    "Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider" => array(
      "AnhNhan\ModHub\Modules\User\Providers\UserAuthenticationProvider",
    ),
    "Doctrine\ORM\Id\AbstractIdGenerator" => array(
      "AnhNhan\ModHub\Storage\Doctrine\UIDGenerator",
    ),
    "YamwLibs\Libs\Html\Markup\HtmlTag" => array(
      "AnhNhan\ModHub\Views\Form\Controls\AbstractFormControl",
      "AnhNhan\ModHub\Views\Form\Controls\HiddenControl",
      "AnhNhan\ModHub\Views\Form\Controls\TextAreaControl",
      "AnhNhan\ModHub\Views\Form\Controls\TextControl",
    ),
    "AnhNhan\ModHub\Views\Form\Controls\AbstractFormControl" => array(
      "AnhNhan\ModHub\Views\Form\Controls\HiddenControl",
      "AnhNhan\ModHub\Views\Form\Controls\TextAreaControl",
      "AnhNhan\ModHub\Views\Form\Controls\TextControl",
    ),
    "AnhNhan\ModHub\Views\Objects\AbstractObject" => array(
      "AnhNhan\ModHub\Views\Objects\Object",
      "AnhNhan\ModHub\Modules\Forum\Views\Objects\ForumObject",
    ),
    "AnhNhan\ModHub\Views\Page\PageView" => array(
      "AnhNhan\ModHub\Views\Page\BarePageView",
    ),
    "AnhNhan\ModHub\Web\Application\HttpPayload" => array(
      "AnhNhan\ModHub\Web\Application\HtmlPayload",
      "AnhNhan\ModHub\Web\Application\JsonPayload",
      "AnhNhan\ModHub\Web\Application\RawHttpPayload",
    ),
    "AnhNhan\ModHub\Web\Application\AbstractPayload" => array(
      "AnhNhan\ModHub\Web\Application\HttpPayload",
      "AnhNhan\ModHub\Web\Application\HtmlPayload",
      "AnhNhan\ModHub\Web\Application\JsonPayload",
      "AnhNhan\ModHub\Web\Application\RawHttpPayload",
    ),
  ),
  "implementations" => array(
    "AnhNhan\ModHub\Storage\Transaction\TransactionAwareEntityInterface" => array(
      "AnhNhan\ModHub\Modules\Forum\Storage\Discussion",
      "AnhNhan\ModHub\Modules\Forum\Storage\Post",
      "AnhNhan\ModHub\Modules\Tag\Storage\Tag",
      "AnhNhan\ModHub\Modules\User\Storage\Role",
    ),
    "Symfony\Component\DependencyInjection\Extension\ExtensionInterface" => array(
      "AnhNhan\ModHub\Modules\User\DependencyInjection\SecurityExtension",
      "AnhNhan\ModHub\Modules\User\DependencyInjection\UserExtension",
    ),
    "Symfony\Component\Security\Core\User\UserProviderInterface" => array(
      "AnhNhan\ModHub\Modules\User\Providers\DefaultUserProvider",
    ),
    "Symfony\Component\Security\Core\Role\RoleInterface" => array(
      "AnhNhan\ModHub\Modules\User\Storage\Role",
    ),
    "Symfony\Component\Security\Core\User\AdvancedUserInterface" => array(
      "AnhNhan\ModHub\Modules\User\Storage\User",
    ),
    "YamwLibs\Libs\View\ViewInterface" => array(
      "AnhNhan\ModHub\Views\AbstractView",
      "AnhNhan\ModHub\Views\Form\Controls\AbstractFormControl",
      "AnhNhan\ModHub\Views\Form\Controls\SubmitControl",
      "AnhNhan\ModHub\Views\Page\FooterView",
    ),
    "YamwLibs\Libs\Html\Interfaces\YamwMarkupInterface" => array(
      "AnhNhan\ModHub\Views\AbstractView",
    ),
    "Symfony\Component\Routing\Matcher\RequestMatcherInterface" => array(
      "AnhNhan\ModHub\Web\AppRouting",
    ),
    "Symfony\Component\Routing\Generator\UrlGeneratorInterface" => array(
      "AnhNhan\ModHub\Web\AppRouting",
    ),
    "Symfony\Component\HttpKernel\HttpKernelInterface" => array(
      "AnhNhan\ModHub\Web\HttpKernel",
    ),
    "Symfony\Component\DependencyInjection\ContainerAwareInterface" => array(
      "AnhNhan\ModHub\Web\HttpKernel",
    ),
  ),
);
