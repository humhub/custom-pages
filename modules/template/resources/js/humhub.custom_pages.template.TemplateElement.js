humhub.module('custom_pages.template.TemplateElement', function (module, require, $) {
    var customPage = require('custom_pages.template.editor');
    var Widget = require('ui.widget').Widget;
    var object = require('util').object;
    var string = require('util').string;
    var client = require('client');
    var modal = require('ui.modal');
    var loader = require('ui.loader');


    TemplateEditor = function (node, options) {
        Widget.call(this, node, options);
    };

    /**
     * Template Element
     */
    var TemplateElement = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(TemplateElement, Widget);

    TemplateElement.prototype.init = function () {
        this.editor = customPage.editor;
        this.$root = this.editor.$;

        this.id = this.$.attr('id');
        this.editUrl = this.editor.options.elementEditUrl;
        this.deleteUrl = this.editor.options.elementDeleteUrl;

        this.name = this.options.templateElement;
    
        this.title = this.options.templateElementTitle || this.name;
        this.content = this.options.templateContent;
        this.contentId = this.options.templateContentId;
        this.owner = this.options.templateOwner;
        this.ownerId = this.options.templateOwnerId;
        this.empty = this.options.templateEmpty;
        this.default = this.options.templateDefault;
        this.ownerContentId = this.options.templateOwnerContentId;
        this.label = this.options.templateLabel;
    };

    TemplateElement.prototype.isEqual = function (element) {
        return this.$[0] === element.$[0];
    };

    TemplateElement.prototype.isActive = function () {
        return this.data('active') == true;
    };

    TemplateElement.prototype.isParentOf = function (element) {
        var parent = element.getParent();
        return parent && parent.id === this.id;
    };

    TemplateElement.prototype.highlight = function () {
        this.$.addClass((this.empty) ? 'highlightAdd' : 'highlightEdit');
    };

    TemplateElement.prototype.getParent = function () {
        var $parent = this.$.parent().closest('[data-template-element], [data-template-item]');

        if ($parent.length) {
            return this.editor.getElement($parent);
        }
    };

    TemplateElement.prototype.getParentElement = function () {
        var $parent = this.$.parent().closest('[data-template-element]');

        if ($parent.length) {
            return this.editor.getElement($parent);
        }
    };

    TemplateElement.prototype.isPartOfInlineEdit = function () {
        return !this.editor.activeItem || this.editor.activeItem.isParentOf(this);
    };

    TemplateElement.prototype.activate = function (prevenParentActivation) {
        if (!prevenParentActivation) {
            var parent = this.getParentElement();

            if (parent) {
                if (!parent.isActive() && parent.isPartOfInlineEdit()) {
                    parent.activate(true);
                    this.editor.activeElements.push(parent);
                }
            }
        }

        if (this.isActive()) {
            return;
        }

        this.highlight();
        this.data('active', true);
        this.renderMenu();
    };

    TemplateElement.prototype.renderMenu = function () {
        var items = [this.createEditButton()];

        if (!this.default && !this.empty) {
            items.push(this.createDeleteButton());
        }
        
        this.renderMenuItems({items: items});
    };

    TemplateElement.prototype.createEditButton = function (size) {
        var btnSizeClass;
        switch (size) {
            case 'small':
                btnSizeClass = 'btn-xs';
                break;
            default:
                btnSizeClass = 'btn-sm';
                break;
        }

        return $(string.template(TemplateElement.template.editButton, {target: this.id, url: this.editUrl, btnSizeClass: btnSizeClass}));
    };



    TemplateElement.prototype.getEditData = function () {
        return {
            ownerModel: this.owner,
            ownerId: this.ownerId,
            name: this.name
        };
    };

    TemplateElement.prototype.createDeleteButton = function (size) {
        var btnSizeClass;
        switch (size) {
            case 'small':
                btnSizeClass = 'btn-xs';
                break;
            default:
                btnSizeClass = 'btn-sm';
                break;
        }
        
        var options = {
            
            target: this.id,
            url: this.deleteUrl,
            btnSizeClass: btnSizeClass
        };
        
        options = $.extend(options, this.getDeleteConfirmOptions());

        return $(string.template(TemplateElement.template.deleteButton, options));
    };
    
    TemplateElement.prototype.getDeleteConfirmOptions = function () {
        return {
            confirmHeader: customPage.text('confirmDeleteContentHeader'),
            confirmBody: customPage.text('confirmDeleteContentBody'),
            confirmText: customPage.text('confirmDeleteButton')
        };
    };

    TemplateElement.prototype.editAction = function (evt) {
        modal.load(evt, {dataType:'json', data: this.getEditData()});
    };

    TemplateElement.prototype.deleteAction = function (evt) {
        var that = this;
        this.loader();
        client.post(evt, {data: this.getEditData()}).then(function (response) {
            if (response.success) {
                that.editor.replaceElement(that, response.output);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    TemplateElement.prototype.renderMenuItems = function (options) {
        var that = this;
        if (this.$menu) {
            that.$menu.remove();
        }

        options.cssClass = options.cssClass || 'elementMenu';
        this.$menu = (!this.$menu) ? $(string.template(TemplateElement.template.menu, {cssClass : options.cssClass})) : this.$menu;

        if (this.name) {
            this.$menu.append($('<span>' + this.title + '</span>'));
        } 
        
        if(this.label) {
            this.$menu.append($('<span class="label label-warning">' + this.label + '</span>'));
        }
        
        this.$menu.append(options.items);

        this.$menu.on('mouseover', function (evt) {
            evt.stopPropagation();
            if ($('#overlay').length) {
                that.$menu.css('z-index', '1029');
            } else {
                that.$menu.css('z-index', '1');
            }
        });

        this.$menu.on('click', function (evt) {
            //evt.stopPropagation();
        });

        this.$menu.on('mouseout', function (evt) {
            if ($('#overlay').length) {
                that.$menu.css('z-index', '1028');
            } else {
                that.$menu.css('z-index', '0');
            }
        });

        if ($('#overlay').length) {
            that.$menu.css('z-index', '1028');
        }

        $('body').append(this.$menu);

        if (options.afterInsert) {
            options.afterInsert.call();
        }

        var position = (options.position) ? options.position : 'rt';

        var offset = this.$.offset();

        var left = position.indexOf('l') === 0;
        var top = position.indexOf('t') === 1;

        var offsetTopAlign = options.topAlign || 0;
        var offsetLeftAlign = options.leftAlign || 0;

        if (this.$.is('span, a')) {
            options.outside = true;
        }

        if (options.outside && offset.top <= this.$root.offset().top + 5) {
            top = 0;
        }

        if (top) {
            offsetTopAlign += (options.outside) ? -(this.$menu.height()) : 5;
        } else {
            offsetTopAlign += that.$.outerHeight() - that.$menu.outerHeight();
            offsetTopAlign += (options.outside) ? (this.$menu.height()) : -5;
        }

        if (left) {
            offsetLeftAlign += (options.outside) ? 0 : +5;
        } else {
            offsetLeftAlign += that.$.outerWidth() - that.$menu.outerWidth();
            offsetLeftAlign += (options.outside) ? 0 : -5;
        }

        this.$menu.css({
            'top': offset.top + offsetTopAlign,
            'left': offset.left + offsetLeftAlign
        });

        if (options.beforeShow) {
            options.beforeShow.call();
        }

        this.$menu.fadeIn('fast');
    };

    TemplateElement.prototype.deactivate = function () {
        if (this.$menu) {
            this.$menu.remove();
            this.$menu = undefined;
        }

        this.data('active', false);

        this.$.removeClass('highlightAdd').removeClass('highlightEdit');
    };

    TemplateElement.prototype.data = function (key, value) {
        if (typeof value !== 'undefined') {
            return this.$.data(key, value);
        } else {
            return this.$.data(key);
        }
    };

    TemplateElement.prototype.getUniqueId = function ($element) {
        return this.$.attr('id');
        if (!$element) {
            return;
        }

        return $element.data('template-empty') ? Date.now().toString() :
                $element.data('template-content') + ':' + $element.data('template-content-id');
    };
    
    TemplateElement.prototype.loader = function (show) {
        if(show === false) {
            loader.reset(this.$);
        } else {
            loader.set(this.$, {
                'alignHeight': true
            });
        }
    };

    TemplateElement.template = {
        menu: '<div class="editMenu {cssClass}" style="display:none;"></div>',
        editButton: '<a data-action-click="editAction" data-action-url="{url}" data-action-target="#{target}" class="template-menu-button btn btn-primary {btnSizeClass} tt" href="#"><i class="fa fa-pencil"></i></a>',
        deleteButton: '<a data-action-click="deleteAction" data-action-url="{url}" data-action-target="#{target}" data-action-confirm="{confirmBody}" data-action-confirm-header="{confirmHeader}" data-action-confirm-text="{confirmText}" class="template-menu-button btn btn-danger {btnSizeClass} tt" href="#"><i class="fa fa-times"></i></a>'
    };

    module.export = TemplateElement;
});