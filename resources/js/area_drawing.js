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
