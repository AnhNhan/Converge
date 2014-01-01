'use strict';

define([
    'underscore',
    'backbone'
], function(_, Backbone) {
    var AbstractEntityModel = Backbone.Model.extend({
        url: function () {
            newId = this.cleanId();
            return this.urlRoot + newId;
        },
        idAttribute: 'uid',
        defaults: {
            uidTyppe: 'XUID'
            // uid, mandatory
        },

        cleanId: function () {
            return this.id.replace(this.get('uidTyppe') + '-', '');
        }
    });

    return AbstractEntityModel;
});
