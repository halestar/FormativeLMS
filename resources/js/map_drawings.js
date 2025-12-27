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
