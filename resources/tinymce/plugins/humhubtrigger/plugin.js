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

    function callAttachFilesWindow() {
        if (config.selector && $(config.selector).length) {
            $(config.selector).trigger(config.event);
        }
    }

    editor.ui.registry.addButton('humhubtrigger', {
        icon: config.icon,
        text: config.text,
        onAction: callAttachFilesWindow
    })
})