let SessionSettings = (function()
{
    SessionSettings.defaults =
        {
            height: 600,
            width: 600,
            highlightRoom: 0,
        };

    SessionSettings.url = '/settings';

    function SessionSettings(pageName)
    {
        this.pageName = pageName;
        this.settings = {};
        this.syncSettings();
        //register directives here
        $('[save-tab]').on('click', function()
        {
            window.sessionSettings.set('active_tab', $(this).attr('save-tab'));
        });
        $('[save-fn]').on('click', function()
        {
            window.sessionSettings.set('active_fn', $(this).attr('save-fn'));
        });
    }


    SessionSettings.prototype.restoreSettings = function()
    {
        if(this.settings.hasOwnProperty('active_tab'))
        {
            //in this case, we will restore the tab, first remove the active tab
            $('.nav-link.active').removeClass('active');
            //add the new active class to the tab
            $('#tab-' + this.settings.active_tab).addClass('active');
            //next we remove the show from the active pane
            $('.tab-pane.active').removeClass('active').removeClass('show');
            $('#tab-pane-' + this.settings.active_tab).addClass('active').addClass('show');
        }
        if(this.settings.hasOwnProperty('active_fn'))
        {
            //this is the easiest case, we simply execute the passed function
            eval(this.settings.active_fn);
        }
    }

    SessionSettings.prototype.syncSettings = function()
    {
        let config =
            {
                params: { key: this.pageName },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

            }
        axios.get(SessionSettings.url, config)
            .then(function(response)
            {
                window.sessionSettings.settings = response.data;
                if(window.sessionSettings.settings === "" || Array.isArray(window.sessionSettings.settings))
                    window.sessionSettings.settings = {};
                window.sessionSettings.restoreSettings();
            })
            .catch((error) => console.log(error));
    }

    SessionSettings.prototype.get = function(key, defaultValue = null)
    {
        if(this.settings.hasOwnProperty(key))
            return this.settings[key];
        this.set(key, defaultValue);
        return defaultValue;
    }

    SessionSettings.prototype.set = function(key, value)
    {
        //first we prepare the settings object
        this.settings[key] = value;
        let params =
                    {
                        key: this.pageName,
                        value: this.settings
                    };
        axios.post(SessionSettings.url, params)
            .then(function(response)
                {
                    console.log(response);
                })
            .catch((error) => console.log(error));
    }

    return SessionSettings;
})();

