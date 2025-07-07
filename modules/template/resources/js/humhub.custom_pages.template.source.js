humhub.module('custom_pages.template.source', function (module, require, $) {
    var client = require('client');
    var modal = require('ui.modal');
    var Widget = require('ui.widget').Widget;
    var object = require('util').object;
    var status = require('ui.status');

    var TemplateSourceEditor = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(TemplateSourceEditor, Widget);

    TemplateSourceEditor.prototype.init = function () {
        this.$sourceInput = $('#template-form-source');
        this.$form = $('#sourceForm');
        this.$elements = $('#templateElementTable');
        this.initEvents();
    };

    TemplateSourceEditor.prototype.initEvents = function () {
        var that = this;

        this.codeMirror = CodeMirror.fromTextArea($("#template-form-source")[0], {
            lineNumbers: true,
            mode: "text/html",
            extraKeys: {"Ctrl-Space": "autocomplete"}
        });

        this.codeMirror.on('change', function () {
            console.log('changed');
            that.$sourceInput.data('changed', true);
        });

        // Fixes an issue in which the editor is not rendered correctly on init
        setTimeout(function () {
            that.codeMirror.refresh();
        }, 500);

        this.$form.on('submit', function () {
            that.$sourceInput.data('changed', false);
        });

        // Note some browser do not support custom messages for this event.
        $(window).on('beforeunload.custom_pages', function () {
            if (that.$sourceInput.data('changed')) {
                return module.text('warning.beforeunload');
            }
        });

        $(document).on('pjax:beforeSend', function (evt) {
            if (that.$sourceInput.data('changed') && !window.confirm(module.text('warning.beforeunload'))) {
                evt.preventDefault();
                return;
            }

            that.$sourceInput.data('changed', false);
        })

    };

    TemplateSourceEditor.prototype.editElementSubmit = function (evt) {
        this._updateInputValue();
        var that = this;
        client.submit(evt, {dataType: 'json'}).then(function (response) {
            that._destroyInput();
            if (response.success) {
                that.updateElement(response);
                modal.global.close();
                status.success(response.message);
            } else {
                modal.global.setDialog(response);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    TemplateSourceEditor.prototype.editMultipleElementsSubmit = function (evt) {
        this._updateInputValue();
        var that = this;
        client.submit(evt).then(function (response) {
            that._destroyInput();
            if (response.success) {
                that.$elements.replaceWith(response.output);
                modal.global.close();
                status.success(response.message);
            } else {
                modal.global.setDialog(response);
            }
        }).catch(function (e) {
            module.log.error(e);
        });
    };

    TemplateSourceEditor.prototype.deleteElementSubmit = function (evt) {
        var that = this;
        client.post(evt).then(function (response) {
            if (response.success) {
                that.removeElement(response.id);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    TemplateSourceEditor.prototype.removeElement = function (id) {
        $('[data-template-element-definition="' + id + '"]').fadeOut('fast', function () {
            $(this).remove();
            if ($('#templateElements tr').length === 0) {
                $('#templateElementTable thead').hide();
            }
        });
    };

    TemplateSourceEditor.prototype.updateElement = function (response) {
        var $currentRow = $('[data-template-element-definition="' + response.id + '"]');
        if (!$currentRow.length) {
            var $content = $(response.output).hide();
            $('#templateElementTable thead').show();
            $('#templateElements').append($content);
            $content.fadeIn('fast');

            if (response.name) {
                this.insertPlaceholder('{{ ' + response.name + ' }}');
            }
        } else {
            $currentRow.replaceWith(response.output);
        }
    };

    TemplateSourceEditor.prototype.insertPlaceholder = function (txt) {
        codeMirror = this.getCodeMirror();
        codeMirror.getDoc().replaceSelection(txt);
        codeMirror.save();
    };

    TemplateSourceEditor.prototype.getCodeMirror = function () {
        return $(".CodeMirror:visible")[0].CodeMirror;
    };

    TemplateSourceEditor.prototype.reset = function (evt) {
        var that = this;
        client.post(evt).then(function (response) {
            if (response.success) {
                that.updateElement(response);
                modal.global.close();
                status.success(response.message);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    TemplateSourceEditor.prototype._updateInputValue = function () {
        if (typeof tinyMCE === 'object' && typeof tinyMCE.triggerSave === 'function') {
            tinyMCE.triggerSave();
        }
    };

    TemplateSourceEditor.prototype._destroyInput = function () {
        if (typeof tinyMCE === 'object' && typeof tinyMCE.remove === 'function') {
            tinyMCE.remove();
        }
    };

    var _getCaret = function (el) {
        if (el.selectionStart) {
            return el.selectionStart;
        } else if (document.selection) {
            el.focus();

            var r = document.selection.createRange();
            if (r == null) {
                return 0;
            }

            var re = el.createTextRange();
            var rc = re.duplicate();
            re.moveToBookmark(r.getBookmark());
            rc.setEndPoint('EndToStart', re);

            return rc.text.length;
        }
        return 0;
    };

    module.initOnPjaxLoad = true;
    var init = function () {
        if ($('#templatePageRoot').length && require('ui.view').getState().action === 'edit-source') {
            module.editor = Widget.instance('#templatePageRoot');
            _initEvents();
        }
    };

    var _initEvents = function () {


        /*
         $(document).on('contentResetSuccess', function(evt, result) {
         
         });*/


    };

    var unload = function () {
        $(document).off('.custom_pages');
        $(window).off('.custom_pages');
        $(document).on('pjax:beforeSend');
    };

    module.export({
        init,
        unload,
        TemplateSourceEditor,
    });
});
