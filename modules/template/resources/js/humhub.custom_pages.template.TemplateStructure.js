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
        this.$.draggable({
            handle: '.cp-ts-header',
        });
    }

    TemplateStructure.prototype.addContainerItem = function (evt) {
        const container = evt.$target.closest('[data-element-id]');

        modal.load(evt, {
            url:  container.data('default') !== undefined ? this.data('create-container-url') : this.data('item-add-url'),
            dataType: 'json',
            data: {
                templateInstanceId: this.data('template-instance-id'),
                elementId: container.data('element-id'),
                elementContentId: container.data('element-content-id'),
            }
        });
    };

    TemplateStructure.prototype.editElements = function (evt) {
        modal.load(evt, {
            url:  this.data('elements-edit-url'),
            dataType: 'json',
            data: {
                id: evt.$target.closest('[data-template-instance-id]').data('template-instance-id'),
            }
        });
    };

    TemplateStructure.prototype.moveContainerItem = function (evt, direction) {
        const container = evt.$target.closest('[data-element-id]');
        const options = {
            url: this.data('item-move-url'),
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
            url: this.data('item-delete-url'),
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
