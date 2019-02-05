humhub.module('custom_pages.template.source', function (module, require, $) {
    var client = require('client');
    var modal = require('ui.modal');
    var Widget = require('ui.widget').Widget;
    var object = require('util').object;

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
        this.$sourceInput.on('change', function () {
            that.$sourceInput.data('changed', true);
        });

        this.$form.on('submit', function () {
            that.$sourceInput.data('changed', false);
        });

        // Note some browser do not support custom messages for this event.
        $(window).on('beforeunload.custom_pages', function () {
            if (that.$sourceInput.data('changed')) {
                return module.text('warning.beforeunload');
            }
        });

        $(document).on('keydown.custom_pages', '#template-form-source', function (e) {
            var keyCode = e.keyCode || e.which;

            if (keyCode === 9) {
                e.preventDefault();

                var $this = $(this);
                var start = $this.get(0).selectionStart;
                var end = $this.get(0).selectionEnd;

                // set textarea value to: text before caret + tab + text after caret
                $(this).val($(this).val().substring(0, start)
                        + "\t"
                        + $this.val().substring(end));

                // put caret at right position again
                $this.get(0).selectionStart = $this.get(0).selectionEnd = start + 1;
            }
        });
    };

    TemplateSourceEditor.prototype.editElementSubmit = function (evt) {
        var that = this;
        client.submit(evt, {dataType: 'json'}).then(function (response) {
            if (response.success) {
                that.updateElement(response);
                modal.global.close();
            } else {
                modal.global.setDialog(response);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    TemplateSourceEditor.prototype.editMultipleElementsSubmit = function (evt) {
        var that = this;
        client.submit(evt).then(function (response) {
            if (response.success) {
                that.$elements.replaceWith(response.output);
                modal.global.close();
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
        });
    };

    TemplateSourceEditor.prototype.updateElement = function (response) {
        var $currentRow = $('[data-template-element-definition="' + response.id + '"]');
        if (!$currentRow.length) {
            var $content = $(response.output).hide();
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
        var textarea = this.$sourceInput[0];
        var currentPos = _getCaret(textarea);
        var strLeft = textarea.value.substring(0, currentPos);
        var strRight = textarea.value.substring(currentPos, textarea.value.length);
        textarea.value = strLeft + txt + strRight;
        $(textarea).trigger('change');
    };

    TemplateSourceEditor.prototype.reset = function (evt) {
        client.post(evt).then(function(response) {
            if (response.success) {
                this.updateElement(response);
                modal.global.close();
            }
        }).catch(function(e) {
            module.log.error(e, true);
        });
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

    var TemplateSourcePreview = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(TemplateSourcePreview, Widget);

    TemplateSourcePreview.prototype.update = function (evt) {
        var options = {
            data: {
                reload: 1,
                editView: $('#editModePreview').is(':visible') ? '1' : '0'
            }
        };

        var that = this;
        client.html(evt, options).then(function (response) {
            var $result = $(response.html);
            $result.find('#stage').hide();
            that.$.replaceWith($result);
            $result.find('#stage').fadeIn('fast');
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    TemplateSourcePreview.prototype.switchMode = function (evt) {
        evt.$trigger.toggleClass('active');
        $('#nonEditModePreview, #editModePreview').toggle();
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
    };

    module.export({
        init: init,
        unload: unload,
        TemplateSourceEditor: TemplateSourceEditor,
        TemplateSourcePreview: TemplateSourcePreview
    });
});