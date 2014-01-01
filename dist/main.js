


define('view-maincontentview',[
    'jquery',
    'backbone'
], function($, Backbone) {
    var MainContentView = Backbone.View.extend({
        currentController: null,
        displayController: function (controller) {
            if (this.currentController !== undefined && this.currentController !== null) {
                this.currentController.destroy();
            }

            this.currentController = controller;
            this.render();
        },
        render: function () {
            console.log(this.$el);
            $('.content').html(this.currentController.render().$el);
        }
    });

    return new MainContentView;
});



define('model-abstract-entity',[
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



define('model-tag-tagmodel',[
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



define('collection-tag-tagcollection',[
    'jquery',
    'underscore',
    'backbone',
    'model-tag-tagmodel'
], function($, _, Backbone, TagModel) {
    var TagsCollection = Backbone.Collection.extend({
        model: TagModel,
        url: '/tag/',

        parse: function (response) {
            return response.payloads.tags;
        },

        comparator: 'displayOrder',

        color: function (color) {
            return this.filter(function (tag) {
                return tag.get('color') == color;
            });
        }
    });

    return TagsCollection;
});



define('view-tag-tagview',[
    'jquery',
    'underscore',
    'backbone',
    'model-tag-tagmodel'
], function($, _, Backbone, TagModel) {
    var TagView = Backbone.View.extend({
        className: 'tag-object',
        template: _.template($('<div><span></span></div>').html()),

        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },

        render: function () {
            this.$el.html(this.template(this.model.toJSON()));
            this.$('span').text(this.model.get('label'));

            if (this.model.get('color')) {
                this.$el.addClass('tag-color-' + this.model.escape('color'));
            }

            return this;
        }
    });

    return TagView;
});



define('view-tag-taglistview',[
    'jquery',
    'underscore',
    'backbone',
    'model-tag-tagmodel',
    'collection-tag-tagcollection',
    'view-tag-tagview'
], function($, _, Backbone, TagModel, TagCollection, TagView) {
    var TagListView = Backbone.View.extend({
        $el: $('<div />'),
        $list: [],

        initialize: function () {
            this.listenTo(this.collection, 'add', this.addOne);
            this.listenTo(this.collection, 'remove', this.removeOne);
            this.listenTo(this.collection, 'reset', this.addAll);

            // Add initial thingies
            this.addAll();
        },

        addAll: function () {
            this.collection.each(this.addOne, this);
        },

        addOne: function (model, collection, options) {
            var tag = new TagView({model: model, id: 'tag-' + model.cleanId()});
            this.$el.append(tag.render().$el);
            this.$list.push(tag);
        },

        removeOne: function (model, collection, options) {
            tagview = _.find(this.$list, function (view) { return view.model.id == model.id; });

            if (tagview !== undefined) {
                tagview.remove();
            }
        }
    });

    return TagListView;
});



define('controller-tag-listing',[
    'jquery',
    'underscore',
    'backbone',
    'collection-tag-tagcollection',
    'view-tag-taglistview'
], function($, _, Backbone, TagCollection, TagListView) {
    var TagListingController = Backbone.View.extend({
        tags: null,
        taglist: null,
        initialize: function () {
            this.tags = new TagCollection;
            this.tags.fetch({reset: true});

            this.taglist = new TagListView({collection: this.tags});
        },
        render: function () {
            this.$el.html(this.taglist.render().$el);
            return this;
        }
    });

    return TagListingController;
});



define('router-tag-tagrouter',[
    'jquery',
    'underscore',
    'backbone',
    'view-maincontentview',
    'controller-tag-listing'
], function($, _, Backbone, MainContentView, TagListingController) {
    var TagRouter = Backbone.Router.extend({
        routes: {
            'tag/': 'listTags',
            'tag/create': 'createTag',
            'tag/:id': 'displayTag'
        },

        listTags: function () {
            MainContentView.displayController(new TagListingController);
        },

        createTag: function () {
            console.log("Creating tags not implemented!");
        },

        displayTag: function (id) {
            console.log("Displaying tags not implemented!");
        }
    });

    return {
        _instance: null,
        get: function () {
            if (this._instance === null) {
                this._instance = new TagRouter;
            }

            return this._instance;
        },
        initialize: function () {
            this.get();
        }
    };
});



define('app',[
    'jquery',
    'underscore',
    'backbone',
    'router-tag-tagrouter',
], function($, _, Backbone, TagRouter) {
    var initialize = function() {
        // TODO: Write all application routers, and start them here
        TagRouter.initialize();

        // Kicking off routing
        Backbone.history.start({
            pushState: true,
            hashChange: false // Continue using URLs for older browsers
        });
    };

    return {
        initialize: initialize
    };
});



require.config({
    shim: {
        // Not adding Underscore.js and Backbone.js here, since we have
        // patched them to call `define` already
    },
    paths: {
        // Not loading Underscore, Backbone and jQuery, since they are in
        // `libs-pck` already
        jquery: '/rsrc/js/external-jquery-1-10-2',
        underscore: '/rsrc/js/external-underscore',
        backbone: '/rsrc/js/external-backbone',
        templates: '/rsrc/js/templates-'
    },

    // Add this map config in addition to any baseUrl or
    // paths config you may already have in the project.
    map: {
        // '*' means all modules will get 'jquery-private'
        // for their 'jquery' dependency.
        '*': {
            'jquery': 'jquery-private',
            // text is actually external-text (because it's in that folder)
            'text':   'external-text'
        },

        // 'jquery-private' wants the real jQuery module
        // though. If this line was not here, there would
        // be an unresolvable cyclic dependency.
        'jquery-private': { 'jquery': 'jquery' }
    }
});

require([
    'app',
], function(App){
    App.initialize();
});

define("main", function(){});
