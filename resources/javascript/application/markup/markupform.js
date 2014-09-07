(function ($) {
    var MarkupAuthoringForm = function (element, options) {
        this.options  =
        this.$element =
        this.$output  =
        this.$source  = null

        this.$element = $(element)
        this.options = options = $.extend({}, MarkupAuthoringForm.defaults, this.$element.data(), options)
        this.$output = $(options.output_selector)
        this.$output.html(options.output_template)

        var refreshFunc = _.debounce($.proxy(this.process, this), options.bounce_delay)

        this.$element
            .on('change.cv.markupform', refreshFunc)
            .on('keyup.cv.markupform', refreshFunc)
        this.process()
    }

    MarkupAuthoringForm.defaults = {
        preview_uri:  'markup/process',
        output_selector: '.markup-preview-output',
        output_template: '<div class="muted pull-right" style="text-align: right;"><div class="time"></div><div class="saved"></div></div><div class="text"></div>',
        type: 'POST',
        dataType: 'json',
        bounce_delay: 500
    }

    MarkupAuthoringForm.prototype.process = function () {
        var uri_suffix = ''
        if (this.options.draftKey)
        {
            uri_suffix = '?draft_object_key=' + this.options.draftKey
        }
        $.ajax({
            url: this.options.preview_uri + uri_suffix,
            type: this.options.type,
            data: { text: this.$element.val() },
            dataType: this.options.dataType,
            $output: this.$output,
            error: function (x,e) {console.log(x, e)},
            success: function (data) {
                    if (data.status === 'ok') {
                        this.$output.find('.text').html(data.payloads.contents)
                        this.$output.find('.time').html('Took me ' + data.payloads.time + 'ms!')
                        this.$output.find('.saved').html(data.payloads.last_saved)
                    } else {
                        console.log("Error while processing markup!")
                    }
                }
            })
    }


    var old = $.fn.markupform

    $.fn.markupform = function (option) {
        if (this.length == 1)
            new MarkupAuthoringForm(this, option)
        return this
    }

    $.fn.markupform.Constructor = MarkupAuthoringForm

    $.fn.markupform.noConflict = function () {
        $.fn.markupform = old
        return this
    }
})(jQuery);
