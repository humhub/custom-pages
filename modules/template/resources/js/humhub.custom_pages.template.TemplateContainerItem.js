humhub.module('custom_pages.template.TemplateContainerItem', function (module, require, $) {
    var customPage = require('custom_pages.template.editor');
    var client = require('client');
    var object = require('util').object;
    var string = require('util').string;
    var TemplateElement = require('custom_pages.template.TemplateElement');

    /**
     * TemplateContainerItem
     * 
     * @param {type} $elem
     * @returns {template_L2.TemplateContainerItem}
     */
    TemplateContainerItem = function (node, options) {
        TemplateElement.call(this, node, options);
        this.itemId = this.data('template-item');
        this.parent = this.getParent();
        this.name = this.title = this.data('template-item-title');
        this.inline = this.$.hasClass('inline');
        if (!this.name) {
            this.name = this.title = this.getParent().name + ':' + this.$.index();
        }

        this.editUrl = this.editor.options.itemEditUrl;
        this.deleteUrl = this.editor.options.itemDeleteUrl;
    };

    object.inherits(TemplateContainerItem, TemplateElement);

    TemplateContainerItem.prototype.getName = function() {
        var name = this.$.children('[data-title]').data('title');
        if(!name || !name.length) {
            name = this.data('template-item-title');
        }
        
        return name;
    }

    TemplateContainerItem.prototype.isFirst = function () {
        return this.$.index() === 0;
    };

    TemplateContainerItem.prototype.isLast = function () {
        return (this.$.index() + 1) === this.getParent().$.children().length;
    };

    TemplateContainerItem.prototype.renderMenu = function () {
        if (this.$menu) {
            this.$menu.remove();
        }

        var that = this;
        var items = [];

        if (!this.isFirst()) {
            items.push(this.createMoveButton(-1));
        }

        if (!this.isLast()) {
            items.push(this.createMoveButton(1));
        }

        items.push(this.createEditButton('small'));
        items.push(this.createDeleteButton('small'));

        this.renderMenuItems({
            items: items,
            cssClass: 'elementToggleMenu',
            position: 'lt',
            outside: true,
            leftAlign: -13,
            beforeShow: function () {
                that.$menu.css('left', '+=13px');
            }
        });
    };

    TemplateContainerItem.prototype.createMoveButton = function (step) {
        var iconClass;

        if (this.inline) {
            iconClass = (step > 0) ? 'fa-caret-right' : 'fa-caret-left';
        } else {
            iconClass = (step > 0) ? 'fa-caret-down' : 'fa-caret-up';
        }

        return $(string.template(TemplateContainerItem.template.moveButton, {iconClass: iconClass, target: this.id, step: step}));
    };
    
    TemplateContainerItem.prototype.deleteAction = function (evt) {
        var that = this;
        client.post(evt, {data: this.getEditData()}).then(function (response) {
            if (response.success) {
                that.editor.replaceElement(that.getParent(), response.output);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    TemplateContainerItem.prototype.moveItem = function (evt) {
        var that = this;
        var parent = this.getParent();
        
        var options = {
            url: this.editor.options.itemMoveUrl,
            data : {
                elementContentId: parent.elementContentId,
                itemId: that.itemId,
                step: evt.$trigger.data('step')
            }
        };
        
        this.loader();
        client.get(evt, options).then(function (response) {
            if (response.success) {
                that.deactivate();
                parent.$.replaceWith(response.output);
                parent.highlight();
            }
        }).catch(function (e) {
            module.log.error(e, true);
        }).finally(function() {
            that.loader(false);
        });
    };
    
    TemplateContainerItem.prototype.getEditData = function () {
        return {
            itemId: this.itemId,
            elementId: this.getParentElement().elementId,
            elementContentId: this.getParentElement().elementContentId
        };
    };

    TemplateContainerItem.prototype.getUniqueId = function ($element) {
        if (!$element) {
            return;
        }

        return 'templateContainerItem' + ':' + $element.data('template-item');
    };

    TemplateElement.prototype.getDeleteConfirmOptions = function () {
        return {
            confirmHeader: customPage.text('confirmDeleteItemHeader'),
            confirmBody: customPage.text('confirmDeleteItemBody'),
            confirmText: customPage.text('confirmDeleteButton')
        };
    };

    TemplateContainerItem.template.moveButton = '<a data-action-click="moveItem" data-step="{step}" data-action-target="#{target}" class="btn btn-success btn-xs tt template-menu-button" href="#"><i class="fa {iconClass}"></i></a>';

    module.export = TemplateContainerItem;
});
