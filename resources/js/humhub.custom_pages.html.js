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
            const htmlTinyMce = tinyMCE.get('html_content');
            if (!htmlTinyMce ||
                !(response._response.result.files instanceof Array) ||
                !response._response.result.files.length) {
                return;
            }

            htmlTinyMce.insertContent(getFileHtmlTags(response._response.result.files));
        });
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