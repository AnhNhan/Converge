'use strict';

define([
    'model-abstract-entity'
], function(AbstractEntityModel) {
    var TagModel = AbstractEntityModel.extend({
        urlRoot: '/tag/',
        defaults: {
            uidTyppe: 'TTAG',
            // uid, mandatory
            // label, mandatory
            color: '',
            displayOrder: 0,
            description: null
        }
    });

    return TagModel;
});
