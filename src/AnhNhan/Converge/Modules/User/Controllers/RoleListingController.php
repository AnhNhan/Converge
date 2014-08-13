<?php
namespace AnhNhan\Converge\Modules\User\Controllers;

use AnhNhan\Converge;
use AnhNhan\Converge\Modules\User\Storage\Role;
use AnhNhan\Converge\Modules\User\Query\RoleQuery;
use AnhNhan\Converge\Views\Grid\Grid;
use AnhNhan\Converge\Views\Panel\Panel;

use AnhNhan\Converge\Web\Application\HtmlPayload;
use AnhNhan\Converge\Web\Application\JsonPayload;
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

        $container->push(Converge\ht("a", "Create new role", array(
            "href"  => "/role/create",
            "class" => "btn btn-primary pull-right",
        )));

        $container->push(Converge\ht("h1", "Roles / Permissions"));

        $grid = new Grid;
        $row = $grid->row();

        foreach ($roles as $role) {
            $headerGrid = new Grid;
            $headerRow  = $headerGrid->row();
            $headerRow
                ->column(10)
                ->push(Converge\ht("h3", $role->label)
                    ->append(Converge\ht("small", " " . $role->name))
                )
                ->parentRow()
                ->column(2)
                ->push(
                    Converge\ht("a", Converge\icon_ion("edit", "edit"))
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
        $payload->setPayloadContents(Converge\ht("div", $container)->addClass("role-listing"));

        $this->app->getService("resource_manager")
            ->requireCss("application-role-listing")
        ;

        return $payload;
    }
}
