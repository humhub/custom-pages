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
        items: [
            {tag: 'div', class: 'panel panel-default', style: 'background:#ededed; padding:5px; margin:-1rem'},
            {tag: 'div', class: 'panel-body', style: 'background:#fff; border-radius:4px; box-shadow:0 0 3px #dadada; padding:10px'}
        ],
        wrap: function (content) {
            return this.startHtml() + content + this.endHtml();
        },
        unwrap: function (content) {
            const regexp = new RegExp('^' + this.regexp('(.+?)') + '$', 'is');
            return content.replace(regexp, '$1')
        }
    };

    const onAction = (btn) => {
        const content = editor.getContent();
        if (wrapper.isActive()) {
            editor.setContent(wrapper.unwrap(content));
            btn.setActive(false);
        } else {
            editor.setContent(wrapper.wrap(content));
            btn.setActive(true);
        }
        editor.focus();
    }

    editor.ui.registry.addToggleButton('wrapper', {
        icon: 'unselected',
        text: config.text,
        tooltip: config.tooltip,
        onAction,
        onSetup: (btn) => {
            btn.setActive(wrapper.isActive());
            editor.on('NodeChange', () => {
                btn.setActive(wrapper.isActive());
                wrapper.cleanup();
            });
        }
    });

    wrapper.isActive = function () {
        const nodes = editor.dom.getRoot().childNodes;
        return nodes.length === 1 &&
            editor.dom.is(nodes[0], this.selector(0)) &&
            nodes[0].childNodes.length === 1 &&
            editor.dom.is(nodes[0].childNodes[0], this.selector(1));
    }

    wrapper.startTag = function (i) {
        if (typeof this.items[i] === 'undefined') {
            return '';
        }

        let html = '<' + this.items[i].tag;
        if (this.items[i].class) {
            html += ' class="' + this.items[i].class + '"';
        }
        html += '>';

        return html;
    }

    wrapper.endTag = function (i) {
        if (typeof this.items[i] === 'undefined') {
            return '';
        }

        return '</' + this.items[i].tag + '>';
    }

    wrapper.startHtml = function (separator) {
        if (typeof separator === 'undefined') {
            separator = '';
        }

        let html = '';
        for (let i = 0; i < this.items.length; i++) {
            html += this.startTag(i);
            if (i < this.items.length - 1) {
                html += separator;
            }
        }

        return html;
    }

    wrapper.endHtml = function (separator) {
        if (typeof separator === 'undefined') {
            separator = '';
        }

        let html = '';
        for (let i = this.items.length - 1; i >=0; i--) {
            html += this.endTag(i);
            if (i > 0) {
                html += separator;
            }
        }

        return html;
    }

    wrapper.selector = function (index) {
        const item = this.items[index];
        let selector = item.tag;
        if (item.class) {
            selector += '.' + item.class.replace(' ', '.');
        }
        return selector;
    }

    wrapper.regexp = function (separator) {
        return this.startHtml('.*?') + separator + this.endHtml('.*?');
    }

    wrapper.cleanup = function () {
        const content = editor.getContent();
        let cleanContent = content;
        const emptyLine = '<p>&nbsp;</p>';
        const emptyBody = this.startTag(1) + '&nbsp;' + this.endTag(1);
        const nl = '[\r\n]*';

        if (cleanContent.match(new RegExp(emptyLine, 'i'))) {
            const emptyLineRegexp = '(' + nl + emptyLine + nl + ')*';
            cleanContent = cleanContent.replace(new RegExp('^' + emptyLineRegexp + '(' + this.regexp('.+?') + ')' + emptyLineRegexp + '$', 'is'), '$2');
        }

        if (cleanContent.match(new RegExp(emptyBody, 'i'))) {
            cleanContent = cleanContent
                .replace(new RegExp(this.endTag(1) + nl + emptyBody, 'ig'), emptyLine + this.endTag(1))
                .replace(new RegExp(this.startTag(0) + nl + emptyBody, 'ig'), this.startHtml() + emptyLine);
        }

        if (cleanContent !== content) {
            editor.setContent(cleanContent);
        }
    }

    wrapper.initStyles = function () {
        let styles = ''
        let parentSelector = '';
        for (let i = 0; i < this.items.length; i++) {
            styles += parentSelector + this.selector(i) + ' {' + this.items[i].style + '}';
            parentSelector = this.selector(i) + ' ';
        }
        editor.contentStyles.push(styles);
    }

    wrapper.initStyles();
})