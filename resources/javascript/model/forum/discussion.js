'use strict';

define([
    'model-abstract-entity'
], function(AbstractEntityModel) {
    var DiscussionModel = AbstractEntityModel.extend({
        urlRoot: '/disq/',
        defaults: {
            uidTyppe: 'DISQ',
            // uid, mandatory
            // label, mandatory
            label: '',
            author: '',
            postCount: 0,
            createdAt: 0,
            lastActivity: 0
        },

        lastActivity: function () {
            var d_names = ["Sun", "Mon", "Tue",
            "Wed", "Thu", "Fri", "Sat"];

            var m_names = ["Jan", "Feb", "Mar",
            "Apr", "May", "Jun", "Jul", "Aug", "Sep",
            "Oct", "Nov", "Dec"];

            var d = new Date(this.get('lastActivity') * 1000);
            var curr_day = d.getDay();
            var curr_date = d.getDate();
            var curr_month = d.getMonth();
            var curr_year = d.getFullYear() % 100;

            return d_names[curr_day] + ", " + curr_date + " " + m_names[curr_month] + " '" + (curr_year < 10 ? ' ' : '') + curr_year;
        }
    });

    return DiscussionModel;
});
