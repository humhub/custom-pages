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
        this.initEditableRows();
        this.initHighlight();
        this.initMenuAlignment();
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

    TemplateStructure.prototype.initEditableRows = function () {
        const that = this;
        this.$.on('click', '.cp-structure-template', function (evt) {
            evt.$target = $(evt.target);
            if (evt.$target.is('.cp-structure-template, .cp-structure-text, .fa-circle')) {
                evt.$trigger = $(this);
                that.editElements(evt);
            }
        });
    }

    TemplateStructure.prototype.initHighlight = function () {
        const activeClass = 'cp-editor-container-active';
        this.$.on('mouseenter', '.cp-structure-template, .cp-structure-container', function () {
            const obj = $(this).hasClass('cp-structure-container')
                ? $('[data-editor-container-id=' + $(this).closest('[data-container-id]').data('container-id') + ']')
                : $('[data-editor-container-item-id=' + $(this).closest('[data-container-item-id]').data('container-item-id') + ']');
            if (!obj.length || ['STYLE', 'SCRIPT'].includes(obj[0].tagName)) {
                return;
            }

            const copy = obj.clone();
            copy.addClass(activeClass).css({
                width: obj.outerWidth(),
                minHeight: obj.outerHeight() > 2 ? obj.outerHeight() : 2,
                top: obj.position().top,
                left: obj.position().left,
            });

            $('.cp-structure-overlay, .' + activeClass).remove();
            $('body').append('<div class="cp-structure-overlay"></div>');
            obj.after(copy);
            copy.fadeIn('fast');
        }).on('mouseleave', '.cp-structure-template, .cp-structure-container', function () {
            $('.cp-structure-overlay, .' + activeClass).remove();
        });
    }

    TemplateStructure.prototype.initMenuAlignment = function () {
        const that = this;
        that.$.on('mouseenter', '.dropdown-toggle', function () {
            const menu = $(this).next();
            $(this).parent().position().top + menu.outerHeight() + 10 > that.$.outerHeight()
                ? menu.addClass('dropdown-menu-top')
                : menu.removeClass('dropdown-menu-top');
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
                that.getEditorContainer(containerItem).replaceWith(response.output);
                containerItem.fadeOut('fast', () => {
                    containerItem.remove();
                    that.refreshAddItemButton(containerItem.data('container-id'));
                });
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
        this.refreshAddItemButton(containerId);
    }

    TemplateStructure.prototype.refreshAddItemButton = function (containerId) {
        const container = this.$.find('[data-container-id=' + containerId + ']');
        const allowAddItem = container.data('allow-multiple') === 1 || !container.find('[data-container-item-id]').length;
        container.find('> .cp-structure-container [data-action-click=addContainerItem]').toggle(allowAddItem);
        $('[data-actions-container-id], [data-actions-container-item-id]').remove();
    }

    module.export = TemplateStructure;
});
