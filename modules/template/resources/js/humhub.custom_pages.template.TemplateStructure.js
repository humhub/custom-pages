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
        this.initDraggable();
        this.initHighlight();
    }

    TemplateStructure.prototype.initDraggable = function () {
        const that = this;
        const rootTemplateInstanceId = that.$.find('[data-template-type=layout]').data('template-instance-id');

        that.$.css(that.getStoredData()[rootTemplateInstanceId] ?? {
            top: $('#editPageButton').position().top,
            left: '20px',
        });

        this.$.draggable({
            handle: '.cp-ts-header',
            stop: function (e) {
                const data = that.getStoredData();
                data[rootTemplateInstanceId] = $(e.target).position();
                window.localStorage.setItem('cp-structure', JSON.stringify(data));
            }
        });
    }

    TemplateStructure.prototype.initHighlight = function () {
        this.$.on('mouseover', '.cp-ts-template', function () {
            const itemId = $(this).closest('[data-container-item-id]').data('container-item-id');
            $('.highlightStructure').removeClass('highlightStructure');
            $('[data-template-item=' + itemId + ']').addClass('highlightStructure');
            $('[data-template-element], [data-template-item]').each(function () {
                Widget.instance(this).deactivate();
            })
        }).on('mouseout', '.cp-ts-template', function () {
            $('.highlightStructure').removeClass('highlightStructure');
        });
    }

    TemplateStructure.prototype.getStoredData = function () {
        const data = window.localStorage.getItem('cp-structure');
        return data ? JSON.parse(data) : {};
    }

    TemplateStructure.prototype.addContainerItem = function (evt) {
        const container = evt.$target.closest('[data-element-id]');
        this.setCurrentEditorElement(container);
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
        const that = this;
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
                that.getParentContainer(container).replaceWith(response.output);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        }).finally(function() {
            loader.reset(container);
        });
    }

    TemplateStructure.prototype.getParentContainer = function (container) {
        return $('[data-ui-widget="custom_pages.template.TemplateContainer"][data-template-element-content-id=' + container.data('element-content-id') + ']')
    }

    TemplateStructure.prototype.moveUpContainerItem = function (evt) {
        this.moveContainerItem(evt, 'up')
    }

    TemplateStructure.prototype.moveDownContainerItem = function (evt) {
        this.moveContainerItem(evt, 'down')
    }

    TemplateStructure.prototype.deleteContainerItem = function (evt) {
        const that = this;
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
                that.getParentContainer(containerItem).replaceWith(response.output);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    }

    TemplateStructure.prototype.setCurrentEditorElement = function (container) {
        const editor = Widget.instance('[data-ui-widget="custom_pages.template.editor.TemplateInlineEditor"]');
        if (editor) {
            editor.currentElement = editor.getElement($('[data-ui-widget="custom_pages.template.TemplateContainer"][data-template-element-content-id=' + container.data('element-content-id') + ']'));
        }
    }

    module.export = TemplateStructure;
});
