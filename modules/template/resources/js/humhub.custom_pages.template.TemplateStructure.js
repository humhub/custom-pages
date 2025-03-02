humhub.module('custom_pages.template.TemplateStructure', function (module, require, $) {
    const Widget = require('ui.widget').Widget;
    const object = require('util').object;
    const modal = require('ui.modal');
    const client = require('client');
    const loader = require('ui.loader');

    const TemplateStructure = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(TemplateStructure, Widget);

    TemplateStructure.prototype.init = function () {
        const rootTemplate = this.$.closest('[data-template-type="layout"][data-template-instance-id]');
        this.rootTemplateInstanceId = rootTemplate.data('template-instance-id');
        this.elementsEditUrl = rootTemplate.data('elements-edit-url');
        this.createContainerUrl = rootTemplate.data('create-container-url');
        this.itemAddUrl = rootTemplate.data('item-add-url');
        this.itemMoveUrl = rootTemplate.data('item-move-url');
        this.itemDeleteUrl = rootTemplate.data('item-delete-url');
    }

    TemplateStructure.prototype.addContainerItem = function (evt) {
        const container = evt.$target.closest('[data-element-id]');

        modal.load(evt, {
            url:  container.data('default') !== undefined ? this.createContainerUrl : this.itemAddUrl,
            dataType: 'json',
            data: {
                templateInstanceId: this.rootTemplateInstanceId,
                elementId: container.data('element-id'),
                elementContentId: container.data('element-content-id'),
            }
        });
    };

    TemplateStructure.prototype.editElements = function (evt) {
        modal.load(evt, {
            url:  this.elementsEditUrl,
            dataType: 'json',
            data: {
                id: evt.$target.closest('[data-template-instance-id]').data('template-instance-id'),
            }
        });
    };

    TemplateStructure.prototype.moveContainerItem = function (evt, direction) {
        const container = evt.$target.closest('[data-element-id]');
        const options = {
            url: this.itemMoveUrl,
            data : {
                elementContentId: container.data('element-content-id'),
                itemId: container.data('container-item-id'),
                step: direction === 'up' ? -1 : 1
            }
        };

        loader.set(container);
        client.get(evt, options).then(function (response) {
            if (response.success) {
                direction === 'up'
                    ? container.prev().before(container)
                    : container.next().after(container);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        }).finally(function() {
            loader.reset(container);
        });
    }

    TemplateStructure.prototype.moveUpContainerItem = function (evt) {
        this.moveContainerItem(evt, 'up')
    }

    TemplateStructure.prototype.moveDownContainerItem = function (evt) {
        this.moveContainerItem(evt, 'down')
    }

    TemplateStructure.prototype.deleteContainerItem = function (evt) {
        const containerItem = evt.$target.closest('[data-container-item-id]');
        const options = {
            url: this.itemDeleteUrl,
            data: {
                itemId: containerItem.data('container-item-id'),
                elementId: containerItem.data('element-id'),
                elementContentId: containerItem.data('element-content-id'),
            }
        };

        client.post(evt, options).then(function (response) {
            if (response.success) {
                containerItem.fadeOut('fast', () => containerItem.remove());
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    }

    module.export = TemplateStructure;
});
