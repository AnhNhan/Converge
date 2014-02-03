'use strict';

$(function () {
    var self = this, $this = $('.disq-tag-selector'), _text = $this.text(), _tags, tags = [];

    if (_text) {
        _tags = _text.split(',');
        _tags.forEach(function (val, i) {
            tags.push(val.trim());
        });
        $this.text('');
    }

    $this
        .textext({
            plugins : 'ajax filter tags prompt autocomplete arrow',
            prompt : 'Add some tags...',
            tagsItems: tags,
            filterItems: tags,
            ajax : {
                url : 'search/autocomplete/tags',
                dataType : 'json',
                cacheResults : false
            },
            autocomplete: {
                dropdown: {
                    maxHeight: '300px'
                }
            }
        })
    ;
});
