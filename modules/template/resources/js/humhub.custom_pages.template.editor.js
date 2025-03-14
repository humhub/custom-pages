humhub.module('custom_pages.template.editor', function (module, require, $) {
    const Widget = require('ui.widget').Widget;
    const object = require('util').object;
    const client = require('client');
    const modal = require('ui.modal');
    const additions = require('ui.additions');

    TemplateInlineEditor = function (node, options) {
        Widget.call(this, node, options);
        additions.observe(this.$);
    };

    object.inherits(TemplateInlineEditor, Widget);

    TemplateInlineEditor.prototype.init = function () {
        this.initHighlight();
    };

    TemplateInlineEditor.prototype.initHighlight = function () {
        const activeClass = 'cp-structure-active';
        this.$.on('mouseover', '[data-editor-container-item-id]', function () {
            const item = $('.cp-structure [data-container-item-id=' + $(this).data('editor-container-item-id') + '] > li > .cp-structure-row');
            $('.' + activeClass).removeClass(activeClass);
            item.addClass(activeClass);
        }).on('mouseout', '[data-editor-container-item-id]', function () {
            $('.' + activeClass).removeClass(activeClass);
        });
    }

    TemplateInlineEditor.prototype.editItemSubmit = function (evt) {
        const that = this;
        that._updateInputValue();
        that._removeDisabledFields(evt.$form);

        client.submit(evt, {dataType: 'json'}).then(function (response) {
            that._destroyInput();
            if (response.success) {
                const $result = $(response.output);
                if ($result.is('[data-template-item-id]')) {
                    // called for normal edit actions
                    const itemId = $result.data('template-item-id');
                    that.replaceElement(that.getItemById(itemId), $result);
                } else {
                    // called for addItem actions where currentElement is the container
                    that.replaceElement(that.current, $result);
                    that.structure().appendContainerItem($result.data('editor-container-id'), $(response.structure));
                }
                modal.global.close();
                additions.applyTo(that.$);
            } else {
                modal.global.setDialog(response);
            }
        });
    };

    TemplateInlineEditor.prototype._removeDisabledFields = function ($form) {
        // Remove disabled items, before submit, otherwise they are submitted empty.
        $form.find(':disabled').each(function () {
            $form.find('[name="' + $(this).attr('name') + '"]').remove();
        });
    };

    TemplateInlineEditor.prototype.editMultipleElementsSubmit = function (evt) {
        const that = this;
        that._updateInputValue();
        that._removeDisabledFields(evt.$form);

        client.submit(evt, {dataType: 'json'}).then(function (response) {
            that._destroyInput();
            if (response.success) {
                client.reload();
            } else {
                modal.global.setDialog(response);
            }
        });
    };

    TemplateInlineEditor.prototype.getItemById = function (id) {
        return this.getElement($('[data-template-item-id="' + id + '"]'));
    };

    TemplateInlineEditor.prototype._updateInputValue = function () {
        if (typeof tinyMCE === 'object' && typeof tinyMCE.triggerSave === 'function') {
            tinyMCE.triggerSave();
        }
    };

    TemplateInlineEditor.prototype._destroyInput = function () {
        if (typeof tinyMCE === 'object' && typeof tinyMCE.remove === 'function') {
            tinyMCE.remove();
        }
    };

    TemplateInlineEditor.prototype.replaceElement = function (element, content) {
        element.$.replaceWith($(content));
    };

    TemplateInlineEditor.prototype.getElement = function ($elem) {
        return Widget.instance($elem);
    };

    TemplateInlineEditor.prototype.structure = function () {
        if (typeof(this._structure) === 'undefined') {
            this._structure = Widget.instance('[data-ui-widget="custom_pages.template.TemplateStructure"]');
        }
        return this._structure;
    };

    const init = function () {
        if ($('#templatePageRoot').length && require('ui.view').getState().action !== 'edit-source') {
            module.editor = Widget.instance('#templatePageRoot');
        }
    };

    module.export({
        initOnPjaxLoad: true,
        init,
        TemplateInlineEditor,
    });
});
