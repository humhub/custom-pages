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

        this.editTemplateUrl = this.data('template-edit-url');
        this.editUrl = this.editor.options.itemEditUrl;
        this.deleteUrl = this.editor.options.itemDeleteUrl;
        this.isContainerItem = true;
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
        
        if(this.options.allowInlineActivation) {
            items.push(this.createContainerToggle());
        }

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

            'afterInsert': function () {
                var $containerEditToggle = that.$menu.find('#containerEditToggle');
                $containerEditToggle.bootstrapSwitch({
                    'size': 'mini',
                    'state': that.data('isActiveItem'),
                    'onText': customPage.text('toggleOnText'),
                    'offText': customPage.text('toggleOffText')
                });
            },
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
                ownerContentId: parent.ownerContentId,
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
        var that = this;
        return {
            itemId: that.itemId,
            ownerContentId: that.getParentElement().ownerContentId
        };
    };

    TemplateContainerItem.prototype.startInlineEdit = function () {
        if ($('#overlay').length) {
            $('#overlay').remove();
        }

        $('.editMenu').css('z-index', '1028');
        this.$.css('background-color', '#fff');
        this.$.css('z-index', '1027');
        $('<div id="overlay" style="display:none;"></div>').insertBefore(this.$).fadeIn('fast');

        this.data('isActiveItem', true);
        this.data('active', true);
        this.$root.trigger('custom_pages.afterActivateContainer', [this]);
    };

    TemplateContainerItem.prototype.stopInlineEdit = function () {
        $('.editMenu').css('z-index', '0');
        this.data('isActiveItem', false);
        var that = this;
        var $overlay = $('#overlay');
        if ($overlay.length) {
            $overlay.fadeOut('fast', function () {
                $overlay.remove();
                that.$root.trigger('custom_pages.afterDeactivateContainer', [that]);
            });
        }
        this.$.css('background-color', '');
        this.$.css('z-index', '0');
    };

    TemplateContainerItem.prototype.getUniqueId = function ($element) {
        if (!$element) {
            return;
        }

        return 'templateContainerItem' + ':' + $element.data('template-item');
    };

    TemplateContainerItem.prototype.createContainerToggle = function () {
        var that = this;
        return $('<input id="containerEditToggle" type="checkbox" />')
                .on('switchChange.bootstrapSwitch', function (event, state) {
                    if (state) {
                        that.startInlineEdit();
                    } else {
                        that.stopInlineEdit();
                    }
                });
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