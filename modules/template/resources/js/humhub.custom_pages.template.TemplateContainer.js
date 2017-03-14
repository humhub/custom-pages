humhub.module('custom_pages.template.TemplateContainer', function (module, require, $) {
    var string = require('util').string;
    var object = require('util').object;
    var modal = require('ui.modal');
    var TemplateElement = require('custom_pages.template.TemplateElement');

    /**
     * TemplateContainer
     *  
     * @param {type} $elem
     * @returns {template_L2.TemplateContainerItem}         
     * */
    TemplateContainer = function (node, options) {
        TemplateElement.call(this, node, options);
    };

    object.inherits(TemplateContainer, TemplateElement);

    TemplateContainer.prototype.init = function () {
        this.super('init');
        this.isContainer = true;
        this.multiple = this.options.templateMultiple;
        this.templateId = this.options.templateId;
        this.inline = this.$.hasClass('inline');
    };

    TemplateContainer.prototype.renderMenu = function () {
        var items = [];
        if (this.multiple || !this.hasItems()) {
            items.push(this.createAddItemButton());
        }

        this.renderMenuItems({
            items: items,
            outside: true
        });
    };

    TemplateContainer.prototype.hasItems = function () {
        return this.$.find('[data-template-item]').length !== 0;
    };

    TemplateContainer.prototype.createAddItemButton = function () {
        return $(string.template(TemplateElement.template.addButton, {target: this.id}));
    };

    TemplateContainer.prototype.addItem = function (evt) {
        var options = {
            url:  (this.default) ? this.editor.options.createContainerUrl : this.editor.options.itemAddUrl,
            dataType: 'json',
            data: {
                ownerContentId: this.ownerContentId
            }
        };
        
        if (this.default) {
            options.data.ownerModel = this.owner;
            options.data.ownerId = this.ownerId;
        }         

        modal.load(evt, options);
    };

    TemplateContainer.template['addButton'] = '<a data-action-click="addItem" data-action-target="#{target}" class="template-menu-button btn btn-success btn-sm tt" href="#"><i class="fa fa-plus"></i></a>';

    module.export = TemplateContainer;
});