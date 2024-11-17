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
