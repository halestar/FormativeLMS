let LmsToast = (function () {
    /**
     * LMS Toast config definition:
     * {
     *     fa_icon: the font awesome string to use. will be wrapped in an i tag.
     *     icon: html string for the icon. Overrides fa_icon
     *     toast: EL_STYLE object for the toast container.
     *     toast_header: EL_STYLE object for the toast header
     *     toast_icon: EL_STYLE object for the toast icon container.
     *     toast_title: EL_STYLE object for the toast title container.
     *     toast_body: EL_STYLE object for the toast body container.
     *     autohide: boolean to determine if the toast should autohide.
     *     delay: the delay in milliseconds before the toast autohides.
     * }
     *
     * EL_STYLE definition:
     * {
     *      classes: string of the classes you would like to attach to this element
     *      styles: array of CSS_STYLE objects to attach to the container.
     *              can override the classes or add to them. See below for the
     *              description of the CSS_STYLE object.
     * }
     *
     * CSS_STYLE definition:
     * {
     *     css: the css string to manipulate.
     *     value: the value to attach to the css string.
     * }
     */
    LmsToast.defaultToast =
        {
            fa_icon: 'fa-solid fa-triangle-exclamation',
            toast:
                {
                    classes: ''
                },
            toast_header:
                {
                    classes: 'bg-primary bg-gradient',
                    styles:
                        [
                            {
                                css: '--bs-bg-opacity',
                                value: '0.4'
                            }
                        ]
                },
            toast_icon:
                {
                    classes: 'text-primary'
                },
            toast_title:
                {
                    classes: 'text-capitalize'
                },
            toast_body:
                {
                    classes: 'text-bg-light'
                },
            autohide: true,
            delay: 5000,
        };
    LmsToast.messageToast =
        {
            fa_icon: 'fa-solid fa-message',
            toast:
                {
                    classes: ''
                },
            toast_header:
                {
                    classes: 'bg-primary bg-gradient',
                    styles:
                        [
                            {
                                css: '--bs-bg-opacity',
                                value: '0.4'
                            }
                        ]
                },
            toast_icon:
                {
                    classes: 'text-primary'
                },
            toast_title:
                {
                    classes: 'text-capitalize'
                },
            toast_body:
                {
                    classes: 'text-bg-light'
                },
            autohide: true,
            delay: 3000,
        };


    function LmsToast(title, message, action_link = null, config = {}) {
        this.title = title;
        this.message = message;
        this.config = {...LmsToast.defaultToast, ...config};
        this.action_link = action_link;
        this.showToast();
    }

    LmsToast.prototype.applyStyle = function (element, config) {
        if (typeof config === 'object') {
            if (config.hasOwnProperty('classes')) {
                element.addClass(config.classes);
            }
            if (config.hasOwnProperty('styles')) {
                for (const style of config.styles) {
                    if (style.hasOwnProperty('css') && style.hasOwnProperty('value')) {
                        element.css(style.css, style.value);
                    }
                }
            }
        }
    }

    LmsToast.prototype.showToast = function () {
        let template = $($('#toast-template').html());
        this.applyStyle(template, this.config.toast);
        this.applyStyle(template.find('.toast-header'), this.config.toast_header);
        let icon_container_el = template.find('.toast-icon');
        if (this.config.hasOwnProperty("icon")) {
            icon_container_el.append($(this.config.icon));
        } else if (this.config.hasOwnProperty('fa_icon')) {
            icon_container_el.append($('<i class="' + this.config.fa_icon + '"></i>'));
        }
        this.applyStyle(icon_container_el, this.config.toast_icon)
        let toast_title = template.find('.toast-title');
        this.applyStyle(toast_title, this.config.toast_title);
        toast_title.html(this.title);
        let toast_body = template.find('.toast-body');
        this.applyStyle(toast_body, this.config.toast_body);
        template.find('.toast-body').html(this.message);
        if(this.action_link !== null)
        {
            let action_link = this.action_link;
            template.find('.toast-body').on('click', function() { window.location.href = action_link; });
        }
        $('#toast-container').append(template);
        if (this.config.autohide)
            template.toast({autohide: true, delay: this.config.delay}).toast('show');
        else
            template.toast({autohide: false}).toast('show');
    }

    return LmsToast;
})();
