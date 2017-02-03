humhub.module('custom_pages', function(module, require, $) {
    var unload = function() {
        $('.editMenu, .elementMenu').remove();
    };
    
    module.export({
        unload: unload
    });
});