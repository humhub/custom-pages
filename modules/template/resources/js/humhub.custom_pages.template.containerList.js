humhub.module('custom_pages.template.ContainerItemsList', function (module, require, $) {
    const Widget = require('ui.widget').Widget;
    const object = require('util').object;
    const client = require('client');
    const modal = require('ui.modal');
    const loader = require('ui.loader');

    const ContainerItemsList = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(ContainerItemsList, Widget);

    ContainerItemsList.prototype.structure = function () {
        return Widget.instance('[data-ui-widget="custom_pages.template.TemplateStructure"]');
    };

    ContainerItemsList.prototype.editItem = function (evt) {
        modal.load(evt, {
            url: this.data('elements-edit-url'),
            dataType: 'json',
            data: {
                id: evt.$trigger.closest('[data-item-id]').data('template-instance-id'),
                parentId: this.data('template-instance-id'),
            },
        });
    };

    ContainerItemsList.prototype.addItem = function () {
        // The structure view implements the whole flow of adding an item, including the creation of a container
        $(this.data('add-button-selector')).click();
    };

    ContainerItemsList.prototype.moveUp = function (evt) {
        this.moveItem(evt, -1);
    };

    ContainerItemsList.prototype.moveDown = function (evt) {
        this.moveItem(evt, 1);
    };

    ContainerItemsList.prototype.moveItem = function (evt, step) {
        const that = this;
        const row = evt.$trigger.closest('[data-item-id]');
        if ((step < 0 && !row.prev().length) || (step > 0 && !row.next().length)) {
            return;
        }

        loader.set(row);
        client.get(evt, {
            url: this.data('item-move-url'),
            data: {
                elementContentId: this.data('container-id'),
                itemId: row.data('item-id'),
                step: step,
            },
        }).then(function (response) {
            if (response.success) {
                step < 0 ? row.prev().before(row) : row.next().after(row);
                that.afterChange(response);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        }).finally(function () {
            loader.reset(row);
        });
    };

    ContainerItemsList.prototype.deleteItem = function (evt) {
        const that = this;
        const row = evt.$trigger.closest('[data-item-id]');

        client.post(evt, {
            url: this.data('item-delete-url'),
            data: {
                itemId: row.data('item-id'),
                elementId: this.data('element-id'),
                elementContentId: this.data('container-id'),
            },
        }).then(function (response) {
            if (response.success) {
                row.remove();
                that.refreshAddButton();
                that.afterChange(response);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    ContainerItemsList.prototype.refreshAddButton = function () {
        const allowAddItem = this.data('allow-multiple') === 1 || !this.$.find('[data-item-id]').length;
        this.$.find('.cp-container-items-add').toggleClass('d-none', !allowAddItem);
    };

    ContainerItemsList.prototype.afterChange = function (response) {
        // Update the page behind the dialog and the structure view
        $('[data-editor-container-id="' + this.data('container-id') + '"]').replaceWith(response.output);
        const structure = this.structure();
        if (structure) {
            structure.refresh();
        }
    };

    module.export = ContainerItemsList;
});
