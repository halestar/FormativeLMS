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
