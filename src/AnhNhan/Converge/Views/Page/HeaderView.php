<?php
namespace AnhNhan\Converge\Views\Page;

use AnhNhan\Converge as cv;
use AnhNhan\Converge\Modules\User\Storage\User;
use AnhNhan\Converge\Modules\User\Views\UserPlateView;
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

        $header_content
            ->appendContent(
                div('nav-user')
                    ->appendContent(div('nav-user-img', cv\ht('img')->addOption('src', idx($this->user_details, 'image_path', '/images/profile/default.png'))))
                    ->appendContent(
                        div('nav-user-box')
                            ->appendContent(div('nav-user-box-name', h3(idx($this->user_details, 'username', 'Not logged in'))))
                            ->appendContent(
                                div('nav-user-box-detail')
                                    ->appendContent(cv\icon_ion('0', 'android-mail'))
                            )
                    )
            )
        ;

        $header_content
            ->appendContent(
                div('nav-action')
                    ->appendContent(a(cv\icon_ion('', 'search'), 'disq/search')->addClass('btn btn-default'))
                    ->appendContent(a(cv\icon_ion('', 'ios7-chatboxes-outline'), 'disq/create')->addClass('btn btn-default'))
                    ->appendContent(a(cv\icon_ion('', 'ios7-gear'), 'o/settings')->addClass('btn btn-default'))
            )
        ;

        $header_content
            ->appendContent(
                div('nav-join')
                    ->appendContent(a('Logout', 'logout')->addClass('btn btn-default'))
                    ->appendContent(a('Join Us!', 'join')->addClass('btn btn-info'))
                    ->appendContent(a('Login', 'login')->addClass('btn btn-primary'))
            )
        ;

        $header_link = a(null, '/', true)
            ->appendContent(h2('Converge'))
            ->appendContent(h4('Make me one with everything.'))
        ;
        $header_content->appendContent($header_link);

        return $header_content;
    }
}
