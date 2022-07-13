/**
 * plugin.js
 *
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 * @author Lucas Bartholemy <lucas@bartholemy.com>
 */

tinymce.PluginManager.add('humhubtrigger', function(editor, url) {
    const config = editor.getParam('humhubTrigger');

    const isEnabled = () => config.selector && $(config.selector).length;
    const humhubFileUploadWidget = () => isEnabled && $(config.selector).data('humhubFileUpload')
        ? $(config.selector).data('humhubFileUpload')
        : null;

    function callAttachFilesWindow() {
        if (isEnabled) {
            $(config.selector).trigger(config.event);
        }
    }

    const buttonParams = {
        icon: config.icon,
        text: config.text,
        onAction: callAttachFilesWindow
    };
    editor.ui.registry.addButton('humhubtrigger', buttonParams);
    editor.ui.registry.addMenuItem('humhubtrigger', buttonParams);

    if (humhubFileUploadWidget()) {
        humhubFileUploadWidget().on('humhub:file:uploadEnd', function (evt, response) {
            if (!(response._response.result.files instanceof Array) ||
                !response._response.result.files.length) {
                return;
            }
            editor.insertContent(getFileHtmlTags(response._response.result.files));
        });
    }

    function getFileHtmlTags(files) {
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
})