/*
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2021 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */
humhub.module('custom_pages.html', function (module, require, $) {
    const Widget = require('ui.widget').Widget;

    const init = function () {
        if (!$('#custom-page-html-file-upload').length) {
            return;
        }

        Widget.instance('#custom-page-html-file-upload').on('humhub:file:uploadEnd', function (evt, response) {
            if (!(htmlContentCodeMirror instanceof CodeMirror) ||
                !(response._response.result.files instanceof Array) ||
                !response._response.result.files.length) {
                return;
            }

            insertTextAtCursor(htmlContentCodeMirror, getFileHtmlTags(response._response.result.files));
        });
    }

    const insertTextAtCursor = function (codeMirror, text) {
        const valueLines = codeMirror.getValue().split('\n');
        const cursor = codeMirror.getCursor();
        const cursorLine = valueLines[cursor.line];
        const cursorLineBeforeCursor = cursorLine.substring(0, cursor.ch);
        const cursorLineAfterCursor  = cursorLine.substring(cursor.ch, cursorLine.length);

        valueLines[cursor.line] = cursorLineBeforeCursor + text + cursorLineAfterCursor;

        codeMirror.setValue(valueLines.join('\n'));
    }

    const getFileHtmlTags = function (files) {
        let htmlTags = '\n';
        files.forEach(function (file) {
            if (typeof(file.url) === 'undefined' || typeof(file.mimeType) === 'undefined') {
                return;
            }

            if (file.mimeType.indexOf('image/') === 0) {
                htmlTags += '<img src="' + file.url + '" class="img-responsive">';
            } else {
                htmlTags += '<a href="' + file.url + '" target="_blank">' + (typeof(file.name) === 'undefined' ? file.url : file.name) + '</a>';
            }

            htmlTags += '\n';
        });

        return htmlTags;
    }

    module.export({
        init,
    });
});