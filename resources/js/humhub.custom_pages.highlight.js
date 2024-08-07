humhub.module('custom_pages.highlight', function (module, require, $) {
    const highlightWords = require('ui.additions').highlightWords;

    const init = function () {
        $(document).ready(function () {
            if (typeof module.config.highlight === 'string') {
                highlightWords('#layout-content', module.config.highlight);
            }
        });
    }

    module.export({
        init,
        initOnPjaxLoad: true,
    })
})
