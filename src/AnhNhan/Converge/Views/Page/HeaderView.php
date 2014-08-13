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
            ->append(
                div('nav-user')
                    ->append(div('nav-user-img', cv\ht('img')->addOption('src', idx($this->user_details, 'image_path', '/images/profile/default.png'))))
                    ->append(
                        div('nav-user-box')
                            ->append(div('nav-user-box-name', h3(idx($this->user_details, 'username', 'Not logged in'))))
                            ->append(
                                div('nav-user-box-detail')
                                    ->append(cv\icon_ion('0', 'android-mail'))
                            )
                    )
            )
        ;

        $header_content
            ->append(
                div('nav-action')
                    ->append(a(cv\icon_ion('', 'search'), 'disq/search')->addClass('btn btn-default'))
                    ->append(a(cv\icon_ion('', 'ios7-chatboxes-outline'), 'disq/create')->addClass('btn btn-default'))
                    ->append(a(cv\icon_ion('', 'ios7-gear'), 'o/settings')->addClass('btn btn-default'))
            )
        ;

        $header_content
            ->append(
                div('nav-join')
                    ->append(a('Logout', 'logout')->addClass('btn btn-default'))
                    ->append(a('Join Us!', 'join')->addClass('btn btn-info'))
                    ->append(a('Login', 'login')->addClass('btn btn-primary'))
            )
        ;

        $header_link = a(null, '/', true)
            ->append(h2('Converge'))
            ->append(h4('Make me one with everything.'))
        ;
        $header_content->append($header_link);

        return $header_content;
    }
}
