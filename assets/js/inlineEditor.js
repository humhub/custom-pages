/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */
(function() {
    var cfg = editConfig;
    
    var inherits = function (Sub, Parent) {
        Sub.prototype = Object.create(Parent.prototype);
        Sub._super = Parent.prototype;
    };

    /**
     * Template Element
     */
    var TemplateElement = function ($elem) {
        this.$ = $elem;
        this.$.data('element', this);

        this.$root = cfg.$templatePageRoot;

        this.id = this.getUniqueId($elem);
        this.editUrl = cfg.elementEditUrl;
        this.deleteUrl = cfg.elementDeleteUrl;

        this.name = this.data('template-element');
        this.content = this.data('template-content');
        this.contentId = this.data('template-content-id');
        this.owner = this.data('template-owner');
        this.ownerId = this.data('template-owner-id');
        this.empty = this.data('template-empty');
        this.default = this.data('template-default');
        this.ownerContentId = this.data('template-owner-content-id');

    };

    TemplateElement.prototype.isEqual = function(element) {
        return this.$[0] === element.$[0];
    }

    TemplateElement.prototype.isActive = function() {
        return this.data('active') == true;
    };

    TemplateElement.prototype.isParentOf = function(element) {
        var parent = element.getParent();
        return parent && parent.id === this.id;
    };

    TemplateElement.prototype.highlight = function () {
        this.$.addClass((this.empty) ? 'highlightAdd' : 'highlightEdit');
    };

    TemplateElement.prototype.getParent = function () {
        var $parent = this.$.parent().closest('[data-template-element], [data-template-item]');

        if($parent.length) {
            return editPage.getElement($parent);
        }
    };

    TemplateElement.prototype.getParentElement = function () {
        var $parent = this.$.parent().closest('[data-template-element]');

        if($parent.length) {
            return editPage.getElement($parent);
        }
    };

    TemplateElement.prototype.isPartOfInlineEdit = function () {
        return !editPage.activeItem || editPage.activeItem.isParentOf(this);
    };

    TemplateElement.prototype.activate = function (prevenParentActivation) {
        if(!prevenParentActivation) {
            var parent = this.getParentElement();

            if(parent) {
                if(!parent.isActive() && parent.isPartOfInlineEdit()) {
                    parent.activate(true);
                    editPage.activeElements.push(parent);
                }
            }
        }

        if (this.isActive()) {
            return;
        }

        this.highlight();
        this.data('active', true);
        this.renderMenu();
    };

    TemplateElement.prototype.renderMenu = function () {
        var items = [this.createEditButton()];

        if(!this.default) {
            items.push(this.createDeleteButton());
        }
        this.renderMenuItems({
             items: items
        });
    };

    TemplateElement.prototype.createEditButton = function (size) {
        var btnSizeClass;
        switch(size) {
            case 'small':
                btnSizeClass = 'btn-xs';
                break;
            default:
                btnSizeClass = 'btn-sm';
                break;
        }

        var buttonHtml = '<a class="template-menu-button btn btn-primary '+btnSizeClass+' tt" href="#"><i class="fa fa-pencil"></i></a>';

        var that = this;

        return $(buttonHtml).on('click', function (evt) {
            evt.preventDefault();
            $.ajax(that.editUrl, {
                dataType: 'json',
                data: that.getEditData(),
                beforeSend: function () {
                    setModalLoader();
                    $('#globalModal').modal('show');
                    editPage.currentElement = that;
                },
                success: function (json) {
                    $('#globalModal').html(json.content);
                }
            });
        });
    };

    TemplateElement.prototype.getEditData = function() {
        return {
            ownerModel: this.owner,
            ownerId: this.ownerId,
            name: this.name
        };
    };

    TemplateElement.prototype.createDeleteButton = function (size) {
        var btnSizeClass;
        switch(size) {
            case 'small':
                btnSizeClass = 'btn-xs';
                break;
            default:
                btnSizeClass = 'btn-sm';
                break;
        }

        var buttonHtml = '<a class="template-menu-button btn btn-danger '+btnSizeClass+' tt" href="#"><i class="fa fa-times"></i></a>';

        var that = this;

        return $(buttonHtml).on('click', function (evt) {
            evt.preventDefault();
            $.ajax(that.deleteUrl, {
                dataType: 'json',
                data: that.getEditData(),
                beforeSend: function () {
                    setModalLoader();
                    $('#globalModal').modal('show');
                    editPage.currentElement = that;
                },
                success: function (json) {
                    $('#globalModal').html(json.content);
                    $('#globalModal').modal('show');
                }
            });
        });
    };

    TemplateElement.prototype.renderMenuItems = function (options) {
        var that = this;
        if (this.$menu) {
            that.$menu.remove();
        }

        options.cssClass = options.cssClass || 'elementMenu';
        this.$menu = (!this.$menu) ? $('<div class="editMenu ' + options.cssClass + '" style="display:none;"></div>') : this.$menu;

        if(this.name) {
            this.$menu.append($('<span>#' + this.name + '</span>'), options.items);
        } else {
            this.$menu.append(options.items);
        }

        this.$menu.on('mouseover', function(evt) {
            evt.stopPropagation();
            if($('#overlay').length) {
                that.$menu.css('z-index', '1029');
            } else {
                that.$menu.css('z-index', '1');
            }
        });
        
        this.$menu.on('click', function(evt) {
            evt.stopPropagation();
        });

        this.$menu.on('mouseout', function(evt) {
            if($('#overlay').length) {
                that.$menu.css('z-index', '1028');
            } else {
                that.$menu.css('z-index', '');
            }
        });
        
        if($('#overlay').length) {
                that.$menu.css('z-index', '1028');
            } 

        $('body').append(this.$menu);

        if(options.afterInsert) {
            options.afterInsert.call();
        }

        var position = (options.position) ? options.position : 'rt';
        
        var offset = this.$.offset();
        
        var left = position.indexOf('l') === 0;
        var top = position.indexOf('t') === 1;
        
        if(offset.top <= this.$root.offset().top + 5) {
            top = 0;
        }
        
        var offsetTopAlign = options.topAlign || 0;
        var offsetLeftAlign = options.leftAlign || 0;
        
        if(this.$.is('span, a')) {
            options.outside = true;
        }
        
        if(top) {
            offsetTopAlign += (options.outside) ? - (this.$menu.height()) : 5;
        } else {
            offsetTopAlign += that.$.outerHeight() - that.$menu.outerHeight();
            offsetTopAlign += (options.outside) ? (this.$menu.height()) : -5;
        }
        
        if(left) {
            offsetLeftAlign += (options.outside) ? 0 : +5;
        } else {
            offsetLeftAlign += that.$.outerWidth() - that.$menu.outerWidth();
            offsetLeftAlign += (options.outside) ? 0 : -5;
        }

        this.$menu.css({
            'top': offset.top + offsetTopAlign,
            'left': offset.left + offsetLeftAlign
        });

        if(options.beforeShow) {
            options.beforeShow.call();
        }

        this.$menu.fadeIn('fast');
    };

    TemplateElement.prototype.deactivate = function () {
        if (this.$menu) {
            this.$menu.remove();
            this.$menu = undefined;
        }

        this.data('active', false);

        this.$.removeClass('highlightAdd').removeClass('highlightEdit');
    };

    TemplateElement.prototype.data = function(key, value) {
        if(typeof value !== 'undefined') {
            return this.$.data(key, value);
        } else {
            return this.$.data(key);
        }
    };

    TemplateElement.prototype.getUniqueId = function($element) {
        if (!$element) {
            return;
        }

        return $element.data('template-empty') ? Date.now().toString() :
                $element.data('template-content') + ':' + $element.data('template-content-id');
    };

    /**
     * TemplateContainerElement
     *  
     * @param {type} $elem
     * @returns {template_L2.TemplateContainerItem}         
     * */
    TemplateContainerElement = function ($elem) {
        TemplateElement.call(this, $elem);
        this.isContainer = true;
        this.multiple = this.data('template-multiple');
        this.templateId = this.data('template-id');
        this.inline = this.$.hasClass('inline');
    };

    inherits(TemplateContainerElement, TemplateElement);

    TemplateContainerElement.prototype.renderMenu = function() {
        var items = [];

        if(this.multiple || !this.hasItems()) {
            items.push(this.createAddItemButton());
        }

        this.renderMenuItems({
            items: items,
            outside : true
        });
    };

    TemplateContainerElement.prototype.hasItems = function() {
        return this.$.find('[data-template-item]').length !== 0;
    };

    TemplateContainerElement.prototype.createAddItemButton = function () {
        var data;
        var that = this;

        var buttonHtml = '<a class="template-menu-button btn btn-success btn-sm tt" href="#"><i class="fa fa-plus"></i></a>';

        var url = (this.default) ? cfg.createContainerUrl : cfg.itemAddUrl;

        if(this.default) {
            data = {
                ownerModel: that.owner, 
                ownerId: that.ownerId, 
                ownerContentId: that.ownerContentId
            };
        } else {
            data = {
                ownerContentId: that.ownerContentId
            };
        }

        return $(buttonHtml).on('click', function (evt) {
            evt.preventDefault();
            $.ajax(url, {
                method: 'GET',
                dataType: 'json',
                data: data,
                beforeSend: function () {
                    setModalLoader();
                     $('#globalModal').modal('show');
                    editPage.currentElement = that;
                },
                success: function (json) {
                    if(!json.success) {
                        $('#globalModal').html(json.content);
                    } else {
                        $(document).trigger('addItemSuccess', [json]);
                    }
                }
            });
        });
    };

    /**
     * TemplateContainerItem
     * 
     * @param {type} $elem
     * @returns {template_L2.TemplateContainerItem}
     */
    TemplateContainerItem = function ($elem) {
        TemplateElement.call(this, $elem);
        this.itemId = this.data('template-item');
        this.index = this.$.index();
        this.parent = this.getParent();
        this.name = this.data('template-item-title');
        this.inline = this.$.hasClass('inline');
        if(!this.name) {
            this.name = this.getParent().name+':'+this.index;
        }

        this.editTemplateUrl = this.data('template-edit-url');
        this.editUrl = cfg.itemEditUrl;
        this.deleteUrl = cfg.itemDeleteUrl;
        this.isContainerItem = true;
    };

    inherits(TemplateContainerItem, TemplateElement);

    TemplateContainerItem.prototype.isFirst = function () {
        return this.index == 0;
    }

    TemplateContainerItem.prototype.isLast = function () {
        return (this.index + 1) == this.getParent().$.children().length;
    }

    TemplateContainerItem.prototype.renderMenu = function() {
        if(this.$menu) {
            this.$menu.remove();
        }

        var that = this;
        var items = [this.createContainerToggle()];

        if(!this.isFirst()) {
            items.push(this.createMoveButton(-1));
        }

        if(!this.isLast()) {
            items.push(this.createMoveButton(1));
        }

        items.push(this.createEditButton('small'));
        items.push(this.createDeleteButton('small'));

        this.renderMenuItems({
            items: items ,
            'cssClass': 'elementToggleMenu',
            'position' : 'lt',
            outside : true,
            leftAlign: -13,
            
            'afterInsert': function() {
                $containerEditToggle = that.$menu.find('#containerEditToggle');
                $containerEditToggle.bootstrapSwitch({
                    'size': 'mini',
                    'state': that.data('isActiveItem'),
                    'onText': cfg.toggleOnText,
                    'offText': cfg.toggleOffText
                });
            },
            beforeShow: function() {
                that.$menu.css('left', '+=13px');
            }
        });
    };

    TemplateContainerItem.prototype.createMoveButton = function (step) {
        var that = this;
        var iconClass;
        
        if(this.inline) {
            iconClass = (step > 0) ? 'fa-caret-right' : 'fa-caret-left';
        } else {
            iconClass = (step > 0) ? 'fa-caret-down' : 'fa-caret-up';
        }
        
        var buttonHtml = '<a class="btn btn-success btn-xs tt template-menu-button" href="#"><i class="fa '+iconClass+'"></i></a>';

        var parent = that.getParent();

        var data = {
            ownerContentId: parent.ownerContentId,
            itemId: that.itemId, 
            step: step
        };

        return $(buttonHtml).on('click', function (evt) {
            evt.preventDefault();
            $.ajax(cfg.itemMoveUrl, {
                method: 'GET',
                dataType: 'json',
                data: data,
                success: function (json) {
                    if(json.success) {
                        that.deactivate();
                        parent.$.replaceWith(json.content);
                        parent.highlight();
                    }
                }
            });
        });
    };

    TemplateContainerItem.prototype.createEditTemplateButton = function () {
        return $('<a class="btn btn-primary btn-xs tt template-menu-button" target="_blank" href="'+this.editTemplateUrl+'">'+cfg.editTemplateText+'</a>');
    };

    TemplateContainerItem.prototype.getEditData = function() {
        var that = this;
        return {
            itemId: that.itemId,
            ownerContentId: that.getParentElement().ownerContentId
        };
    };

    TemplateContainerItem.prototype.startInlineEdit = function () {
        if ($('#overlay').length) {
            $('#overlay').remove();
        }

        $('.editMenu').css('z-index', '1028');
        this.$.css('background-color', '#fff');
        this.$.css('z-index', '1027');
        $('<div id="overlay" style="display:none;"></div>').insertBefore(this.$).fadeIn('fast');

        this.data('isActiveItem', true);
        this.data('active',true);
        this.$root.trigger('custom_pages.afterActivateContainer', [this]);
    };

    TemplateContainerItem.prototype.stopInlineEdit = function () {
        $('.editMenu').css('z-index', '');
        this.data('isActiveItem', false);
        var that = this;
        var $overlay = $('#overlay');
        if ($overlay.length) {
            $overlay.fadeOut('fast', function () {
                $overlay.remove();
                that.$root.trigger('custom_pages.afterDeactivateContainer', [that]);
            });
        }
        this.$.css('background-color', '');
        this.$.css('z-index', '');
    };

    TemplateContainerItem.prototype.getUniqueId = function ($element) {
        if (!$element) {
            return;
        }

        return 'templateContainerItem' + ':' + $element.data('template-item');
    };

    TemplateContainerItem.prototype.createContainerToggle = function () {
        var that = this;
        return $('<input id="containerEditToggle" type="checkbox" />')
                .on('switchChange.bootstrapSwitch', function (event, state) {
                    if (state) {
                        that.startInlineEdit();
                    } else {
                        that.stopInlineEdit();
                    }
        });
    };

    /**
     * Template Edit Page
     */
    TemplateInlineEdit = function () {
        this.$ = cfg.$templatePageRoot;

        this.activeElements = [];
        this.init();
    };
    TemplateInlineEdit.prototype.init = function () {
        this.initEvents();
    };

    TemplateInlineEdit.prototype.initEvents = function () {
        var that = this;
        this.$.on('mouseover', '[data-template-element], [data-template-item]', function (evt) {
            if(that.setActivateElement($(this))) {
                evt.stopPropagation();
            }
        });
        
        this.$.on('click', '[data-template-element], [data-template-item]', function (evt) {
            evt.preventDefault();
            evt.stopPropagation();
        });

        this.$.on('custom_pages.afterActivateContainer', function(event, item) {
            that.activeItem = item;
            that.setActivateElement(item.$);
            that.clearExcept(item);
        });

        this.$.on('custom_pages.afterDeactivateContainer', function(event, item) {
            that.activeItem = undefined;
            if(item.getParent()) {
                that.setActivateElement(item.getParent().$);
            }
        });

        $(document).on('itemDeleteSuccess', function (evt, json) {
            $('#globalModal').modal('hide');
            that.currentElement.deactivate();
            if(that.isActiveItem(that.currentElement)) {
                that.currentElement.stopInlineEdit();
            }
            that.replaceElement(that.currentElement.getParent(), json.content);
        });

        $(document).on('templateElementEditSuccess templateElementDeleteSuccess', function (evt, json) {
            $('#globalModal').modal('hide');
            that.replaceElement(that.currentElement, json.content);
        });
        
        /*$('a, button').on('click', function() {
            if(that.$.find($(this)) && !$(this).closest('editMenu').length && !$(this).closest('.modal').length) {
                that.clearExcept();
            }
        });*/
    };

    TemplateInlineEdit.prototype.replaceElement = function (element, content) {
        var $content = $(content);
        element.$.replaceWith($content);
        var newElement = this.getElement($content);
        
        if(this.isActiveItem(element)) {
            this.activeItem = undefined;
            this.clearExcept();
            newElement.data('isActiveItem', true);
            this.setActivateElement($content);
            newElement.startInlineEdit(true);
        }
        
    };

    TemplateInlineEdit.prototype.clearExcept = function(element) {
        $.each(this.activeElements, function(index, active) {
            if(!element || !active.isEqual(element)) {
                active.deactivate();
            }
        });

        this.activeEllements = [element];
    };

    TemplateInlineEdit.prototype.setActivateElement = function ($element) {
        var element = this.getElement($element);

        if(element.isActive()) {
            this.checkCurrentActivation(element);
            return true;
        }

        if(this.activeItem && !this.activeItem.isParentOf(element) && (!element.itemId || !this.activeItem.$.find(element.$).length)) {
            return false;
        } 

        var hasRootOwner = element.owner === "humhub\\modules\\custom_pages\\modules\\template\\models\\TemplateInstance";
        var isActiveCotnainerItemContent = this.activeItem && this.activeItem.isParentOf(element);

        if(hasRootOwner || element.isContainerItem || isActiveCotnainerItemContent) {
            this.checkCurrentActivation(element);
            element.activate();
            this.activeElements.push(element);
            return true;
        }

        return false;
    };

    TemplateInlineEdit.prototype.checkCurrentActivation = function (element) {
        var that = this;
        var oldActiveElements = this.activeElements;
        this.activeElements = [];
        $.each(oldActiveElements, function(index, active) {
            if(active.isParentOf(element) || element.isEqual(active) || that.isActiveItem(active)) {
                that.activeElements.push(active);
            } else {
                active.deactivate();
            }
        });
    };

    TemplateInlineEdit.prototype.isActiveItem = function (element) {
        return this.activeItem && this.activeItem.isEqual(element);
    }

    TemplateInlineEdit.prototype.getElement = function ($elem) {
        return this.getElementType($elem);
    };

     TemplateInlineEdit.prototype.getElementType = function ($elem) {
        if($elem.data('element')) {
            return $elem.data('element');
        }

        if($elem.is('[data-template-item]')) {
            return new TemplateContainerItem($elem);
        }

        switch ($elem.data('template-content')) {
            case 'humhub\\modules\\custom_pages\\modules\\template\\models\\ContainerContent':
                return new TemplateContainerElement($elem);
                break;
            default:
                return new TemplateElement($elem);
                break;
        }
    };

    var editPage = new TemplateInlineEdit();
})();