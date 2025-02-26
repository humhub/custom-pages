humhub.module('custom_pages.template.TemplateStructure', function (module, require, $) {
    const Widget = require('ui.widget').Widget;
    const object = require('util').object;
    const modal = require('ui.modal');
    const client = require('client');

    const TemplateStructure = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(TemplateStructure, Widget);

    TemplateStructure.prototype.init = function () {
        const rootTemplate = this.$.closest('[data-template-type="layout"][data-template-instance-id]');
        this.rootTemplateInstanceId = rootTemplate.data('template-instance-id');
        this.createContainerUrl = rootTemplate.data('create-container-url');
        this.itemAddUrl = rootTemplate.data('item-add-url');
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

    TemplateStructure.prototype.deleteContainerItem = function (evt) {
        const containerItem = evt.$target.closest('[data-container-item-id]');

        const data = {
            itemId: containerItem.data('container-item-id'),
            elementId: containerItem.data('element-id'),
            elementContentId: containerItem.data('element-content-id'),
        };

        client.post(evt, {data}).then(function (response) {
            if (response.success) {
                location.reload();
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    }

    module.export = TemplateStructure;
});
