'use strict';

$(function ()
{
    $('.show-changes-btn').click(function (e)
    {
        e.preventDefault()
        var $this = $(this)
        $this.parent().children('.xact-container').slideDown()

        $this.slideUp(function () { $this.parent().children('.hide-changes-btn').slideDown() })
    })
    $('.hide-changes-btn').click(function (e)
    {
        e.preventDefault()
        var $this = $(this)
        $this.parent().children('.xact-container').slideUp()

        $this.slideUp(function () { $this.parent().children('.show-changes-btn').slideDown() })
    })
})
