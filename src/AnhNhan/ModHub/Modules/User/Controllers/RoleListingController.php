<?php
namespace AnhNhan\ModHub\Modules\User\Controllers;

use AnhNhan\ModHub;
use AnhNhan\ModHub\Modules\User\Storage\Role;
use AnhNhan\ModHub\Modules\User\Query\RoleQuery;
use AnhNhan\ModHub\Views\Grid\Grid;
use AnhNhan\ModHub\Views\Panel\Panel;

use AnhNhan\ModHub\Web\Application\HtmlPayload;
use AnhNhan\ModHub\Web\Application\JsonPayload;
use YamwLibs\Libs\Html\Markup\MarkupContainer;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
final class RoleListingController extends AbstractUserController
{
    public function handle()
    {
        $request = $this->request;

        $container = new MarkupContainer;

        // Currently not paged
        $roles = id(new RoleQuery($this->app))
            ->retrieveRoles()
        ;

        $container->push(ModHub\ht("a", "Create new role", array(
            "href"  => "/role/create",
            "class" => "btn btn-primary pull-right",
        )));

        $container->push(ModHub\ht("h1", "Roles"));

        $grid = new Grid;
        $row = $grid->row();

        foreach ($roles as $role) {
            $headerGrid = new Grid;
            $headerRow  = $headerGrid->row();
            $headerRow
                ->column(10)
                ->push(ModHub\ht("h3", $role->label)
                    ->appendContent(ModHub\ht("small", " " . $role->name))
                )
                ->parentRow()
                ->column(2)
                ->push(
                    ModHub\ht("a", ModHub\icon_text("edit ", "edit", false))
                        ->addClass("btn btn-default btn-small pull-right")
                        ->addOption("href", "role/{$role->cleanId}/edit")
                )
            ;

            $row->column(4)->push(
                id(new Panel)
                    ->setId($role->uid)
                    ->setHeader($headerGrid)
                    ->append($role->description)
            );
        }
        $container->push($grid);

        $payload = new HtmlPayload;
        $payload->setTitle("Roles");
        $payload->setPayloadContents($container);

        return $payload;
    }
}
