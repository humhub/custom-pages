/**
 * plugin.js
 *
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 * @author Lucas Bartholemy <lucas@bartholemy.com>
 */

tinymce.PluginManager.add('wrapper', function(editor, url) {
    const config = editor.getParam('wrapper');
    const wrapper = {
        start: '<div class="panel panel-default"><div class="panel-body">',
        end: '</div></div>'
    };

    editor.contentStyles.push('.panel.panel-default {background:#ededed; padding:5px; margin:-1rem}' +
        '.panel .panel-body {background:#fff; border-radius:4px; box-shadow:0 0 3px #dadada; padding:10px}');

    const isWrapped = () => {
        const nodes = editor.dom.getRoot().childNodes;
        return nodes.length === 1 &&
            editor.dom.is(nodes[0], 'div.panel.panel-default') &&
            nodes[0].childNodes.length === 1 &&
            editor.dom.is(nodes[0].childNodes[0], 'div.panel-body');
    }

    const onAction = (btn) => {
        const content = editor.getContent();
        if (isWrapped()) {
            editor.setContent(content.replace(/^<div class="panel panel-default">.*?<div class="panel-body">(.+?)<\/div>.*?<\/div>$/is, '$1'));
            btn.setActive(false);
        } else {
            editor.setContent(wrapper.start + content + wrapper.end);
            btn.setActive(true);
        }
    }

    editor.ui.registry.addToggleButton('wrapper', {
        icon: 'unselected',
        text: config.text,
        tooltip: config.tooltip,
        onAction,
        onSetup: (btn) => {btn.setActive(isWrapped())}
    })
})