function confirmDelete(msg, url)
{
    if(confirm(msg))
    {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        var delInput = document.createElement('input');
        delInput.type = 'hidden';
        delInput.name = '_method';
        delInput.value = 'DELETE';
        form.appendChild(delInput);
        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = jQuery('meta[name="csrf-token"]').attr('content');
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}

function showClassNotification(notification) {
    //first, set up a toast
    let template = $($('#toast-template').html());
    template.css('border-color', notification.borderColor);
    template.find('.toast-header')
        .css('background-color', notification.bgColor)
        .css('color', notification.textColor);
    template.find('.toast-icon').append($(notification.icon));
    template.find('.toast-title').html(notification.title);
    template.find('.toast-body').html(notification.message);
    $('#toast-container').append(template);
    template.toast({autohide: true, delay: 5000}).toast('show');
    //next we add the notification to the menu
    let notificationTemplate = $($('#notification-template').html());
    notificationTemplate.attr('href', notification.url)
        .css('border-color', notification.borderColor);
    notificationTemplate.find('.notification-header')
        .css('background-color', notification.bgColor)
        .css('color', notification.textColor);
    notificationTemplate.find('.notification-title')
        .html(notification.title);
    notificationTemplate.find('.notification-icon')
        .append($(notification.icon));
    notificationTemplate.find('.notification-body').html(notification.message);
    $('#notifications-dropdown-container').prepend(notificationTemplate);
    //and we make the menu visible
    $('#notification-menu').removeClass('d-none');
}

function showClassMessageNotification(notification) {
    //first, set up a toast
    let template = $($('#toast-template').html());
    template.css('border-color', notification.borderColor);
    template.find('.toast-header')
        .css('background-color', notification.bgColor)
        .css('color', notification.textColor);
    template.find('.toast-icon').append($(notification.icon));
    template.find('.toast-title').html(notification.title);
    template.find('.toast-body').html(notification.message);
    $('#toast-container').append(template);
    template.toast({autohide: true, delay: 5000}).toast('show');
}

function generatePassword()
{
    return Array(8).fill('123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz!@#$')
        .map(x => x[Math.floor(crypto.getRandomValues(new Uint32Array(1))[0] / (0xffffffff + 1) * x.length)]).join('');
}

let TextCounter = (function()
{
    function TextCounter(container, max_chars = 255, min_chars = -1)
    {
        this.container = $('#' + container);
        this.max_chars = max_chars;
        this.min_chars = min_chars;
        let text_length = this.container.val().length;
        this.count_container = $('<span class="float-end rounded count-message px-1" id="count_message">' +
            text_length + ' / ' + this.max_chars + '</span>');

        if(text_length > this.max_chars || text_length < this.min_chars)
            this.count_container.removeClass('bg-secondary').addClass('text-white bg-danger');
        else
            this.count_container.removeClass('text-white bg-danger').addClass('bg-secondary');

        this.count_container.insertAfter(this.container);

        that = this;
        this.container.on('keyup', function()
        {
            let text_length = that.container.val().length;
            if(text_length > that.max_chars || text_length < that.min_chars)
                that.count_container.removeClass('bg-secondary').addClass('text-white bg-danger');
            else
                that.count_container.removeClass('text-white bg-danger').addClass('bg-secondary');
            that.count_container.html(text_length + ' / ' + that.max_chars);
        });
    }



    return TextCounter;
})();


let AreaDrawing = (function()
{

    function AreaDrawing(canvas, ctx, config)
    {
        this.canvas = canvas;
        this.ctx = ctx;
        this.config = { ...this.getDefaults(), ...config };
        this.width = this.canvas.width;
        this.height = this.canvas.height;
        this.isDrawing = false;
        this.mouseX = 0;
        this.mouseY = 0;
        this.points = [];
        this.highlightBounds = false;
        $(this.canvas).on('mouseover', this.config.mouseOver);
        $(this.canvas).on('mouseout', this.config.mouseOut);
        $(this.canvas).on('mousemove', this.config.mouseMove);
        $(this.canvas).on('click', this.config.click);
    }

    AreaDrawing.prototype.getDefaults = function()
    {
        return {
            //room data
            name: 'New Area',
            capacity: null,
            phone: null,
            room_id: null,
            action: '',
            //rendering data
            bound_background: 'rgb(0 0 0 / 40%)',
            bound_text_stroke: '#000',
            bound_text_fill: '#fff',
            bound_text_font: '16px Monospace',
            highlighted_background: 'rgb(137 206 0 / 40%)',
            highlighted_text_stroke: '#fff',
            highlighted_text_fill: '#000',
            highlighted_text_font: '18px Monospace',
            special_background: 'rgba(196 70 1 / 40%)',
            special_text_stroke: '#fff',
            special_text_fill: '#000',
            special_text_font: '18px Monospace',
            clearFirst: true,
            highlightSpecial: false,
            //event data
            mouseOver: $.proxy(this.onMouseOver, this),
            mouseOut: $.proxy(this.onMouseOut, this),
            mouseMove: $.proxy(this.onMouseMove, this),
            click: $.proxy(this.onMouseClick, this),
        };
    }

    AreaDrawing.prototype.isInsideBounds = function(pointX, pointY)
    {
        let isInside = false;
        for (let i = 0, j = this.points.length - 1; i < this.points.length; j = i++) {
            const xi = this.points[i].x, yi = this.points[i].y;
            const xj = this.points[j].x, yj = this.points[j].y;

            const intersect = ((yi > pointY) !== (yj > pointY)) &&
                (pointX < (xj - xi) * (pointY - yi) / (yj - yi) + xi);
            if (intersect) isInside = !isInside;
        }
        return isInside;
    }

    AreaDrawing.prototype.onMouseOver = function(event)
    {
        if(this.isDrawing)
        {
            $("html").css("cursor", "crosshair");
        }
    };

    AreaDrawing.prototype.onMouseOut = function(event)
    {
        if(this.isDrawing)
        {
            $("html").css("cursor", "pointer auto");
        }
    };

    AreaDrawing.prototype.onMouseMove = function(event)
    {
        let rect = this.canvas.getBoundingClientRect();
        let mouseX = event.clientX - ~~rect.left;
        let mouseY = event.clientY - ~~rect.top;
        if(this.isDrawing)
        {
            this.mouseX = mouseX;
            this.mouseY = mouseY;
            this.render();
        }
        else
        {
            if(this.points.length > 2 && this.isInsideBounds(mouseX, mouseY))
            {
                if(!this.highlightBounds)
                {
                    this.highlightBounds = true;
                    this.render();
                }
            }
            else
            {
                if(this.highlightBounds)
                {
                    this.highlightBounds = false;
                    this.render();
                }
            }
        }
    };

    AreaDrawing.prototype.selectionWidth = function()
    {
        if(this.points.length > 0)
        {
            let minX = this.points[0].x;
            let maxX = this.points[0].x;
            for(let i = 1; i < this.points.length; i++)
            {
                if(this.points[i].x < minX)
                    minX = this.points[i].x;
                if(this.points[i].x > maxX)
                    maxX = this.points[i].x;
            }
            return maxX - minX;
        }
        return 0;
    }

    AreaDrawing.prototype.beginDrawing = function()
    {
        this.isDrawing = true;
        this.points = [];
    }

    AreaDrawing.prototype.endDrawing = function()
    {
        this.isDrawing = false;
        this.render();
    }

    AreaDrawing.prototype.onMouseClick = function(event)
    {
        if(this.isDrawing)
        {
            // in this case we begin the drawing by creating a point at this location
            let rect = this.canvas.getBoundingClientRect();
            this.points.push({x: event.clientX - ~~rect.left, y: event.clientY - ~~rect.top });

            // if there are more than 2 points (min 3 for a triangle) and the last point is
            // within 10 pixels of the original point (where the point outline is), then
            //we will consider this polygon "complete"
            if(this.points.length > 2)
            {
                let firstX = this.points[0].x;
                let firstY = this.points[0].y;
                let lastX = this.points[this.points.length - 1].x;
                let lastY = this.points[this.points.length - 1].y;
                if((lastX >= (firstX - 5) && lastX <= (firstX + 5)) &&
                    (lastY >= (firstY - 5) && lastY <= (firstY + 5)))
                {
                    this.isDrawing = false;
                }
            }

            this.render();
        }
    }

    AreaDrawing.prototype.wrapText = function(text)
    {
        var words = text.split(' ');
        var currentLine = '';
        var lines = [];
        for(var n = 0; n < words.length; n++)
        {
            var testLine = currentLine + words[n];
            var metrics = this.ctx.measureText(testLine);
            if (metrics.width > this.selectionWidth())
            {
                if(currentLine === '') {
                    //we need to be able to fit one word, so we'll have to add it.
                    lines.push(testLine);
                }
                else
                {
                    lines.push(currentLine);
                    currentLine = words[n] + ' ';
                }
            }
            else
            {
                currentLine = testLine;
            }
        }
        lines.push(currentLine);
        return lines;
    }

    AreaDrawing.prototype.drawName = function()
    {
        if(this.config.name)
        {
            let centerX = 0;
            let centerY = 0;
            for (let i = 0; i < this.points.length; i++)
            {
                centerX += this.points[i].x;
                centerY += this.points[i].y;
            }
            centerX /= this.points.length;
            centerY /= this.points.length;

            // Set text alignment and baseline
            this.ctx.textAlign = "center";
            this.ctx.textBaseline = "middle";
            if(this.config.highlightSpecial)
            {
                this.ctx.strokeStyle = this.config.special_text_stroke;
                this.ctx.fillStyle = this.config.special_text_fill;
                this.ctx.font = this.config.special_text_font;
            }
            else if(this.highlightBounds)
            {
                this.ctx.strokeStyle = this.config.highlighted_text_stroke;
                this.ctx.fillStyle = this.config.highlighted_text_fill;
                this.ctx.font = this.config.highlighted_text_font;
            }
            else
            {
                this.ctx.strokeStyle = this.config.bound_text_stroke;
                this.ctx.fillStyle = this.config.bound_text_fill;
                this.ctx.font = this.config.bound_text_font;
            }

            // Draw the text
            let nameLines = this.wrapText(this.config.name);
            let lineHeight = 15;
            //adjust center for the number of lines
            for(let i = 0; i < nameLines.length; i++)
            {
                this.ctx.strokeText(nameLines[i], centerX, centerY + (i * lineHeight), this.selectionWidth());
                this.ctx.fillText(nameLines[i], centerX, centerY + (i * lineHeight), this.selectionWidth());
            }
        }
    }

    AreaDrawing.prototype.render = function()
    {
        //clear the canvas
        if(this.config.clearFirst)
            this.ctx.clearRect(0, 0, this.width, this.height);

        //Draw the existing path
        this.ctx.strokeStyle = "#000";
        this.ctx.lineWidth = 1;
        this.ctx.beginPath();
        for(let i = 0; i < this.points.length; i++)
        {
            this.ctx.strokeRect(this.points[i].x - 2, this.points[i].y - 2, 4, 4);
            this.ctx.strokeRect(this.points[i].x - 5, this.points[i].y - 4, 10, 10);
            if(i === 0)
                this.ctx.moveTo(this.points[i].x, this.points[i].y);
            else
                this.ctx.lineTo(this.points[i].x, this.points[i].y);
        }

        if(this.isDrawing)
        {
            // if we're drawing and we have at least a starting point, draw a line
            // from the last point to where the mouse is at
            if(this.points.length > 0)
            {
                this.ctx.moveTo(this.points[this.points.length - 1].x, this.points[this.points.length - 1].y);
                this.ctx.lineTo(this.mouseX, this.mouseY);
            }
            this.ctx.stroke();
        }
        else
        {
            this.ctx.closePath();
            this.ctx.stroke();
            if(this.config.highlightSpecial)
                this.ctx.fillStyle = this.config.special_background;
            else if(this.highlightBounds)
                this.ctx.fillStyle = this.config.highlighted_background;
            else
                this.ctx.fillStyle = this.config.bound_background;
            this.ctx.fill();
            this.drawName();
        }

    }

    AreaDrawing.prototype.clear = function()
    {
        this.points = [];
        this.isDrawing = true;
        this.render();
    }

    AreaDrawing.prototype.selectionHeight = function()
    {
        if(this.points.length > 0)
        {
            let minY = this.points[0].y;
            let maxY = this.points[0].y;
            for(let i = 1; i < this.points.length; i++)
            {
                if(this.points[i].y < minY)
                    minY = this.points[i].y;
                if(this.points[i].y > maxY)
                    maxY = this.points[i].y;
            }
            return maxY - minY;
        }
        return 0;
    }

    AreaDrawing.prototype.getData = function()
    {
        return this.points;
    }

    AreaDrawing.prototype.loadData = function(data)
    {
        this.points = data;
        this.render();
    }

    AreaDrawing.prototype.setName = function(name)
    {
        this.config.name = name;
    }

    AreaDrawing.prototype.setAction = function(action)
    {
        this.config.action = action;
    }

    return AreaDrawing;
})();

let MapDrawings = (function()
{
    MapDrawings.defaults =
    {
        height: 600,
        width: 600,
        highlightRoom: 0,
    };

    MapDrawings.urls =
        {
            get_building_area: "/locations/maps/area",
        };

    function MapDrawings(container, buildingAreaId, config)
    {
        this.container = $('#' + container);
        this.config = { ...MapDrawings.defaults, ...config };
        this.buildingAreaId = buildingAreaId;
        //first, we create the elements we need and attach a loading icon.
        this.container.addClass('blueprint-container');
        this.container.append("<canvas id='blueprint-" + this.buildingAreaId +
            "' class='w-100 h-100' width='" + this.config.width + "' height='" +
            this.config.height + "'></canvas>");
        this.overlay = $("<div class='position-absolute top-0 left-0 w-100 h-100 bg-secondary d-flex flex-columns'>" +
            "<div class='spinner-border m-auto' role='status'>" +
            "<span class='visually-hidden'>Loading...</span></div></div>");
        this.container.append(this.overlay);
        this.canvas = document.getElementById("blueprint-" + this.buildingAreaId);
        this.ctx = this.canvas.getContext('2d');
        this.loadBuildingArea();
    }

    MapDrawings.prototype.loadBuildingArea = function()
    {
        that = this;
        // next, we initiate a call to get all the information about the building area
        axios.get(MapDrawings.urls.get_building_area + "/" + this.buildingAreaId)
            .then(function(response)
            {
                that.setupMap(response.data.data);
            })
            .catch((error) => console.log(error));
    }

    MapDrawings.prototype.setupMap = function(mapObj)
    {
        this.blueprintUrl = mapObj.blueprint_url;
        this.container.css('background-image', 'url("' + this.blueprintUrl + '")');
        this.drawings = [];
        for(let i = 0; i < mapObj.rooms.length; i++)
        {
            let config =
                {
                    name: mapObj.rooms[i].name,
                    room_id: mapObj.rooms[i].id,
                    phone: mapObj.rooms[i].phone?.pretty,
                    capacity: mapObj.rooms[i].capacity,
                    clearFirst: false,
                    highlightSpecial: (this.config.highlightRoom === mapObj.rooms[i].id),
                    mouseOver: $.proxy(this.onMouseOver, this),
                    mouseOut: $.proxy(this.onMouseOut, this),
                    mouseMove: $.proxy(this.onMouseMove, this),
                }
            let aDrawing = new AreaDrawing(this.canvas, this.ctx, config);
            if(mapObj.rooms[i].img_data)
                aDrawing.loadData(mapObj.rooms[i].img_data);
            this.drawings.push(aDrawing);
        }
        this.overlay.remove();
        this.render();
    }

    MapDrawings.prototype.render = function ()
    {
        this.ctx.clearRect(0, 0, this.config.width, this.config.height);
        for(let i = 0; i < this.drawings.length; i++)
        {
            this.drawings[i].render();
        }
    }

    MapDrawings.prototype.getCanvas = function()
    {
        return this.canvas;
    }

    MapDrawings.prototype.getCtx = function()
    {
        return this.ctx;
    }

    MapDrawings.prototype.onMouseOver = function(event){};

    MapDrawings.prototype.onMouseOut = function(event){};

    MapDrawings.prototype.onMouseMove = function(event)
    {
        event.stopPropagation();
        let rect = this.canvas.getBoundingClientRect();
        let mouseX = event.clientX - ~~rect.left;
        let mouseY = event.clientY - ~~rect.top;
        let changes = false;
        for(let i = 0; i < this.drawings.length; i++)
        {
            if(this.drawings[i].isInsideBounds(mouseX, mouseY))
            {
                if(!this.drawings[i].highlightBounds)
                {
                    this.drawings[i].highlightBounds = true;
                    changes = true;
                }
            }
            else if(this.drawings[i].highlightBounds)
            {
                this.drawings[i].highlightBounds = false;
                changes = true;
            }
        }
        if(changes)
            this.render();
    };

    return MapDrawings;
})();

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

