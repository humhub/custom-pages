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

    TemplateStructure.prototype.editor = function () {
        if (typeof(this._editor) === 'undefined') {
            this._editor = Widget.instance('[data-ui-widget="custom_pages.template.editor.TemplateInlineEditor"]');
        }
        return this._editor;
    }

    TemplateStructure.prototype.initDraggable = function () {
        const that = this;
        const rootTemplateInstanceId = that.$.find('[data-template-type=layout]').data('template-instance-id');

        that.$.css(that.getStoredData()[rootTemplateInstanceId] ?? {
            top: $('#editPageButton').position().top,
            left: '20px',
        }).show();

        this.$.draggable({
            handle: '.cp-structure-header',
            stop: function (e) {
                const data = that.getStoredData();
                data[rootTemplateInstanceId] = $(e.target).position();
                window.localStorage.setItem('cp-structure', JSON.stringify(data));
            }
        });
    }

    TemplateStructure.prototype.initHighlight = function () {
        const activeClass = 'cp-editor-container-active';
        this.$.on('mouseover', '.cp-structure-template, .cp-structure-container', function () {
            const obj = $(this).hasClass('cp-structure-container')
                ? $('[data-editor-container-id=' + $(this).closest('[data-container-id]').data('container-id') + ']')
                : $('[data-editor-container-item-id=' + $(this).closest('[data-container-item-id]').data('container-item-id') + ']');
            $('.' + activeClass).removeClass(activeClass);
            obj.addClass(activeClass).parents('[data-editor-container-item-id]').addClass(activeClass);
        }).on('mouseout', '.cp-structure-template, .cp-structure-container', function () {
            $('.' + activeClass).removeClass(activeClass);
        });
    }

    TemplateStructure.prototype.getStoredData = function () {
        const data = window.localStorage.getItem('cp-structure');
        return data ? JSON.parse(data) : {};
    }

    TemplateStructure.prototype.addContainerItem = function (evt) {
        const container = evt.$target.closest('[data-element-id]');
        this.setCurrent(container);
        modal.load(evt, {
            url:  container.data('default') !== undefined ? this.data('create-container-url') : this.data('item-add-url'),
            dataType: 'json',
            data: {
                templateInstanceId: container.closest('[data-template-instance-id]').data('template-instance-id'),
                elementId: container.data('element-id'),
                elementContentId: container.data('container-id'),
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
                elementContentId: container.data('container-id'),
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
                that.getEditorContainer(container).replaceWith(response.output);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        }).finally(function() {
            loader.reset(container);
        });
    }

    TemplateStructure.prototype.getEditorContainer = function (container) {
        return $('[data-editor-container-id=' + container.data('container-id') + ']')
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
                elementContentId: containerItem.data('container-id'),
            }
        };

        client.post(evt, options).then(function (response) {
            if (response.success) {
                containerItem.fadeOut('fast', () => containerItem.remove());
                that.getEditorContainer(containerItem).replaceWith(response.output);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    }

    TemplateStructure.prototype.setCurrent = function (container) {
        this.current = container;
        const editor = this.editor();
        editor.current = editor.getElement($('[data-editor-container-id=' + container.data('container-id') + ']'));
    }

    TemplateStructure.prototype.appendContainerItem = function (containerId, item) {
        this.current
            .data('container-id', containerId)
            .attr('data-container-id', containerId)
            .removeAttr('data-default')
            .append(item);
    }

    module.export = TemplateStructure;
});
