'use strict';

$(function () {
    $('#form-tag-selector')
        .textext({
            plugins : 'tags prompt autocomplete arrow filter',
            tagsItems : [ 'Basic', 'JavaScript', 'PHP', 'Scala' ],
            prompt : 'Add some tags...'
        })
        .bind('getSuggestions', function(e, data) {
            var list = [
                'Basic',
                'Closure',
                'Cobol',
                'Delphi',
                'Erlang',
                'Fortran',
                'Go',
                'Groovy',
                'Haskel',
                'Java',
                'JavaScript',
                'OCAML',
                'PHP',
                'Perl',
                'Python',
                'Ruby',
                'Scala'
            ],
            textext = $(e.target).textext()[0],
            query = (data ? data.query : '') || ''
            ;

            $(this).trigger(
                'setSuggestions',
                { result : textext.itemManager().filter(list, query) }
            );
        })
    ;
});
