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

function showTooltip(element, message, config = {})
{
    let defaultConfig =
    {
        duration: 5000,
        clickAway: true,
        direction: 'end',
        theme: 'primary'
    }
    let finalConfig = {...defaultConfig, ...config};
    element = $(element);
    element.css('position', 'relative');
    let tooltip = $('<div class="lms-tooltip lms-tooltip-' + finalConfig.direction +
        ' lms-tooltip-' + finalConfig.theme + '"><span class="lms-tooltip-text">' + message + '</span></div>');
    element.append(tooltip);
    if(finalConfig.clickAway)
    {
        tooltip.on('blur', function() { tooltip.remove(); });
    }
    if(finalConfig.duration > 0)
    {
        setTimeout(function() { tooltip.remove(); }, finalConfig.duration);
    }
}

function copyLink(originator, url, tooltip_config = {})
{
    navigator.clipboard.writeText(url).then(function()
    {
        showTooltip(originator, 'Link Copied!', tooltip_config);
    });
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

    // Defaults for the object.
    AreaDrawing.defaults =
        {
            //room data
            name: 'New Area',
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
            highlightSpecial: false,
            points: [],
            room_id: null,
        };

    /**
     *  Constructor for the AreaDrawing class.
     * @param canvas The canvas to draw on.
     * @param ctx The canva's context
     * @param config The configuration object.
     * @constructor
     */
    function AreaDrawing(canvas, ctx, config)
    {
        //save the passed paramenters
        this.canvas = canvas;
        this.ctx = ctx;
        //merge the config
        this.config = { ...AreaDrawing.defaults, ...config };
        //set the canvas dimensions
        this.width = this.canvas.width;
        this.height = this.canvas.height;
        //load the config variables.
        this.points = this.config.points;
        this.special = this.config.highlightSpecial;
        this.name = this.config.name;
        //set the defaults.
        this.isDrawing = false;
        this.highlight = false;
    }

    /**********************************************************************
     * Internal Functions
     */

    /**
     * Calculates the width of the area drawing
     * @returns {number} The width of the area drawing in pixels
     */
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

    /**
     * This function wraps the text to fit the current Area Drawing
     * @param text
     * @returns {*[]}
     */
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

    /**********************************************************************
     * API Functions
     */

    /**
     * This function checks if a point is inside the bounds of this area drawing.
     * @param pointX The X coordinate of the point to check.
     * @param pointY The Y coordinate of the point to check.
     * @returns {boolean} True if the point is inside the bounds, false otherwise.
     */
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

    /**
     * This function draws the name of the area inside the area drawing.
     */
    AreaDrawing.prototype.drawName = function()
    {
        if(this.name)
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
            if(this.special)
            {
                this.ctx.strokeStyle = this.config.special_text_stroke;
                this.ctx.fillStyle = this.config.special_text_fill;
                this.ctx.font = this.config.special_text_font;
            }
            else if(this.highlight)
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
            let nameLines = this.wrapText(this.name);
            let lineHeight = 15;
            //adjust center for the number of lines
            for(let i = 0; i < nameLines.length; i++)
            {
                this.ctx.strokeText(nameLines[i], centerX, centerY + (i * lineHeight), this.selectionWidth());
                this.ctx.fillText(nameLines[i], centerX, centerY + (i * lineHeight), this.selectionWidth());
            }
        }
    }

    /**
     * This function sets the Area Drawing to drawing mode, which means that
     * a line is always drawn from the last point to the current mouse position.
     */
    AreaDrawing.prototype.beginDrawing = function()
    {
        this.isDrawing = true;
        this.points = [];
    }

    /**
     * This functions clears all the points from the area drawing.
     */
    AreaDrawing.prototype.clearDrawing = function()
    {
        this.points = [];
    }

    /**
     *  This function ends the drawing process, so that shape is always closed.
     */
    AreaDrawing.prototype.endDrawing = function()
    {
        this.isDrawing = false;
    }

    /**
     * This function returns the points that make up this shape.
     * @returns {*} An array of x,y coordinates that make up the shape.
     */
    AreaDrawing.prototype.getData = function()
    {
        return this.points;
    }

    /**
     * This function loads the area drawing data.
     * @param data An array of x,y coordinates that make up the shape.
     */
    AreaDrawing.prototype.loadData = function(data)
    {
        this.points = data;
    }

    /**
     * This function sets the name of the area drawing.
     * @param name The name to set for the area drawing.
     */
    AreaDrawing.prototype.setName = function(name)
    {
        this.name = name;
    }

    /**
     * This function toggles whether this area drawing should be highlighted using the highlight
     * property
     * @param highlight True if the area drawing should be highlighted, false otherwise.
     */
    AreaDrawing.prototype.setHighlight = function(highlight)
    {
        this.highlight = highlight;
    }

    /**
     * This function returns whether this area drawing is highlighted.
     * @returns {boolean} True if the area drawing is highlighted, false otherwise.
     */
    AreaDrawing.prototype.isHighlighted = function()
    {
        return this.highlight;
    }

    /**
     * Gets the room id assigned to this area drawing.
     * @returns {null|*} The room id assigned to this area drawing, or null if no room id is assigned.
     */
    AreaDrawing.prototype.getRoomId = function()
    {
        return this.config.room_id;
    }

    /**
     * This function adds a point to the area drawing.
     * @param x The x coordinate of the point to add.
     * @param y The y coordinate of the point to add.
     */
    AreaDrawing.prototype.addPoint = function(x, y)
    {
        this.points.push({x: x, y: y });
        if(this.isDrawing && this.isShapeClosed())
            this.endDrawing();
    }

    AreaDrawing.prototype.isShapeClosed = function()
    {
        // if there are more than 2 points (min 3 for a triangle) and the last point is
        // within 10 pixels of the original point (where the point outline is), then
        //we will consider this polygon "complete"
        if(this.points.length <= 2)
            return false;
        let firstX = this.points[0].x;
        let firstY = this.points[0].y;
        let lastX = this.points[this.points.length - 1].x;
        let lastY = this.points[this.points.length - 1].y;
        return ((lastX >= (firstX - 5) && lastX <= (firstX + 5)) &&
            (lastY >= (firstY - 5) && lastY <= (firstY + 5)));
    }

    /**
     * This function will render the area drawing on the canvas.
     */
    AreaDrawing.prototype.render = function()
    {
        //Draw the existing path
        this.ctx.strokeStyle = "#000";
        this.ctx.lineWidth = 1;
        this.ctx.beginPath();
        for(let i = 0; i < this.points.length; i++)
        {
            //this draws a square inside a square to denote a point
            this.ctx.strokeRect(this.points[i].x - 2, this.points[i].y - 2, 4, 4);
            this.ctx.strokeRect(this.points[i].x - 5, this.points[i].y - 4, 10, 10);
            //if this is the first point, we're simply moving the drawing point to it. Every poiont after that we draw a
            //line from the last point to this one.
            if(i === 0)
                this.ctx.moveTo(this.points[i].x, this.points[i].y);
            else
                this.ctx.lineTo(this.points[i].x, this.points[i].y);
        }

        //at this point, we're done drawing the path.
        if(!this.isDrawing)
        {
            //if we're not currently drawing this path, then close off the shape
            this.ctx.closePath();
            this.ctx.stroke();
            //we will also fill the shape with one of 3 colors:
            // 1. special_background if this area drawing is special (usually a selected room)
            // 2. highlighted_background if this area drawing is highlighted (usually a mouse-over area.
            if(this.special)
                this.ctx.fillStyle = this.config.special_background;
            else if(this.highlight)
                this.ctx.fillStyle = this.config.highlighted_background;
            else
                this.ctx.fillStyle = this.config.bound_background;
            this.ctx.fill();
            //finally, we draw the area name.
            this.drawName();
        }
    }

    return AreaDrawing;
})();

let MapDrawings = (function()
{
    //Default config paranments
    MapDrawings.defaults =
    {
        height: 600,
        width: 600,
        highlightRoom: 0,
        action: null
    };

    //Default URLS
    MapDrawings.urls =
        {
            get_building_area: "/locations/areas/map",
        };

    /**
     * Constructor for the MapDrawings class.
     * @param container The container ID that will hold the map
     * @param buildingAreaId THe id of the building area that will be drawn on the map.
     * @param config The configuration parameters for the map.
     * @constructor
     */
    function MapDrawings(container, buildingAreaId, config)
    {
        //get the root container parent.
        this.containerName = container;
        this.container = $('#' + this.containerName);
        //save the config parameters
        this.config = { ...MapDrawings.defaults, ...config };
        //set some defaults.
        this.isDrawing = false;
        this.newDrawing = null;
        //we create the canvas
        this.container.addClass('blueprint-container');
        this.container.append("<canvas id='blueprint-" + this.containerName +
            "' class='w-100 h-100' width='" + this.config.width + "' height='" +
            this.config.height + "'></canvas>");
        //we also create the overlay that will be applied when loading maps
        this.overlay = $("<div class='position-absolute top-0 left-0 w-100 h-100 bg-secondary d-flex flex-columns'>" +
            "<div class='spinner-border m-auto' role='status'>" +
            "<span class='visually-hidden'>Loading...</span></div></div>");
        //save the canvas and the context for other modules.
        this.canvas = document.getElementById("blueprint-" + this.containerName);
        this.ctx = this.canvas.getContext('2d');
        //finally, we build the map
        this.loadBuildingArea(buildingAreaId);
        //lastly, add the event listeners to the canvas element.
        this.canvas.addEventListener('mousemove', this.onMouseMove.bind(this));
        this.canvas.addEventListener('click', this.onMouseClick.bind(this));
    }

    /**********************************************************************
     * Internal Functions
     */

    /**
     * This function will setup the map for the given map object.
     * @param mapObj The map object you get from the API call.
     */
    MapDrawings.prototype.setupMap = function(mapObj)
    {
        //first, we load the map image as the background of the container element
        this.blueprintUrl = mapObj.blueprint_url;
        this.container.css('background-image', 'url("' + this.blueprintUrl + '")');
        //next, we will be loading all the room data and adding them to the list of drawings.
        this.drawings = [];
        for(let i = 0; i < mapObj.rooms.length; i++)
        {
            let config =
                {
                    name: mapObj.rooms[i].name,
                    room_id: mapObj.rooms[i].id,
                    highlightSpecial: (this.config.highlightRoom === mapObj.rooms[i].id),
                    points: mapObj.rooms[i].img_data,
                }
            let aDrawing = new AreaDrawing(this.canvas, this.ctx, config);
            this.drawings.push(aDrawing);
        }
        //at this point, all the data is loaded, so we remove the loading overlay
        this.overlay.remove();
        //and we render the map.
        this.render();
    }

    /**
     * The internal rendering function for the map.
     * This draws the map and either all the drawing in the map, or the current
     * shape that we're drawing.
     */
    MapDrawings.prototype.render = function ()
    {
        this.ctx.clearRect(0, 0, this.config.width, this.config.height);
        if(this.isDrawing && this.newDrawing !== null)
        {
            this.newDrawing.render();
        }
        else
        {
            for (let i = 0; i < this.drawings.length; i++) {
                this.drawings[i].render();
            }
        }
    };


    /**********************************************************************
     * API Functions
     */

    /**
     * This function will load the map for the given building area, and
     * clear any drawings that are happening.
     * @param buildingAreaId The id of the building area to load.
     */
    MapDrawings.prototype.loadBuildingArea = function(buildingAreaId)
    {
        //if we were drawing, cancel it.
        if(this.newDrawing !== null)
        {
            this.isDrawing = false;
            this.newDrawing.clear();
            this.newDrawing = null;
        }
        this.canvas.style.cursor = 'default';
        that = this;
        //save the area id and append the loading overlay
        this.buildingAreaId = buildingAreaId;
        this.container.append(this.overlay);
        // call the api to get the map info
        axios.get(MapDrawings.urls.get_building_area + "/" + this.buildingAreaId)
            .then(function(response)
            {
                //here we get a valid map response, so we load the map.
                that.setupMap(response.data.data);
            })
            .catch((error) => console.log(error));
    }

    /**
     * This function will reload the map for the current building area.
     */
    MapDrawings.prototype.reload = function()
    {
        this.loadBuildingArea(this.buildingAreaId);
    };

    /**
     * Gets the canvas element that the areas will be drawn on.
     * @returns {HTMLElement} The canvas element that the areas will be drawn on.
     */
    MapDrawings.prototype.getCanvas = function()
    {
        return this.canvas;
    };

    /**
     * Gets the canvas context that the areas will be drawn on.
     * @returns {*} The canvas context that the areas will be drawn on.
     */
    MapDrawings.prototype.getCtx = function()
    {
        return this.ctx;
    };

    /**
     * This function will highlight a single room on the map. It will remove the highlight from any other room.
     * @param roomId The id of the room to highlight.
     */
    MapDrawings.prototype.highlightRoom = function(roomId = null)
    {
        for(let i = 0; i < this.drawings.length; i++)
            this.drawings[i].setHighlight((roomId !== null) && (this.drawings[i].getRoomId() === roomId));
        this.render();
    };

    /**
     * Same as highlightRoom, but accepts an array of room ids.
     * @param rooms An array of room ids to highlight.
     */
    MapDrawings.prototype.highlightRooms = function(rooms = [])
    {
        for(let i = 0; i < this.drawings.length; i++)
            this.drawings[i].setHighlight(rooms.includes( this.drawings[i].getRoomId()));
        this.render();
    };

    /**
     * This function will remove the highlight from all rooms on the map.
     */
    MapDrawings.prototype.removeHighlight = function()
    {
        for(let i = 0; i < this.drawings.length; i++)
            this.drawings[i].setHighlight(false);
        this.render();
    };

    /**
     * This function will begin drawing a new area on the map.
     * @param area_name The name of the area to draw. Defaults to "New Area".
     */
    MapDrawings.prototype.beginDrawing = function(area_name = "New Area")
    {
        this.isDrawing = true;
        this.newDrawing = new AreaDrawing(this.canvas, this.ctx, {name: area_name});
        this.newDrawing.beginDrawing();
        this.canvas.style.cursor = 'crosshair';
        this.render();
    };

    /**
     * This function will end the current drawing on the map and re-render the existing shapes.
     */
    MapDrawings.prototype.endDrawing = function()
    {
        this.newDrawing.endDrawing();
        this.isDrawing = false;
        this.newDrawing = null;
        this.canvas.style.cursor = 'default';
        this.render();
    };

    /**
     * This function will clear the currently drawn area on the map. It will NOT end drawing though.
     */
    MapDrawings.prototype.clearDrawing = function()
    {
        if(this.isDrawing && this.newDrawing !== null)
            this.newDrawing.clearDrawing();
    };

    /**
     * This function will return the bounds of the current drawing on the map.
     * @returns {*|null} The bounds of the current drawing on the map. Returns null if no drawing is happening.
     */
    MapDrawings.prototype.getDrawingBounds = function()
    {
        if(this.isDrawing && this.newDrawing !== null)
            return this.newDrawing.getData();
        return null;
    };

    /**********************************************************************
     * EVENT Functions
     */

    /**
     * This function fires when the mouse is moved over the canvas. If the map is in drawing mode, then
     * the current shape being drawn will be drawn with a line ending where the mouse is. If we're
     * not in drawing mode, but we are currently hovering over a shape, then the shape will be highlighted.
     * @param event
     */
    MapDrawings.prototype.onMouseMove = function(event)
    {
        //stop all other events from bubbling.
        event.stopPropagation();
        //the x and y coordintes are absolute, we need to convert them to relative to the canvas.
        let rect = this.canvas.getBoundingClientRect();
        let mouseX = event.clientX - ~~rect.left;
        let mouseY = event.clientY - ~~rect.top;
        if(this.isDrawing && this.newDrawing !== null)
        {
            //if we're currently drawing a shape, then this is the only shape that will render.
            this.render();
            if(this.newDrawing.points.length > 0 && this.newDrawing.isDrawing)
            {
                let lastX = this.newDrawing.points[this.newDrawing.points.length - 1].x;
                let lastY = this.newDrawing.points[this.newDrawing.points.length - 1].y;
                this.ctx.moveTo(lastX, lastY);
                this.ctx.lineTo(mouseX, mouseY);
            }
            this.ctx.stroke();
        }
        else
        {
            //if we're not currenly drawing, then we check if we're hovering a shape and set the hover over it.
            //we will only update if there is a change though, to lessen the load on the canvas.
            let changes = false;
            for(let i = 0; i < this.drawings.length; i++)
            {
                let inside = this.drawings[i].isInsideBounds(mouseX, mouseY);
                if(inside && !this.drawings[i].isHighlighted())
                {
                    //if we're inside a shape the is not highlighted, then we highlight it.
                    this.drawings[i].setHighlight(true);
                    changes = true;
                }
                else if(!inside && this.drawings[i].isHighlighted())
                {
                    //if we're not inside a shape that is highlighted, then we un-highlight it.
                    this.drawings[i].setHighlight(false);
                    changes = true;
                }
            }
            //re-render the map if there was a change.
            if(changes)
                this.render();
        }
    };

    /**
     * This function is called when the mouse is clicked on the map. If we're currently drawing on the map, then
     * we add a point to the current shape being drawn and check if the shape is complete. If we're not drawing,
     * and we have an action function set in the config, then we check if we're clicking on a shape and, if we
     * are, we call the action passed to the config with the room id of the shape that was clicked.
     * @param event The mouse event that triggered the click.
     */
    MapDrawings.prototype.onMouseClick = function(event)
    {
        //the x and y coordintes are absolute, we need to convert them to relative to the canvas.
        let rect = this.canvas.getBoundingClientRect();
        let mouseX = event.clientX - ~~rect.left;
        let mouseY = event.clientY - ~~rect.top;
        if(this.isDrawing && this.newDrawing !== null && this.newDrawing.isDrawing)
        {
            // in this case we begin the drawing by creating a point at this location
            this.newDrawing.addPoint(mouseX, mouseY);
            this.render();
        }
        else if(typeof this.config.action === 'function')
        {
            let room_id = null;
            for(let i = 0; i < this.drawings.length; i++)
            {
                if(this.drawings[i].isInsideBounds(mouseX, mouseY))
                {
                    room_id = this.drawings[i].getRoomId();
                    break;
                }
            }
            if(room_id !== null)
                this.config.action(room_id);
        }
    }

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

    SessionSettings.prototype.saveTo = function (key, value)
    {
        let params =
            {
                key: key,
                value: value
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
