/********************************************************
* Date: 15.04.2012
* Author: Girman Evgeniy
* E-Mail: girman.evg@gmail.com
* About : Модуль для управления и написания текста на майке
*********************************************************/

function measureText(context, text) {
        if (context.measureText) {
            return context.measureText(text).width; //-->
        } else if (context.mozMeasureText) { //FF < 3.5
            return context.mozMeasureText(text); //-->
        } else {
        	return 0;  //Added else-clause
        }
        throw "measureText() not supported!";
    }

function fillText(context, text, px, py) {
        var width;
        if (context.fillText) {
            return context.fillText(text, px, py);
        } else if (context.mozDrawText) { //FF < 3.5
            context.save();
            context.translate(px, py);
            width = context.mozDrawText(text);
            context.restore();
            return width;
        } else {
        	return 0;   ///Added else-clause
        }
        throw "fillText() not supported!";
}
	
//библиотека для работы с канвой
canvt = {
		isIE8 : function () {  return navigator.userAgent.indexOf('MSIE 8') > 0; },
		array_shift : function (arr) {
			var ret = [];
			for (var i=1; i<arr.length; ++i) ret.push(arr[i]);
			return ret;
		},	    
		
		/**
		* Clears the canvas by setting the width. You can specify a colour if you wish.
		* @param object canvas The canvas to clear
		*/
		Clear : function (canvas) {
			if ($.browser.msie && $.browser.version < 9) { if (typeof G_vmlCanvasManager != 'undefined') G_vmlCanvasManager.initElement(canvas); } //костыль для ИЕ
			var context = canvas.getContext('2d');
			var color   = arguments[1];

			if (canvt.isIE8() && !color) {
				color = 'white';
			}

			/**
			* Can now clear the canvas back to fully transparent
			*/
			if (!color || (color && color == 'transparent')) {
				context.clearRect(0,0,canvas.width, canvas.height);
				// Reset the globalCompositeOperation
				context.globalCompositeOperation = 'source-over';
			} else {
				context.fillStyle = color;
				if ($.browser.msie && $.browser.version < 9) { if (typeof G_vmlCanvasManager != 'undefined') G_vmlCanvasManager.initElement(canvas); } //костыль для ИЕ
				context = canvas.getContext('2d');
				context.beginPath();
				if (canvt.isIE8()) {
					context.fillRect(0,0,canvas.width,canvas.height);
				} else {
					context.fillRect(-10,-10,canvas.width + 20,canvas.height + 20);
				}
				context.fill();
			}
		},
		
		/**
		* @param object context The context
		* @param string font    The font
		* @param int    size    The size of the text
		* @param int    x       The X coordinate
		* @param int    y       The Y coordinate
		* @param string text    The text to draw
		* @parm  string         The vertical alignment. Can be null. "center" gives center aligned  text, "top" gives top aligned text.
		*                       Anything else produces bottom aligned text. Default is bottom.
		* @param  string        The horizontal alignment. Can be null. "center" gives center aligned  text, "right" gives right aligned text.
		*                       Anything else produces left aligned text. Default is left.
		* @param  bool          Whether to show a bounding box around the text. Defaults not to
		* @param int            The angle that the text should be rotate at (IN DEGREES)
		* @param string         Background color for the text
		* @param bool           Whether the text is bold or not
		* @param bool           Whether the bounding box has a placement indicator
		*/	
		Text : function (context, font, size, x, y, text) {
			/**
			* This calls the text function recursively to accommodate multi-line text
			*/
			if (typeof(text) == 'string' && text.match(/\r\n/)) {
				
				var arr = text.split('\r\n');
				text = arr[0];
				arr = canvt.array_shift(arr);
				var nextline = arr.join('\r\n')
				canvt.Text(context, font, size, arguments[9] == -90 ? (x + (size * 1.5)) : x, y + (size * 1.5), nextline, arguments[6] ? arguments[6] : null, 'center', arguments[8], arguments[9], arguments[10], arguments[11], arguments[12]);
			}

			// Accommodate MSIE
			if (canvt.isIE8()) {
				y += 2;
			}

			context.font = (arguments[11] ? 'Bold ': '') + (canvt.bold ? 'Bold ': '') + (canvt.italic ? 'Italic ': '') + size + 'pt ' + font;

			var i;
			var origX = x;
			var origY = y;
			var originalFillStyle = context.fillStyle;
			var originalLineWidth = context.lineWidth;

			// Need these now the angle can be specified, ie defaults for the former two args
			if (typeof(arguments[6]) == null) arguments[6]  = 'bottom'; // Vertical alignment. Default to bottom/baseline
			if (typeof(arguments[7]) == null) arguments[7]  = 'left';   // Horizontal alignment. Default to left
			if (typeof(arguments[8]) == null) arguments[8]  = null;     // Show a bounding box. Useful for positioning during development. Defaults to false
			if (typeof(arguments[9]) == null) arguments[9]  = 0;        // Angle (IN DEGREES) that the text should be drawn at. 0 is middle right, and it goes clockwise
			if (typeof(arguments[12]) == null) arguments[12] = true;    // Whether the bounding box has the placement indicator

			// The alignment is recorded here for purposes of Opera compatibility
			if (navigator.userAgent.indexOf('Opera') != -1) {
				context.canvas.__rgraph_valign__ = arguments[6];
				context.canvas.__rgraph_halign__ = arguments[7];
			}

			// First, translate to x/y coords
			context.save();

				context.canvas.__rgraph_originalx__ = x;
				context.canvas.__rgraph_originaly__ = y;

				context.translate(x, y);
				x = 0;
				y = 0;
				
				// Rotate the canvas if need be
				if (arguments[9]) {
					context.rotate(arguments[9] / 57.3);
				}

				// Vertical alignment - defaults to bottom
				if (arguments[6]) {
					var vAlign = arguments[6];

					if (vAlign == 'center') {
						context.translate(0, size / 2);
					} else if (vAlign == 'top') {
						context.translate(0, size);
					}
				}


				// Hoeizontal alignment - defaults to left
				if (arguments[7]) {
					var hAlign = arguments[7];
					var width  = canvt.measureTextWidth =  context.measureText(text).width;
		
					if (hAlign) {
						if (hAlign == 'center') {
							context.translate(-1 * (width / 2), 0)
						} else if (hAlign == 'right') {
							context.translate(-1 * width, 0)
						}
					}
				}
				
				
				context.fillStyle = originalFillStyle;

				/**
				* Draw a bounding box if requested
				*/
				context.save();
					 context.fillText(text,0,0);
					 context.lineWidth = 0.5;
					
					if (arguments[8]) {

						var width = context.measureText(text).width;
						var ieOffset = canvt.isIE8() ? 2 : 0;

						context.translate(x, y);
						context.strokeRect(0 - 3, 0 - 3 - size - ieOffset, width + 6, 0 + size + 6);
		
						/**
						* If requested, draw a background for the text
						*/
						if (arguments[10]) {
			
							var offset = 3;
							var ieOffset = canvt.isIE8() ? 2 : 0;
							var width = context.measureText(text).width

							//context.strokeStyle = 'gray';
							context.fillStyle = arguments[10];
							context.fillRect(x - offset, y - size - offset - ieOffset, width + (2 * offset), size + (2 * offset));
							//context.strokeRect(x - offset, y - size - offset - ieOffset, width + (2 * offset), size + (2 * offset));
						}
						
						/**
						* Do the actual drawing of the text
						*/
						context.fillStyle = originalFillStyle;
						context.fillText(text,0,0);

						if (arguments[12]) {
							context.fillRect(
								arguments[7] == 'left' ? 0 : (arguments[7] == 'center' ? width / 2 : width ) - 2,
								arguments[6] == 'bottom' ? 0 : (arguments[6] == 'center' ? (0 - size) / 2 : 0 - size) - 2,
								4,
								4
							);
						}
					}
				context.restore();
				
				// Reset the lineWidth
				context.lineWidth = originalLineWidth;

			context.restore();
		}
}

//компонент для выбора цвета
cp = {
    color: '#ffffff',
	
	namedColors: [
					['#FFFFCC','#FFFFCC'],['#FFFF99','#FFFF99'],['#FFFF99','#FFFF99'],['#FFFF66','#FFFF66'],['#FFFF33','#FFFF33'],['#FFFF00','#FFFF00'],['#CCCC00','#CCCC00'],['#FFCC66','#FFCC66'],['#FFCC00','#FFCC00'],['#FFCC33','#FFCC33'],['#CC9900','#CC9900'],['#CC9933','#CC9933'],['#996600','#996600'],['#FF9900','#FF9900'],['#FF9933','#FF9933'],['#CC9966','#CC9966'],['#CC6600','#CC6600'],['#996633','#996633'],['#663300','#663300'],['#FFCC99','#FFCC99'],['#FF9966','#FF9966'],['#FF6600','#FF6600'],['#CC663','#CC663'],['#993300','#993300'],['#66000','#66000'],['#FF6633','#FF6633'],['#CC330','#CC330'],['#FF3300','#FF3300'],['#FF000','#FF000'],['#CC0000','#CC0000'],['#99000','#99000'],['#FFCCCC','#FFCCCC'],['#FF999','#FF999'],['#FF6666','#FF6666'],['#FF333','#FF333'],['#FF0033','#FF0033'],['#CC003','#CC003'],['#CC9999','#CC9999'],['#CC666','#CC666'],['#CC3333','#CC3333'],['#993333','#993333'],['#990033','#990033'],['#330000','#330000'],['#FF669','#FF669'],['#FF3366','#FF3366'],['#FF006','#FF006'],['#CC3366','#CC3366'],['#99666','#99666'],['#663333','#663333'],['#FF99C','#FF99C'],['#FF3399','#FF3399'],['#FF009','#FF009'],['#CC006','#CC006'],['#99336','#99336'],['#66003','#66003'],['#FF66C','#FF66C'],['#FF00CC','#FF00CC'],['#FF33CC','#FF33CC'],['#CC6699','#CC6699'],['#CC0099','#CC0099'],['#990066','#990066'],['#FFCCFF','#FFCCFF'],['#FF99FF','#FF99FF'],['#FF66FF','#FF66FF'],['#FF33FF','#FF33FF'],['#FF00FF','#FF00FF'],['#CC3399','#CC3399'],['#CC99CC','#CC99CC'],['#CC66CC','#CC66CC'],['#CC00C','#CC00C'],['#CC33CC','#CC33CC'],['#99009','#99009'],['#993399','#993399'],['#CC66F','#CC66F'],['#CC33FF','#CC33FF'],['#CC00F','#CC00F'],['#9900CC','#9900CC'],['#99669','#99669'],['#660066','#660066'],['#CC99F','#CC99F'],['#9933CC','#9933CC'],['#9933F','#9933F'],['#9900FF','#9900FF'],['#66009','#66009'],['#663366','#663366'],['#9966C','#9966C'],['#9966FF','#9966FF'],['#6600C','#6600C'],['#6633CC','#6633CC'],['#663399','#663399'],['#330033','#330033'],['#CCCCFF','#CCCCFF'],['#9999F','#9999F'],['#6633FF','#6633FF'],['#6600F','#6600F'],['#330099','#330099'],['#33006','#33006'],['#9999CC','#9999CC'],['#6666F','#6666F'],['#6666CC','#6666CC'],['#66669','#66669'],['#333399','#333399'],['#33336','#33336'],['#3333FF','#3333FF'],['#3300F','#3300F'],['#3300CC','#3300CC'],['#3333C','#3333C'],['#000099','#000099'],['#00006','#00006'],['#6699FF','#6699FF'],['#3366F','#3366F'],['#0000FF','#0000FF'],['#0000C','#0000C'],['#0033CC','#0033CC'],['#00003','#00003'],['#0066FF','#0066FF'],['#0066C','#0066C'],['#3366CC','#3366CC'],['#0033F','#0033F'],['#003399','#003399'],['#00336','#00336'],['#99CCFF','#99CCFF'],['#3399F','#3399F'],['#0099FF','#0099FF'],['#6699C','#6699C'],['#336699','#336699'],['#00669','#00669'],['#66CCFF','#66CCFF'],['#33CCF','#33CCF'],['#00CCFF','#00CCFF'],['#3399C','#3399C'],['#0099CC','#0099CC'],['#00333','#00333'],['#99CCCC','#99CCCC'],['#66CCC','#66CCC'],['#339999','#339999'],['#66999','#66999'],['#006666','#006666'],['#33666','#33666'],['#CCFFFF','#CCFFFF'],['#99FFF','#99FFF'],['#66FFFF','#66FFFF'],['#33FFF','#33FFF'],['#00FFFF','#00FFFF'],['#00CCC','#00CCC'],['#99FFCC','#99FFCC'],['#66FFC','#66FFC'],['#33FFCC','#33FFCC'],['#00FFC','#00FFC'],['#33CCCC','#33CCCC'],['#00999','#00999'],['#66CC99','#66CC99'],['#33CC9','#33CC9'],['#00CC99','#00CC99'],['#33996','#33996'],['#009966','#009966'],['#00663','#00663'],['#66FF99','#66FF99'],['#33FF9','#33FF9'],['#00FF99','#00FF99'],['#33CC6','#33CC6'],['#00CC66','#00CC66'],['#00993','#00993'],['#99FF99','#99FF99'],['#66FF6','#66FF6'],['#33FF66','#33FF66'],['#00FF6','#00FF6'],['#339933','#339933'],['#00660','#00660'],['#CCFFCC','#CCFFCC'],['#99CC9','#99CC9'],['#66CC66','#66CC66'],['#66996','#66996'],['#336633','#336633'],['#00330','#00330'],['#33FF33','#33FF33'],['#00FF3','#00FF3'],['#00FF00','#00FF00'],['#00CC0','#00CC0'],['#33CC33','#33CC33'],['#00CC3','#00CC3'],['#66FF00','#66FF00'],['#66FF3','#66FF3'],['#33FF00','#33FF00'],['#33CC0','#33CC0'],['#339900','#339900'],['#00990','#00990'],['#CCFF99','#CCFF99'],['#99FF6','#99FF6'],['#66CC00','#66CC00'],['#66CC3','#66CC3'],['#669933','#669933'],['#33660','#33660'],['#99FF00','#99FF00'],['#99FF3','#99FF3'],['#99CC66','#99CC66'],['#99CC0','#99CC0'],['#99CC33','#99CC33'],['#66990','#66990'],['#CCFF66','#CCFF66'],['#CCFF0','#CCFF0'],['#CCFF33','#CCFF33'],['#CCCC9','#CCCC9'],['#666633','#666633'],['#33330','#33330'],['#CCCC66','#CCCC66'],['#CCCC3','#CCCC3'],['#999966','#999966'],['#99993','#99993'],['#999900','#999900'],['#66660','#66660'],['#FFFFFF','#FFFFFF'],['#CCCCC','#CCCCC'],['#999999','#999999'],['#66666','#66666'],['#333333','#333333'],['#00000','#00000']
				],
	/*namedColors: [
        ['#F0F8FF', 'AliceBlue'], ['#FAEBD7', 'AntiqueWhite'], ['#00FFFF', 'Aqua'], ['#7FFFD4', 'Aquamarine'], ['#F0FFFF', 'Azure'], ['#F5F5DC', 'Beige'],
        ['#FFE4C4', 'Bisque'], ['#000000', 'Black'], ['#FFEBCD', 'BlanchedAlmond'], ['#0000FF', 'Blue'], ['#8A2BE2', 'BlueViolet'], ['#A52A2A', 'Brown'],
        ['#DEB887', 'BurlyWood'], ['#5F9EA0', 'CadetBlue'], ['#7FFF00', 'Chartreuse'], ['#D2691E', 'Chocolate'], ['#FF7F50', 'Coral'], ['#6495ED', 'CornflowerBlue'],
        ['#FFF8DC', 'Cornsilk'], ['#DC143C', 'Crimson'], ['#00FFFF', 'Cyan'], ['#00008B', 'DarkBlue'], ['#008B8B', 'DarkCyan'], ['#B8860B', 'DarkGoldenRod'],
        ['#A9A9A9', 'DarkGray'], ['#A9A9A9', 'DarkGrey'], ['#006400', 'DarkGreen'], ['#BDB76B', 'DarkKhaki'], ['#8B008B', 'DarkMagenta'], ['#556B2F', 'DarkOliveGreen'],
        ['#FF8C00', 'Darkorange'], ['#9932CC', 'DarkOrchid'], ['#8B0000', 'DarkRed'], ['#E9967A', 'DarkSalmon'], ['#8FBC8F', 'DarkSeaGreen'], ['#483D8B', 'DarkSlateBlue'],
        ['#2F4F4F', 'DarkSlateGray'], ['#2F4F4F', 'DarkSlateGrey'], ['#00CED1', 'DarkTurquoise'], ['#9400D3', 'DarkViolet'], ['#FF1493', 'DeepPink'], ['#00BFFF', 'DeepSkyBlue'],
        ['#696969', 'DimGray'], ['#696969', 'DimGrey'], ['#1E90FF', 'DodgerBlue'], ['#B22222', 'FireBrick'], ['#FFFAF0', 'FloralWhite'], ['#228B22', 'ForestGreen'],
        ['#FF00FF', 'Fuchsia'], ['#DCDCDC', 'Gainsboro'], ['#F8F8FF', 'GhostWhite'], ['#FFD700', 'Gold'], ['#DAA520', 'GoldenRod'], ['#808080', 'Gray'], ['#808080', 'Grey'],
        ['#008000', 'Green'], ['#ADFF2F', 'GreenYellow'], ['#F0FFF0', 'HoneyDew'], ['#FF69B4', 'HotPink'], ['#CD5C5C', 'IndianRed'], ['#4B0082', 'Indigo'], ['#FFFFF0', 'Ivory'],
        ['#F0E68C', 'Khaki'], ['#E6E6FA', 'Lavender'], ['#FFF0F5', 'LavenderBlush'], ['#7CFC00', 'LawnGreen'], ['#FFFACD', 'LemonChiffon'], ['#ADD8E6', 'LightBlue'],
        ['#F08080', 'LightCoral'], ['#E0FFFF', 'LightCyan'], ['#FAFAD2', 'LightGoldenRodYellow'], ['#D3D3D3', 'LightGray'], ['#D3D3D3', 'LightGrey'], ['#90EE90', 'LightGreen'],
        ['#FFB6C1', 'LightPink'], ['#FFA07A', 'LightSalmon'], ['#20B2AA', 'LightSeaGreen'], ['#87CEFA', 'LightSkyBlue'], ['#778899', 'LightSlateGray'], ['#778899', 'LightSlateGrey'],
        ['#B0C4DE', 'LightSteelBlue'], ['#FFFFE0', 'LightYellow'], ['#00FF00', 'Lime'], ['#32CD32', 'LimeGreen'], ['#FAF0E6', 'Linen'], ['#FF00FF', 'Magenta'], ['#800000', 'Maroon'],
        ['#66CDAA', 'MediumAquaMarine'], ['#0000CD', 'MediumBlue'], ['#BA55D3', 'MediumOrchid'], ['#9370D8', 'MediumPurple'], ['#3CB371', 'MediumSeaGreen'], ['#7B68EE', 'MediumSlateBlue'],
        ['#00FA9A', 'MediumSpringGreen'], ['#48D1CC', 'MediumTurquoise'], ['#C71585', 'MediumVioletRed'], ['#191970', 'MidnightBlue'], ['#F5FFFA', 'MintCream'], ['#FFE4E1', 'MistyRose'], ['#FFE4B5', 'Moccasin'],
        ['#FFDEAD', 'NavajoWhite'], ['#000080', 'Navy'], ['#FDF5E6', 'OldLace'], ['#808000', 'Olive'], ['#6B8E23', 'OliveDrab'], ['#FFA500', 'Orange'], ['#FF4500', 'OrangeRed'], ['#DA70D6', 'Orchid'],
        ['#EEE8AA', 'PaleGoldenRod'], ['#98FB98', 'PaleGreen'], ['#AFEEEE', 'PaleTurquoise'], ['#D87093', 'PaleVioletRed'], ['#FFEFD5', 'PapayaWhip'], ['#FFDAB9', 'PeachPuff'],
        ['#CD853F', 'Peru'], ['#FFC0CB', 'Pink'], ['#DDA0DD', 'Plum'], ['#B0E0E6', 'PowderBlue'], ['#800080', 'Purple'], ['#FF0000', 'Red'], ['#BC8F8F', 'RosyBrown'], ['#4169E1', 'RoyalBlue'],
        ['#8B4513', 'SaddleBrown'], ['#FA8072', 'Salmon'], ['#F4A460', 'SandyBrown'], ['#2E8B57', 'SeaGreen'], ['#FFF5EE', 'SeaShell'], ['#A0522D', 'Sienna'], ['#C0C0C0', 'Silver'],
        ['#87CEEB', 'SkyBlue'], ['#6A5ACD', 'SlateBlue'], ['#708090', 'SlateGray'], ['#708090', 'SlateGrey'], ['#FFFAFA', 'Snow'], ['#00FF7F', 'SpringGreen'],
        ['#4682B4', 'SteelBlue'], ['#D2B48C', 'Tan'], ['#008080', 'Teal'], ['#D8BFD8', 'Thistle'], ['#FF6347', 'Tomato'], ['#40E0D0', 'Turquoise'], ['#EE82EE', 'Violet'],
        ['#F5DEB3', 'Wheat'], ['#FFFFFF', 'White'], ['#F5F5F5', 'WhiteSmoke'], ['#FFFF00', 'Yellow'], ['#9ACD32', 'YellowGreen']
    ],*/
	
	init: function(id, color, onchange) {
		var self = this;
		this.div = (typeof id == 'object' ?id:$(id));
		
		this.div.addClass('colorpicker');
				

		this.bg = $('<div></div>').addClass('bg'); //this.div.position().left
		this.colors = $('<div></div>').addClass('cp').css({ position: 'absolute', left: this.div.width(), top: this.div.height() }).hide();
		this.div.append(this.bg).append(this.colors);
		//$('body').append(this.colors);
		this.div.click(function(){ self.toogle(); return false; })
		
        var h = '<table cellspacing="1" cellpadding="0" border="0">';
		var _x = 14, x = 0, y = 1;
        for (var n=0;n<this.namedColors.length;n++) {
            v = self.namedColors[n];
			if (x > _x) { h += '</tr>'; x = 0; } 
			if (++x == 1) h += '<tr>';
			h += '<td><div style="background-color:' + v[0]+(v[0].length == 6?'0':'') + '" color="' + v[0]+(v[0].length == 6?'0':'')+ '" title="' + v[1] + '" >&nbsp;</div></td>';
        }
        this.colors.html(h + '</table>');
		var t = this.colors.find('table');
		var d = t.find('td div');
		
		d.bind('click', function (e) { 
			self.bg.css('backgroundColor', $(this).attr('color'));
			if (self.onchange) self.onchange($(this).attr('color')); 
			self.hide(); 
			return false; 
		});
		
		if (color) this.setColor(color);
		
		this.onchange = onchange;
		
		return this;
	},
	
	setColor: function(color){
		this.color = color;
		this.bg.css('backgroundColor', color);
	},
	
    hide: function() {
		this.colors.fadeOut(500);
		$(document).unbind('click');
	},
	
    show: function() {
		var self = this;
		var f = function(){ self.hide(); return false; };
		$(document).bind('click', f );
		this.colors.fadeIn(500);
	},
	
	toogle: function() {
		if (this.colors.css('display') == 'block')
			this.hide();
		else
			this.show();
	}
	
}

//компонент|модуль по размещению текста.
ct = {
	workspace	: { left: null, top: null, width: 200, height: 300 },
	workcanvas	: { left: 10, top: 50, width: 180, height: 80 },
	layers		: [],
	rotateStep	: 45,
	paddingText	: 6,
	imgCenter	: true,
	align		: 'block',//'block', //text
	cookieName	: 'text_layers_'+(is_front()?'front':'back'),
	onChange	: null,
	onEvent 	: null,
	
	init: function(id, src){
		var self = this;
		
		if ($.browser.msie && $.browser.version < 9) return this;
		
		//this.div = $('<div></div>');
		//$(id).append(this.div);
		this.div = $(id);
		this.div.css({ position: 'relative' }).addClass('render_text no_select');
		
		//событие на удаление слоя
		$('html').bind('keyup', function(e){
			if(e.keyCode == 46) {
				var layer = self.getActive();
				if (layer)
					self.handler('remove',layer);					
			}
		});
		
		//загружаем фоновую картинку
		this.img = new Image();		
		this.img.onload = function(){
			self.div.css({width: this.width + 'px', height: this.height + 'px'});
			
			//добавляем фон (майку)
			if (!self.no_background) {
				$(this).css({ position: 'absolute', left: '0px', top: '0px', width: this.width + 'px', height: this.height + 'px' });
				self.div.append($(this));
			}

			//создаём обромление рабочей области
			if (!self.workspace.border) {
				var l = (self.workspace.left?self.workspace.left:(this.width/2-self.workspace.width/2));
				var t = (self.workspace.top?self.workspace.top:(this.height/2-self.workspace.height/2));
				self.workspace.border = $('<div></div>').css({ border: '1px dashed #aaa', zIndex: 1, position: 'absolute', left: l + 'px', top: t + 'px', width: self.workspace.width+'px', height: self.workspace.height+'px' });
				self.div.append(self.workspace.border);
			}
			self.workspace.border.addClass('no_select');
			
			$('#no_resize_label').css('top', ($('#content-editor').position().top + self.workspace.top - $('#no_resize_label').height() - 1)+'px');
			
			if (self.imgCenter == true) {				
				self.div.css({ left: self.div.parent().width() / 2 - this.width / 2, top: self.div.parent().height() / 2 - this.height / 2 })
			}
			
			self.restore();
		}
		this.img.src = src;
		return this;
	},

	setWorkspace: function(p){
		this.workspace.left = p.left;
		this.workspace.top = p.top;
		this.workspace.width = p.width;
		this.workspace.height = p.height;
		var pc = { left: p.left, top: p.top, width: p.width, height: p.height };
		$(this.workspace.border).css(pc);
		pc.left+=1;
		pc.top+=1;
		$('#no_resize_label').css('top', ($('#content-editor').position().top + this.workspace.top - $('#no_resize_label').height() - 1)+'px');
		$(this.workspace.border).parent().find('.doubleBorder').css(pc);
	},
	
	setLayer: function(index, params, nodraw, fontSize){
		if (!this.layers[index]) return;
		for(var n in params)
			this.layers[index][n] = params[n];
		
		var f = this.layers[index];
		if (params.width || params.height) {
			this.layers[index].width = Math.max(f.width, 30);
			this.layers[index].height = Math.max(f.height, 15);
		}
		
		var f = this.layers[index];
		
		if (params.align && this.align == 'block' && !params.left) {
			if (params.align == 'left') f.left = params.left = 0;
			if (params.align == 'center') f.left = this.workspace.border.width()/2-f.border.width()/2;
			if (params.align == 'right') f.left = this.workspace.border.width()-f.border.width();
		}

		if (params.valign && this.align == 'block' && !params.top) {
			if (params.valign == 'top') f.top = params.top = 0;
			if (params.valign == 'center') f.top = params.top = this.workspace.border.height()/2-f.border.height()/2;
			if (params.valign == 'bottom') f.top = params.top = this.workspace.border.height()-f.border.height();
		}
		
		if (typeof params.left == 'number' || typeof params.top == 'number' || typeof params.width == 'number' || typeof params.height == 'number') {
			this.layers[index].border.css({left: f.left, top: f.top, width: f.width, height: f.height});
			var d = $(this.layers[index].canvas).attr({ width:f.width, height:f.height }).css({width: f.width, height: f.height});
			if (d.width() == 0) d.width(f.width);
			if (d.height() == 0) d.height(f.height);
		}
		
		if (nodraw) return;
		//this.draw(index);
		this.drawAndGlueFrame(this.layers[index], fontSize);
	},
	
	addLayer: function(index){ 
		var self = this;
		if (typeof this._padding == 'undefined') 
			this._padding = 0; 
		else {
			for(var i=0;i<this.layers.length;i++) 
				if (this.layers[i])
					this._padding = Math.max(this._padding, this.layers[i].border.position().top + this.layers[i].border.height());
			if (this.layers.length > 0) this._padding += 15;
		}
		
		var layer = { index: index, text: '', fontName: 'Verdana', fontSize: 20, color: 'red', rotate: 0, style: {}, align: 'center' };
		var index = index;
		
		//создаём область для рисования
		var b = this.workspace.border;//.position();

		if (this._padding+20 > b.height()) this._padding = 10;
		
		layer.border = $('<div></div>').css({ border: '1px dashed silver', zIndex: 2, position: 'absolute', left: /*(this.workcanvas.left+this._padding)*/+'0px', top: (/*this.workcanvas.top+*/this._padding)+'px', width: (b.width()-this.workcanvas.left/*-this._padding - 10*/)+'px', height: /*(b.height()-this.workcanvas.top-this._padding - 10)+'px'*/'15px' });
		//layer.border.addClass('preloader');
		layer.left = parseInt(layer.border.css('left'));
		layer.top = parseInt(layer.border.css('top'));
		layer.width = parseInt(layer.border.css('width'));
		layer.height = parseInt(layer.border.css('height'));
		var bb = $('<div></div>').css({ position: 'relative', width:'100%', height:'100%' });
		var bbb = $('<div></div><div></div><div></div><div></div>').css(
					{ 
						position: 'absolute', 
						width:'16px', height:'16px', 
						cursor: 'pointer' 
					}).click(function(e) {
						self.handler($(this).attr('action'), { index: index, event: e });
					});
		bb.append(bbb);
		layer.border.append(bb);
		layer.border.index = index;
		layer.border.click(function(){ self.setActiveLayer(index, true); return false;});
		
		layer.border.draggable({ 
			containment: this.workspace.border,
			start: function(event, ui) { return self.handler('draggable', {action: 'start', ui: ui, event: event, layer: layer.index }); },
			drag: function(event, ui) { return self.handler('draggable', {action: 'drag',ui: ui, event: event, layer: layer.index}); },
			stop: function(event, ui) { return self.handler('draggable', {action: 'stop',ui: ui, event: event, layer: layer.index}); }
		});
		
		//this.div.append(layer.border);
		this.workspace.border.append(layer.border);
		bbb.each(function(){
			var o = $(this);
			
			if (o.index() == 0 || o.index() == 2)
				o.mousedown(function(e){ self.handler($(this).attr('action'), { mouse: 'down', index: index, event: e }); return false;});
			
			switch (o.index()) {
				case 0: o.addClass('btn_lt').attr('action','move');break;
				case 1: o.addClass('btn_rt').attr('action','rotate');break;
				case 2: o.addClass('btn_lb').attr('action','resize');break;
				case 3: o.addClass('btn_rb').attr('action','remove');break;
			}
		});

		
		//инициализзируем канву для рисования текста		
		layer.canvas = $('<canvas></canvas>').attr({ width:layer.border.width(), height:layer.border.height() }).css({ position: 'absolute', left: '0px', top: '0px' });
		bb.append(layer.canvas);
		layer.canvas = layer.canvas[0];
		if ($.browser.msie && $.browser.version < 9) { if (typeof G_vmlCanvasManager != 'undefined') G_vmlCanvasManager.initElement(layer.canvas); } //костыль для ИЕ
		layer.context = layer.canvas.getContext('2d');
		//this.context.drawImage(this.img, 0, 0, this.width, this.height);
		
		this.layers[index] = layer;	
		if (typeof this.onEvent == 'function') this.onEvent('addLayer');
	},
	
	getActive: function(){
		for(var i=0;i<this.layers.length;i++)
		if (parseInt(this.layers[i].border.css('opacity')) == 1) return this.layers[i];
		return null;
	},
	
	parseAlignLayer: function(layer){
		if (layer.align == 'left' && layer.left > 1) layer.align = 'center';
		if (layer.valign == 'top' && layer.top > 1) layer.align = 'center';
	},
	
	setAlignLayer: function(horz, vert, vector, cycle){ 
		var l = this.getActive();
		if (typeof vector == 'undefined') vector = true; //true - right|buttom, false - left|top
		if (typeof cycle == 'undefined') cycle = true;
		
		if (l) {
			var g = {};
			
			this.parseAlignLayer(l);
			
			//if (!(l.align == 'left' || l.align == 'center' || l.align == 'right')) l.align = 'left';
			//if (!(l.valign == 'top' || l.valign == 'center' || l.valign == 'bottom')) l.align = 'top';			
			if (horz && vector && cycle) 
				switch(l.align) {
					case 'left': { g.align = 'center'; break; }
					case 'center': { g.align = 'right'; break; }
					case 'right': { g.align = 'left'; break; }
					default: g.align = 'right';
				}

			if (horz && vector && !cycle) 
				switch(l.align) {
					case 'left': { g.align = 'center'; break; }
					case 'center': { g.align = 'right'; break; }
					case 'right': { g.align = 'right'; break; }
					default: g.align = 'right';
				}
				
			if (horz && !vector && cycle) 
				switch(l.align) {
					case 'left': { g.align = 'right'; break; }
					case 'center': { g.align = 'left'; break; }
					case 'right': { g.align = 'center'; break; }
					default: g.align = 'left';
				}

			if (horz && !vector && !cycle) 
				switch(l.align) {
					case 'left': { g.align = 'left'; break; }
					case 'center': { g.align = 'left'; break; }
					case 'right': { g.align = 'center'; break; }
					default: g.align = 'left';
				}
				
			if (vert && vector && cycle)
				switch(l.valign) {
					case 'top': { g.valign = 'center'; break; }
					case 'center': { g.valign = 'bottom'; break; }
					case 'bottom': { g.valign = 'top'; break; }
					default: g.valign = 'top';
				}

			if (vert && vector && !cycle)
				switch(l.valign) {
					case 'top': { g.valign = 'center'; break; }
					case 'center': { g.valign = 'bottom'; break; }
					case 'bottom': { g.valign = 'bottom'; break; }
					default: g.valign = 'top';
				}

			if (vert && !vector && cycle)
				switch(l.valign) {
					case 'top': { g.valign = 'center'; break; }
					case 'center': { g.valign = 'top'; break; }
					case 'bottom': { g.valign = 'center'; break; }
					default: g.valign = 'top';
				}

			if (vert && !vector && !cycle)
				switch(l.valign) {
					case 'top': { g.valign = 'top'; break; }
					case 'center': { g.valign = 'top'; break; }
					case 'bottom': { g.valign = 'center'; break; }
					default: g.valign = 'top';
				}
				
			this.setLayer(l.index, g);
		}// else alert('Не выделен слой');
	},
	
	setActiveLayer: function(index, updateParam){
		for(var n in this.layers) 
		if (this.layers[n] && this.layers[n].border) {
			this.layers[n].border.css({ zIndex: '200', border: '1px dashed silver' });
			this.layers[n].border.find('div div').css('opacity', '0.6');
		}
		
		this.workspace.border.find('> div').css({ /*zIndex: '200', */border: '1px dashed silver' }).find('div div').css('opacity', '0.6');
		this.workspace.border.find('> div').css('opacity', '0.6');
		
		if (this.layers[index]) {		
			this.layers[index].border.css({ zIndex: '203', border: '1px dashed red' }).find('div div').css('opacity', '1');
			this.layers[index].border.css('opacity', '1');
		}
		
		if (updateParam == true) 
			this.property.setParams(this.layers[index], index);
	},
	
	hideLayer: function(index){
		if (!this.layers[index]) return;
		this.layers[index].border.hide();
	},
	
	showLayer: function(index){
		if (!this.layers[index]) return;
		this.layers[index].border.show();
	},

	countLayers: function(){
		var cnt = 0;
		for(var i=0;i<this.layers.length;i++)
			if (this.layers[i] && this.layers[i].border && this.layers[i].border.is(':visible')) cnt++;
		return cnt;
	},
	
	removeLayer: function(layer){
		if (typeof layer != 'object') layer = this.layers[parseInt(layer)];
		var i = 0;
		for(var n in this.layers) {
			if (this.layers[n] == layer) if (this.layers[n] != null && typeof this.layers[n] != 'undefined') {
				var inps = this.property.div.find('input');
				if (inps[this.layers[n].index]) {
					$(inps[this.layers[n].index]).val(""); 
					$(inps[this.layers[n].index]).css('border','1px solid #7C7C7C'); 
				}
				this.layers[n].border.remove();
				this.layers[n] = undefined;
				//this.layers.splice(i,1);
				//this.property.div.find('input').each(function() {
				//	if ($(this).index() == i) { $(this).val(""); $(this).css('border','1px solid #7C7C7C'); }
				//})
				break;
			}
			i++;
		}
		
		if (typeof this.onChange == 'function') this.onChange();
	},
	
	clearLayer: function(layer){
		//this.layers[index].context.translate(0, 0);
		//this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
		if (canvt) canvt.Clear(layer.canvas);
	},
	
	handler: function(event, params){
		var self = this;
		switch(event) {
			case 'rotate': {
				this.layers[params.index].rotate = this.layers[params.index].rotate - this.rotateStep;
				if (Math.abs(this.layers[params.index].rotate)>=360) this.layers[params.index].rotate = 0;
				
				//подтягиваем высоту блока под текст
				//this.draw(params.index);
				this.drawAndGlueFrame(this.layers[params.index]);
				//var h = this.getTextHeightC(this.layers[params.index]);
				//this.setLayer(params.index, {height: h}, true);
				if (typeof this.onChange == 'function') this.onChange();
				break;
			}
			case 'move': {
				if (params.mouse == 'down') {
					var layer = this.layers[params.index];
					this.layers[params.index].start = params.event;
					var index = params.index;
					
					$(window).unbind('mousemove').bind('mousemove',function(e){ self.handler('move', { mouse: 'move', index: index, event: e }); });
					$(window).unbind('mouseup').bind('mouseup',function(e){ self.handler('move', { mouse: 'up', index: index, event: e }); });
				}
				if (params.mouse == 'move') {
					var p = self.workspace.border.offset();
					var t = $(window).scrollTop();
					var e = this.layers[params.index].border.offset();
					var b = self.workspace.border;
					//var bb = self.workspace.border.offset();
					var offset = { X: this.layers[params.index].start.offsetX, Y: this.layers[params.index].start.offsetY };
					if (typeof offset.X == 'undefined') offset = {X:7,Y:7};
					var css = {left: params.event.clientX, top: params.event.clientY};					
					//var css = {width: params.event.clientX-e.left-offset.X, height: params.event.clientY-e.top-offset.Y};
					//debugger;
					//console.log(p.left +'<='+ css.left+' && '+p.top +'<='+ css.top +' && '+
					//			(p.left + b.width())+'>='+(css.left + this.layers[params.index].border.width())+' && '+
					//			(p.top + b.height())+'>='+(css.top + this.layers[params.index].border.height())
					//			); 
					if (p.left <= css.left && p.top-t <= css.top && 
						p.left + b.width() >= css.left + this.layers[params.index].border.width() && p.top - t + b.height() >= css.top + this.layers[params.index].border.height()) {
							css.left = css.left - p.left;// + b.position().left// - offset.X;
							css.top = css.top - p.top + t;// + b.position().top;// - offset.Y;
							//this.layers[params.index].border.css(css);
							//this.layers[params.index].left = parseInt(this.layers[params.index].border.css('left'));
							//this.layers[params.index].top = parseInt(this.layers[params.index].border.css('top'));
							this.setLayer(params.index, css, true);
					}
				}
				if (params.mouse == 'up') {
					$(window).unbind('mousemove').unbind('mouseup');
					this.layers[params.index].start = null;
					if (typeof this.onChange == 'function') this.onChange();
				}
				break;
			}
			case 'draggable': {
				if (params.action == 'start') {
					params.ui.helper.css({opacity: '0.3'});
				} else
				if (params.action == 'stop') {
					params.ui.helper.css({opacity: '1'});
					if (typeof this.onChange == 'function') this.onChange('dragstop');
				} else
				if (params.action == 'drag') {
					this.layers[params.layer].left = params.ui.position.left;
					this.layers[params.layer].top = params.ui.position.top;
					
					if (typeof this.onDraw == 'function') this.onDraw(this.layers[params.layer]);
				}
				//return true;
				break;
			}
			case 'resize': {
				if (params.mouse == 'down') {
					var layer = params.layer;
					this.layers[params.index].start = params.event;
					var index = params.index;
					$(window).unbind('mousemove').bind('mousemove',function(e){ self.handler('resize', { mouse: 'move', index: index, event: e }); });
					$(window).unbind('mouseup').bind('mouseup',function(e){ self.handler('resize', { mouse: 'up', index: index, event: e }); });
				}
				if (params.mouse == 'move') {
					var p = self.workspace.border.offset();
					var t = $(window).scrollTop();
					var e = this.layers[params.index].border.offset();
					var b = self.workspace.border;
					
					var offset = { X: this.layers[params.index].start.offsetX, Y: this.layers[params.index].start.offsetY };
					if (typeof offset.X == 'undefined') offset = {X:7,Y:7};
					
					//подтягиваем высоту блока под текст
					//var h = this.getTextHeightC(this.layers[params.index]);
					
					var css = {width: params.event.clientX-e.left-offset.X, height: params.event.clientY-e.top+t-offset.Y};
					if (p.left + b.width() >= e.left + css.width && p.top-t + b.height() >= e.top + css.height && css.height>=20 /*&& css.width>=(this.layers[params.index].textWidth>=20?this.layers[params.index].textWidth+(this.paddingText*2):50)*/) {
						//this.layers[params.index].border.css(css).find('canvas').attr(css);
						//this.layers[params.index].width = parseInt(this.layers[params.index].border.css('width'));
						//this.layers[params.index].height = parseInt(this.layers[params.index].border.css('height'));
						//delete css.height;
						this.setLayer(params.index, css, true);
					}
					
					//подтягиваем высоту блока под текст
					this.drawAndGlueFrame(this.layers[params.index], true);
					//this.draw(params.index);
					//var h = this.getTextHeightC(this.layers[params.index]);
					//this.setLayer(params.index, {height: h}, true);
					
					
				}
				if (params.mouse == 'up') {
					$(window).unbind('mousemove').unbind('mouseup');
					this.layers[params.index].start = null;
					if (typeof this.onChange == 'function') this.onChange();
				}
				break;
			}
			case 'remove': {
				if (confirm('Вы уверены, что хорите удалить надпись?')) {
					this.removeLayer(this.layers[params.index]);
					this.save();
					if (typeof this.onChange == 'function') this.onChange();
				}
			}
		}
		
		this.save();
		if (event != 'remove' && typeof params == 'object')
			this.draw(params.index);
		
		if (typeof this.onEvent == 'function') this.onEvent(event);		
		
		return true;
	},
	
	wrapText: function (layer) {
        var marginLeft = 5;
		var marginTop = 5;
		var lineHeight = 25;
		var words = layer.text.split(" ");
        var countWords = words.length;
        var line = "";
        for (var n = 0; n < countWords; n++) {
            var testLine = line + words[n] + " ";
            var testWidth = context.measureText(testLine).width;
            if (testWidth > maxWidth) {
                layer.context.fillText(line, marginLeft, marginTop);
                line = words[n] + " ";
                marginTop += lineHeight;
            }
            else {
                line = testLine;
            }
        }
        //canvt.Text(layer.context, layer.fontName, layer.fontSize, translate.left, translate.top, layer.text, 'center', align, false, layer.rotate);
		context.fillText(line, marginLeft, marginTop);
    },	
	
	TextWidth: function(l){
		var _w = l.border.width();
		var _fs = 7;
		var r = true;

		if ((l.border.position().left) + _w > this.workspace.border.width()) { //console.log('_w');
			_w = (this.workspace.border.width()-l.border.position().left-10);
			this.setLayer(l.index, {width:_w},false);
		}
		
		l.context.font = (l.style.italic?'italic ':'')+(l.style.bold?'bold ':'')+_fs+'px '+l.fontName;
		var w = l.context.measureText(l.text).width;

		if (Math.abs(_w - w) > 2 && _w > w) {
			while (Math.abs(_w - w) > 2 && _w > w){
				l.fontSize = ++_fs;
				this.draw(l.index);
				w = l.textWidth;
				
				if (_fs>this.property.maxFontSize || _fs<=0) break;
			}
			
			if (--_fs <= 5) _fs = 7;
		} else { _fs =  parseInt(l.fontSize); r = false; }
		
		if (l.fontSize <=8 && (l.border.position().left) + l.textWidth > this.workspace.border.width()) { //console.log('_w1');
			var g = l.border.position().left;
			var w = l.context.measureText(l.text).width;
			var _w =  this.workspace.border.width();
			while ((g+w) > _w && l.text.length > 0) {
				l.text = l.text.substr(0,l.text.length-1);
				//w = l.context.measureText(l.text).width;
				this.draw(l.index);
				w = l.textWidth;
			}
			//var r = w / l.text.length;
			//_w = (this.workspace.border.width()-l.border.position().left-10);			
			//this.setLayer(l.index, {width:_w},false);
		}
		
		this.property.setParams(l,l.index);
		return r;
	},
	
	getTextHeight: function(l){ 
		if (!this.textHeightSpan) {
			this.textHeightSpan = document.createElement("span");
			this.textHeightSpan.appendChild(document.createTextNode("height"));
			this.textHeightSpan.setAttribute('style', 'white-space: nowrap; display: inline;');
			document.body.appendChild(this.textHeightSpan);
		}
        this.textHeightSpan.style.fontName = l.fontName;
		this.textHeightSpan.style.fontSize = l.fontSize+'px';
		if (l.style.bold) this.textHeightSpan.style.fontWeight = 'bold';
        var height = this.textHeightSpan.offsetHeight;
        //document.body.removeChild(this.textHeightSpan);
        return height;	
	},

	getTextWidthC: function(l){ 
		var p = { w: l.border.width(), h: l.border.height() };

		var imgd = l.context.getImageData(0, 0, p.w, p.h);
		var pix = imgd.data;
		
		var lastDataRow = 0;
		var startDataRow = -1;
		for (var i = 0, n = pix.length; i < n; i += 4) { 
			var col = Math.floor((i/4) / p.w);
			var row = (i/4) - (col * p.w);
			if (pix[i]>0 || pix[i+1]>0 || pix[i+2]>0 || pix[i+3] > 0) {
				//if (startDataRow < 0) startDataRow = row;
				if (startDataRow < 0 || startDataRow > row) startDataRow = row;
				//lastDataRow = row;
				if (lastDataRow < row) lastDataRow = row;
			}
			//if (Math.abs(lastDataRow - startDataRow) > 5) break;
		}
		return lastDataRow-startDataRow+13;
	},
	
	getTextHeightC: function(l){ 
		//var p = { w: l.border.width(), h: l.border.height() };
		var c = $(l.canvas);
		var p = { w: c.width(), h: c.height() };
		//console.log(l.height+'x'+p.h);
		var imgd = l.context.getImageData(0, 0, (p.w?p.w:l.width), (p.h?p.h:l.height));
		var pix = imgd.data;
		
		var lastDataCol = 0;
		var startDataCol = -1;
		for (var i = 0, n = pix.length; i < n; i += 4) { 
			var col = Math.floor((i/4) / p.w);
			if (pix[i]>0 || pix[i+1]>0 || pix[i+2]>0 || pix[i+3] > 0) {
				//if (startDataCol < 0) startDataCol = col;
				if (startDataCol < 0 || startDataCol > col) startDataCol = col;
				//lastDataCol = col;
				if (lastDataCol < col) lastDataCol = col;
			}
			//if (Math.abs(lastDataCol - startDataCol) > 5) break;
		}
		l.startDataCol = startDataCol;//console.log(startDataCol+'x'+lastDataCol);
		return lastDataCol-startDataCol+20;//(lastDataCol>0?lastDataCol+5:p.h);
	},

	drawAndGlueFrame: function(l, fontSize){		
		this.draw(l);
		this.glueFrame(l, fontSize);
		this.draw(l, l.startDataCol);
	},
	
	glueFrame: function(l, fontSize){
		if (fontSize)
			var tws = this.TextWidth(l);
		
		var p = {};
		
		//подтягиваем высоту текста
		p.height = this.getTextHeightC(l);		
		//console.log(p.height);
		//подтягиваем ширину текста
		if (!fontSize || typeof tws != 'undefined') p.width = l.textWidth;
		//var w = this.getTextWidthC(l);		
		//console.log(w);
		//if (!fontSize || typeof tws != 'undefined') p.width = this.getTextWidthC(l);
		//console.log((l.left+p.width)+'>'+this.workspace.border.width());

		//проверим, если выходит за границы - включаем уменьшение шрифта
		if (!fontSize) { 
			if ((l.left+p.width) > this.workspace.border.width()) {
				//console.log('dfdfdf');
				this.glueFrame(l, true);				
				return;
			}
		}
		
		//устанавливаем ширину и высоту текста
		this.setLayer(l.index, p, true);
	},
	
	draw: function(p, top){
		if (typeof p == 'undefined') return;
		var l = (p && p.border?p:this.layers[p]);		
		if (!l.text || l.text.length == 0) return;
		//console.log(l.text);
		
		//подтягиваем высоту блока под текст
		//var h = this.getTextHeight(l);
		//подтягиваем высоту блока под текст
		//var h = this.getTextHeightC(l);		
		//this.setLayer(l.index, {height: h}, true);

		//console.log(h);
		
		//выровняем ширину надписи по рамке
		//this.TextWidth(l);
		
		//очищаем полотно
		this.clearLayer(l);

		//инициализируем переменные
		//var wc = $(l.canvas);
		//var offset = wc.offset();
		var translate = {left: (l.width / 2 - this.paddingText/2)+this.paddingText/2, top: l.height / 2}
		var align = l.align;

		//translate.left = this.paddingText;
		
		//выравнивание
		if (this.align == 'text') {
			if (l.align) {
				if (l.align == 'left') { translate.left = this.paddingText/2; }
				if (l.align == 'right') { translate.left = l.width - this.paddingText; }
			}
		} else { 
			if (l.rotate == 0) { 
				//translate.left = 0; 
				//translate.top -= l.fontSize/5.6; 
				//translate.top = this.startDataCol;
				//if (top && top > 0) translate.top = top-5;
				align = 'top';
			} else
			align = 'center';
			
			//align = 'left'; /*translate.left = this.paddingText;*/ 
		}
	
		//позиционирование
		//l.context.translate(translate.left, translate.top);

		//поворот
		if (l.rotate) {
			//this.context.save();
			//this.context.rotate(this.layers[0].rotate * Math.PI/180);
			//this.context.restore();
			//this.context.transform(translate.left + Math.cos(this.layers[0].rotate), translate.top + Math.sin(this.layers[0].rotate), -Math.sin(this.layers[0].rotate), Math.cos(this.layers[0].rotate), 0, 0);
		}
		
		//this.context.font = 'italic bold 30px sans-serif';
		//this.context.textBaseline = 'center';
		
		//цвет текста
		l.context.fillStyle = l.color;
		
		//перенос текста
		//this.wrapText(l);
		
		canvt.bold = l.style.bold;
		canvt.italic = l.style.italic;
		canvt.Text(l.context, l.fontName, l.fontSize, translate.left, translate.top, l.text, 'center', 'center', false, l.rotate);
		l.textWidth = canvt.measureTextWidth;
		
		//подтягиваем высоту блока под текст
		//l.context.save();
		//var h = this.getTextHeightC(l);
		//this.setLayer(l.index, {height: h-4}, true);
		//l.context.restore();
		//console.log(h);

		//canvt.Text(l.context, l.fontName, l.fontSize, translate.left, translate.top, l.text, 'center', align, false, l.rotate);
		
		//this.context.fillText(this.layers[0].text, 0, 0);
		//this.layers[0].metrics = this.context.measureText(this.layers[0].text);		
			
		this.save();
	},
	
	save: function(){
		if (this.restoring) return;
		
		var s = '[';
		var i = 0;
		for(var n in this.layers) {
			var l = this.layers[n];
			if (typeof l == 'undefined') continue;
			
			s += (s.length>1?',':'') + '{';
			//console.log(l.border.position().left);
			s += 'left: ' + l.border.position().left + ',';
			s += 'top: ' + l.border.position().top + ',';
			s += 'startDataCol: ' + (typeof l.startDataCol == 'undefined' || l.startDataCol == null?0:l.startDataCol) + ',';			
			s += 'width: ' + l.border.width() + ',';
			s += 'height: ' + l.border.height() + ',';
			s += 'text: "' + l.text + '",';
			s += 'fontName: "' + l.fontName + '",';
			s += 'fontSize: "' + l.fontSize + '",';
			s += 'color: "' + l.color + '",';
			s += 'style: [' + (l.style.bold?'"bold"':'') + (l.style.italic?(l.style.bold?',':'')+'"italic"':'') + '],';
			s += 'align: "' + (l.align?l.align:'') + '",';
			s += 'valign: "' + (l.valign?l.valign:'') + '",';
			s += 'rotate: "' + l.rotate + '"';
			
			s += '}';
			
			i++;
		}
		s += ']';
		
		if (typeof this.onEvent == 'function') this.onEvent('save');
		$.cookie(this.cookieName, s, { expires: 1, path: "/" }); 
	},

	isLayers: function(){
		if (this.layers.length > 0) return true;
		
		var d = $.cookie(this.cookieName);
		if (typeof d == 'string' && d.length > 0) {
			try{ 
				d = eval(d);
				return (d.length>0);
			}catch(e) { }
		}
		return false;
	},
	
	restore: function(side){
		this.restoring = true;
	
		if (typeof side == 'string' && side != this.cookieName) {
			this.cookieName = 'text_layers_'+side;
			this.restore();	
			return;
		}
		
		for(var i=0;i<this.layers.length;i++)
			this.removeLayer(this.layers[i]);
		
		this._padding = 0;
		
		//$(this.workspace.border).hide();
		try {
		var d = $.cookie(this.cookieName);
		if (typeof d == 'string' && d.length > 0) {
			d = eval(d);
			for(var i=0;i<d.length;i++) {
				var inpts = this.property.div.find('input');
				if (i+1 > inpts.length) 
					this.property.addText(d[i].text);
				else $(inpts[i]).val(d[i].text);
				this.addLayer(i); //debugger;
				//delete d[i].width;
				//delete d[i].height;
				this.setLayer(i, d[i], false, false);
				this.setActiveLayer(i);
				if (i == 0) $(inpts[i]).focus();
			}
		}
		
		//this.save();//нужно, чтобы startDataCol прописалось в куку для заказа
		if (typeof this.onEvent == 'function') this.onEvent('restore');
		
		}finally {	this.restoring = false; /*$(this.workspace.border).show();*/ }
	},
	
	clear: function(){
		$.cookie(this.cookieName, null);
		$.cookie('text_layers_back', null);
		$.cookie('text_layers_front', null);
	},
	
	order: function(side){
		var cookieSave = this.cookieName;
		if (typeof side == 'string' && side != this.cookieName) {			
			this.cookieName = 'text_layers_'+side;
			this.restore();			
		}
		
		var self = this;
		var s = [];		

		//прорисовываем слои с текстом
		var i = 0;
		for(var n in this.layers) {
			var l = this.layers[i];
			if (typeof l == 'undefined') continue;
			
			var v = {
				left: l.border.position().left,
				top:  l.border.position().top,
				offsetTopText: (l.startDataCol?l.startDataCol:0),
				text: l.text,
				fontName: l.fontName,
				fontSize: l.fontSize,
				color: l.color,
				style: [],
				align: l.align,
				rotate: l.rotate,
			}

			if (l.style.bold)
				v.style.push('bold');
			if (l.style.italic)
				v.style.push('italic');
			s.push(v);
			
			i++;
		}
		
		var _side = /^text_layers_(\w+)/.exec(this.cookieName)[1];
		
		this.clear();
		if (cookieSave != this.cookieName) {
			this.cookieName = cookieSave;
			this.restore();
		}
		return { side: _side, layers: s };
	},
	
	order2: function(side){
		var cookieName = (typeof side == 'string'?'text_layers_'+side:this.cookieName);
		var s = [];
		var d = $.cookie(cookieName);
		
		if (typeof d == 'string' && d.length > 0) {
			d = eval(d);
			for(var i=0;i<d.length;i++) {
				if (typeof d[i] == 'undefined' || d[i] == null) continue;
				s.push({
					left: d[i].left,
					top:  d[i].top,
					width: d[i].width,
					height:  d[i].height,
					offsetTopText: (typeof d[i].startDataCol == 'undefined'?0:d[i].startDataCol),
					text: d[i].text,
					fontName: d[i].fontName,
					fontSize: d[i].fontSize,
					color: d[i].color,
					style: d[i].style,
					align: (d[i].align?d[i].align:''),
					valign: (d[i].valign?d[i].valign:''),
					rotate: d[i].rotate,
				});
			}
		}
		
		var _side = /^text_layers_(\w+)/.exec(cookieName)[1];
		
		return { side: _side, layers: s };
	},
	
	
	property: {
		maxFontSize: 110,
		countTexts: 4,
		//fontDefault: 'Times New Roman',
		fontDefault: 'Alial',
		sizeDefault: 14,
		colorDefault: '#000000',//'#1454f9',
		//fonts: ['Antiqua','Arial','Avqest','Blackletter','Calibri','Comic Sans','Courier','Decorative','Fraktur','Frosty','Garamond','Georgia','Helvetica','Impact','Minion','Modern','Monospace','Palatino','Roman','Script','Swiss','Times New Roman','Verdana'],
		//fonts: ['Aharoni','Andalus','Angsana New','Angsana New','AngsanaUPC','Aparajita','Arabic Typesetting','Arial','Arial Black','Arial Narrow','Arial Unicode MS','Batang','BatangChe','Book Antiqua','Book Antiqua','Bookman Old Style','Bookman Old Style','Bookshelf Symbol 7','Browallia New','BrowalliaUPC','Calibri','Cambria','Cambria Math','Candara','Century','Century Gothic','Comic Sans MS','Consolas','Constantia','Corbel','Cordia New','CordiaUPC','Courier','Courier New','DFKai-SB','DaunPenh','David','DilleniaUPC','DokChampa','Dotum','DotumChe','Ebrima','Estrangelo Edessa','EucrosiaUPC','Euphemia','FangSong','Fixedsys','FrankRuehl','Franklin Gothic Medium','FreesiaUPC','Gabriola','Garamond','Gautami','Georgia','Gisha','Gulim','GulimChe','Gungsuh','GungsuhChe','Haettenschweiler','Impact','IrisUPC','Iskoola Pota','JasmineUPC','KaiTi','Kalinga','Kartika','Khmer UI','KodchiangUPC','Kokila','Lao UI','Latha','Leelawadee','Levenim MT','LilyUPC','Lucida Console','Lucida Sans Unicode','MS Gothic','MS Mincho','MS Outlook','MS PGothic','MS PMincho','MS Reference Sans Serif','MS Reference Specialty','MS Sans Serif','MS Serif','MS UI Gothic','MT Extra','MV Boli','Malgun Gothic','Mangal','Marlett','Meiryo','Meiryo UI','Microsoft Himalaya','Microsoft JhengHei','Microsoft New Tai Lue','Microsoft PhagsPa','Microsoft Sans Serif','Microsoft Tai Le','Microsoft Uighur','Microsoft YaHei','Microsoft Yi Baiti','MingLiU','MingLiU-ExtB','MingLiU_HKSCS','MingLiU_HKSCS-ExtB','Miriam','Miriam Fixed','Modern','Mongolian Baiti','Monotype Corsiva','MoolBoran','NSimSun','Narkisim','Nyala','PMingLiU','PMingLiU-ExtB','Palatino Linotype','Plantagenet Cherokee','Raavi','Rod','Roman','Sakkal Majalla','Script','Segoe Condensed','Segoe Print','Segoe Script','Segoe UI','Segoe UI Light','Segoe UI Semibold','Segoe UI Symbol','Shonar Bangla','Shruti','SimHei','SimSun','SimSun-ExtB','Simplified Arabic','Simplified Arabic Fixed','Small Fonts','Sylfaen','Symbol','System','Tahoma','Terminal','Times New Roman','Traditional Arabic','Trebuchet MS','Tunga','Utsaah','Vani','Verdana','Vijaya','Vrinda','Webdings','Wingdings','Wingdings 2','Wingdings 3'],
		fonts: ['Arial','Arial Black','Arial Narrow','Book Antiqua','Century Gothic','Comic Sans MS','Courier New','Franklin Gothic Medium','Georgia','Impact','Lucida Console','Lucida Sans Unicode','Microsoft Sans Serif','Prosto','Palatino Linotype','Sylfaen','Tahoma','Times New Roman','Trebuchet MS','Verdana','Webdings','Wingdings'],		sizes: [7,9,10,11,12,13,14,15,16,17,18,20,22,24,26,28,32,34,36,38,40],
		align: ['left','center','right'],		
		
		init: function(id){
			var self = this;
			this.div = $(id);
			
			//создаём компоненты для ввода текста
			//var k = $('<div class="title"><span>добавить</span></div>');
			this.inputs = $('<div class="inputs"></div>');
			//k.find('span').addClass('btnAdd').click(function(){ self.addText(); });
			//this.div.append(k);
			this.div.append(this.inputs);
			for(var i=0;i<this.countTexts;i++) this.addText();
			
			//создаём компонент для выбора имени шрифта
			this.fontName = $('<select></select>').addClass('name');
			for(var i=0;i<this.fonts.length;i++) {
				var o = new Option(this.fonts[i], this.fonts[i]);
				if (this.fontDefault == this.fonts[i]) o.selected = true;
				this.fontName.append(o);
			}
			this.div.append(this.fontName);
			this.fontName.change(function(){ self.handler('fontName', $(this).val()); });
			
			//компонент для выбора цвета шрифта
			this.cp = $('<div class="color"><div class="g">Цвет: </div><div class="h" id="colorSelector"><div></div></div></div>').find('#colorSelector').attr('color',this.colorDefault);
			this.div.append(this.cp.parent());
			
			//создаём компонент для выбора размера шрифта
			this.fontSize = $('<select></select>').addClass('size');
			/*for(var i=0;i<this.sizes.length;i++) {
				var o = new Option(this.sizes[i]+'pt', this.sizes[i]);
				if (parseInt(this.sizeDefault) == parseInt(this.sizes[i])) o.selected = true;
				this.fontSize.append(o);
			}*/
			//for(var i=7;i<77;i++) {
			for(var i=7;i<this.maxFontSize;i++) {
				var o = new Option(i+'pt', i);
				if (parseInt(this.sizeDefault) == parseInt(i)) o.selected = true;
				this.fontSize.append(o);
			}
			this.div.append(this.fontSize);
			this.fontSize.change(function(){ self.handler('fontSize', $(this).val()); });
			
			//кнопки для установки стиля
			this.fontBold = $('<div></div>').addClass('bold');
			this.fontItalic = $('<div></div>').addClass('italic');
			this.fontLeft = $('<div></div>').addClass('left');
			this.fontCenter = $('<div></div>').addClass('center').addClass('select');
			this.fontRight = $('<div></div>').addClass('right');
			this.div.append($('<div></div>').addClass('style').append(this.fontBold).append(this.fontItalic).append(this.fontLeft).append(this.fontCenter).append(this.fontRight));
			
			this.style = this.div.find('.style');
			this.style.find('div').click(function(){ 
				if ($(this).index() > 1) {
					$(this).parent().find('div').each(function(){ if ($(this).index() > 1) $(this).removeClass('select'); });
					$(this).addClass('select');
				}
				else { 
					if ($(this).hasClass('select'))
						$(this).removeClass('select'); 
					else
						$(this).addClass('select'); 
				}
				
				self.handler('style', { action: $(this).attr('class'), checked: $(this).hasClass('select') });
			});
												
			this.cp.com = cp.init(this.cp, this.colorDefault, function(hex){ $('#colorSelector').attr('color', hex); self.handler('fontColor', hex); });
			
		},
			
		addText: function(text){
			var self = this;
			var i = this.inputs.find('input').length;
			var inp = $('<input placeholder="Строка '+(i+1)+'" value="'+(text?text:"")+'" />');
			
			var f = function(){ self.handler('text', $(this)); };
			inp/*.keypress(f)*/.keyup(f).change(f).focus(function(){
				self.setActive($(this).index());
				ct.setActiveLayer($(this).index(), true);
				return false;
			});
			
			this.inputs.append(inp);			
		},
		
		getParams: function(index){
			var align = 'center';
			if (this.fontLeft.hasClass('select'))
				align = 'left';
			else if (this.fontRight.hasClass('select'))
				align = 'right';
			
			return { 
						text: $(this.div.find('input')[index]).val(), 
						fontName: this.fontName.val(), 
						fontSize: /(\d+)/.exec(this.fontSize.val())[1], 
						color: this.cp.attr('color'), 
						//rotate: 0, 
						style: { bold: this.fontBold.hasClass('select'), italic: this.fontItalic.hasClass('select') },
						align: align
					};
		},
		
		getActive: function(){
			var index = 0;
			this.div.find('input').each(function(){ 
				if ($(this).hasClass('select')) index = $(this).index();
			});
			return index;
		},

		setActive: function(index){
			var d = this.div.find('input').each(function(){ 
				$(this).removeClass('select');
			});
			$(d[index]).addClass('select');
		},
		
		setParams: function(layer, index){			
			this.style.find('div').removeClass('select');

			if (!layer) {
				this.fontCenter.addClass('select');
				this.fontName.val(this.fontDefault);
				this.fontSize.val(this.sizeDefault);
				this.cp.val(this.colorDefault);
				this.cp.attr('color', this.colorDefault);
				this.cp.com.setColor(this.colorDefault);				
				return;
			}

			if (layer.style.bold) this.fontBold.addClass('select');
			if (layer.style.italic) this.fontItalic.addClass('select');			
			
			if (layer.align == 'left') this.fontLeft.addClass('select');			
			if (layer.align == 'center') this.fontCenter.addClass('select');			
			if (layer.align == 'right') this.fontRight.addClass('select');			
			
			this.fontName.val(layer.fontName);
			this.fontSize.val(layer.fontSize);
			this.cp.val(layer.color);
			this.cp.attr('color', layer.color);
			this.cp.com.setColor(layer.color);
			//$('#colorSelector div').css('backgroundColor', layer.color);			
			
			if (typeof this.cp.onChange == 'function') this.cp.onChange();
			this.setActive(index);
		},
		
		handler: function(event, params){
			switch(event) {
				case 'text': {
					var idx = params.index();
					if (!ct.layers[idx])
						ct.addLayer(idx);
					ct.setLayer(idx, this.getParams(idx), false, false);
					ct.setActiveLayer(idx);
					break;
				}
				case 'fontName': 
				case 'fontSize': 
				case 'fontColor': 
				case 'style': {
					var idx = this.getActive();
					ct.setLayer(idx, this.getParams(idx));
					ct.setActiveLayer(idx);
					break;
				}			
			}

		}
	}
}