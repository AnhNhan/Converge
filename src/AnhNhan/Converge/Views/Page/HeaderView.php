<?php
namespace AnhNhan\Converge\Views\Page;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\People\Storage\User;
use AnhNhan\Converge\Modules\People\Views\UserPlateView;
use AnhNhan\Converge\Views\AbstractView;

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class HeaderView extends AbstractView
{
    private $user_details = [];

    public function setUserDetails($user_details)
    {
        $this->user_details = $user_details ?: [];
        return $this;
    }

    public function render()
    {
        $header_content = div('header-content');

        if ($this->user_details)
        {
            $header_content
                ->append(
                    div('nav-action')
                        ->append(a(cv\icon_ion('', 'paper-airplane'), 'newsroom/')
                            ->addClass('btn btn-default')
                            ->addOption('data-toggle', 'tooltip-bottom')
                            ->addOption('title', 'newsroom')
                        )
                        ->append(a(cv\icon_ion('', 'ios7-partlysunny'), 'activity/')
                            ->addClass('btn btn-default')
                            ->addOption('data-toggle', 'tooltip-bottom')
                            ->addOption('title', 'activity stream')
                        )
                        ->append(
                            div('btn-group')
                                ->append(a(cv\icon_ion('', 'checkmark'), 'task/')
                                    ->addClass('btn btn-default')
                                    ->addOption('data-toggle', 'tooltip-bottom')
                                    ->addOption('title', 'task listing')
                                )
                                ->append(a(cv\icon_ion('', 'plus'), 'task/create')
                                    ->addClass('btn btn-default')
                                    ->addOption('data-toggle', 'tooltip-bottom')
                                    ->addOption('title', 'create task')
                                )
                        )
                        ->append(
                            div('btn-group')
                                ->append(a(cv\icon_ion('', 'navicon-round'), 'disq/')
                                    ->addClass('btn btn-default')
                                    ->addOption('data-toggle', 'tooltip-bottom')
                                    ->addOption('title', 'discussion listing')
                                )
                                ->append(a(cv\icon_ion('', 'compose'), 'disq/create')
                                    ->addClass('btn btn-default')
                                    ->addOption('data-toggle', 'tooltip-bottom')
                                    ->addOption('title', 'create discussion')
                                )
                        )
                        ->append(a(cv\icon_ion('', 'log-out'), 'logout')
                            ->addClass('btn btn-default')
                            ->addOption('data-toggle', 'tooltip-bottom')
                            ->addOption('title', 'log out')
                        )
                )
                ->append(
                    div('nav-stats dev-only hidden-phone')
                        ->append(
                            span('stat-entry', cv\icon_ion('{{time}}', 'ios7-timer-outline'))
                        )
                        ->append(
                            span('stat-entry', cv\icon_ion('{{queries}} queries', 'ios7-cloud-outline'))
                        )
                        ->append(
                            span('stat-entry', cv\icon_ion('{{memory}}MB', 'ios7-pie-outline'))
                        )
                )
            ;
        }
        else
        {
            $header_content
                ->append(
                    div('nav-join')
                        ->append(a('Join Us!', 'join')->addClass('btn btn-info'))
                        ->append(a('Login', 'login')->addClass('btn btn-primary'))
                )
            ;
        }

        $header_link = a(null, '/', true)
            ->append(h2('Converge'))
            ->append(h4('Make me one with everything'))
        ;
        $header_content->append($header_link);

        return $header_content;
    }
}
