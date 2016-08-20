/********************************************************
* Date: 20.06.2012
* Author: Girman Evgeniy
* E-Mail: girman.evg@gmail.com
* About : Модуль для управления миниатюрами на майке
*********************************************************/

//компонент|модуль по размещению картинки.
imagemove = function(p) {
	this.workspace		= { left: null, top: null, width: 200, height: 300 };
	this.layers			= [];
	this.rotateStep		= 90;
	this.margin			= {left:0, top:0};
	this.no_background	= false;
	this.imgCenter		= false;
	this.cookieName		= 'image_layers_'+(is_front()?'front':'back');
	this.urlSaveChanged	= '';
	this.align			= 'block';
	this.initImg 		= '/ajax/initImg/?url=';
	this.no_resize_label = '#no_resize_label';
	this.small_resize_label = '#small_resize_label';
	//this.url_delete = '/ajax/customize_deleteImg/'+good_id+'/';
	this.draggableInBox = true;	
	this.canvasImg = true;
	this.onEvent = null;
	this.onChange = null;
	this.onDraw = null;
	this.onDeleteLayer = null;
	
	this.init = function(p){
		var self = this;
		for(var n in p) this[n] = p[n];
		
		if (!isNaN(this.src_resize_koef)) this.src_resize_koef = parseFloat(this.src_resize_koef);
		
		this.div = $(this.id);
		this.div.css({ position: 'relative' }).addClass('render_image no_select');
		
		//событие на удаление слоя
		$('html').bind('keyup', function(e){
			if(e.keyCode == 46) {
				var layer = self.getActive();
				if (layer)
					self.handler('remove',layer);
			}
		});
		
		if (!this.workspace.border) {
			var l = (this.workspace.left?this.workspace.left:(200/2-this.workspace.width/2));
			var t = (this.workspace.top?this.workspace.top:(200/2-this.workspace.height/2));
			this.workspace = {left:this.workspace.left,top:this.workspace.top,width:this.workspace.width,height:this.workspace.height};
			this.workspace.border = $('<div></div>').css({ border: '1px dashed #555', zIndex: 1, position: 'relative', left: l + 'px', top: t + 'px', width: this.workspace.width+'px', height: this.workspace.height+'px' });
			//добавим навигационные кнопки
			var b = $('<div></div><div></div><div></div><div></div>')
			.addClass('im_btn')
			.css({ position: 'absolute', width:26, height:26, cursor:'pointer', zIndex:2 })
			/*.click(function(){
				var _param = {align:{}};
				var a = self.layers[0].align;
				_param.align.horizontal = a.horizontal;
				_param.align.vertical = a.vertical;
				switch($(this).index()) {
					case 0: { 
						if (a.vertical == 'bottom') 
							_param.align.vertical = 'center';
						else if (a.vertical == 'center') 
							_param.align.vertical = 'top';
						break;
					}
					case 1: { 
						if (a.horizontal == 'left') 
							_param.align.horizontal = 'center';
						else if (a.horizontal == 'center') 
							_param.align.horizontal = 'right';
						break;
					}
					case 2: { 
						if (a.vertical == 'top') 
							_param.align.vertical = 'center';
						else if (a.vertical == 'center') 
							_param.align.vertical = 'bottom';
						break;
					}
					case 3: { 
						if (a.horizontal == 'right')
							_param.align.horizontal = 'center';
						else if (a.horizontal == 'center')
							_param.align.horizontal = 'left';
						break;
					}
				}
				self.setLayer(0, _param);
				self.save();
				if (self.onEvent) self.onEvent('draggable');
				self.preSave();
				
			});*/
			this.div.append(b);
			this.div.append(this.workspace.border);
		}
		this.workspace.border.addClass('no_select');
		
		var load = function(){
			self.div.css({width: this.width + 'px', height: this.height + 'px'});

			//добавляем фон (майку)
			if (!self.no_background) {
				$(this).css({ position: 'absolute', left: '0px', top: '0px', width: this.width + 'px', height: this.height + 'px' });
				self.div.append($(this));
			}
			
			//создаём обромление рабочей области
			/*if (self.workspace.border) {
				var l = (self.workspace.left?self.workspace.left:(this.width/2-self.workspace.width/2));
				var t = (self.workspace.top?self.workspace.top:(this.height/2-self.workspace.height/2));
				self.workspace.border.css({ left: l + 'px', top: t + 'px', width: self.workspace.width+'px', height: self.workspace.height+'px' });
			}*/
			
			//var b = self.workspace.border;
			//var bp = b.position();
			var b = {left: l, top: t, width: self.workspace.width, height: self.workspace.height};
			$(self.div).find('.im_btn')
			.css({ position: 'absolute', width:26, height:26, cursor:'pointer' })
			.each(function(){
				var pad = 10;
				//$(this).css({background: 'url(bottons.png)no-repeat -'+$(this).index()*$(this).width()+'px top'});
				$(this).addClass('layer_navigate_'+(parseInt($(this).index())+1));
				switch($(this).index()) { 
					case 0: { $(this).css({left:b.left+(b.width/2-$(this).width()/2), top:b.top-$(this).height()-pad }); break;}
					case 1: { $(this).css({left:b.left+b.width+pad, top:b.top+b.height/2-$(this).height()/2 }); break;}
					case 2: { $(this).css({left:b.left+b.width/2-$(this).width()/2, top:b.top+b.height+pad }); break;}
					case 3: { $(this).css({left:b.left-$(this).width()-pad, top: b.top+b.height/2-$(this).height()/2 }); break;}
				}
			})
			
			if (self.imgCenter == true) {				
				self.div.css({ left: self.div.parent().width() / 2 - this.width / 2, top: self.div.parent().height() / 2 - this.height / 2 })
			}
			
			if (typeof self.afterInit == 'function') self.afterInit();
			self.restore();			
		}
		
		//загружаем фоновую картинку
		if (this.src) {
			this.img = new Image();		
			this.img.onload = load;
			this.img.src = this.src;
		} else load();
		
		return this;
	},
	
	this.setLayer = function(index, params, nodraw){
		if (!params) params={};
		if (!this.layers[index]) return;
		for(var n in params)
			//if (n != 'align')
			this.layers[index][n] = params[n];

		var f = this.layers[index];
			
		/*if (params.align) {
			for(var n in params.align) this.layers[index].align[n] = params.align[n];
			var l = f.border;
			var s = this.workspace.border;			
			try {
			var lp = $(f.border).position();
			}catch(e) {var lp = {left:f.left, top:f.top};}
			
			if (f.align.vertical == 'top') 
				f.top = 0;
			if (f.align.vertical == 'center') 
				f.top = parseInt(s.height()/2-l.height()/2);
			if (f.align.vertical == 'bottom') 
				f.top = parseInt(s.height()-l.height());

			if (f.align.horizontal == 'left') 
				f.left = 0;
			if (f.align.horizontal == 'center') 
				f.left = parseInt(s.width()/2-l.width()/2);
			if (f.align.horizontal == 'right') 
				f.left = parseInt(s.width()-l.width());				
		}*/

		if (params.align && this.align == 'block' && !params.left) {
			if (params.align == 'left') f.left = params.left = 0;
			if (params.align == 'center') params.left = f.left = this.workspace.border.width()/2-f.border.width()/2;
			if (params.align == 'right') params.left = f.left = this.workspace.border.width()-f.border.width();
		}

		if (params.valign && this.align == 'block' && !params.top) {
			if (params.valign == 'top') f.top = params.top = 0;
			if (params.valign == 'center') f.top = params.top = this.workspace.border.height()/2-f.border.height()/2;
			if (params.valign == 'bottom') f.top = params.top = this.workspace.border.height()-f.border.height();
		}
		
		if (typeof params.left == 'number' || typeof params.top == 'number') {
			this.layers[index].border.css({left: f.left, top: f.top });
		}
		
		if (typeof params.width == 'number' || typeof params.height == 'number') {
			this.layers[index].border.css({ width: f.width, height: f.height });
			$(this.layers[index].canvas).attr({ width:f.width, height:f.height });
		}
		
		if (params.src) {
			var self = this;			
			this.layers[index].border.addClass('preloader');
			//this.layers[index].img.onload = function(){ self.layers[index].border.removeClass('preloader'); };
			//this.layers[index].img.onload = null;
			this.layers[index].img.params = params;
			this.layers[index].img.src = params.src;
		}

		if (params.img) {
			var self = this;			
			this.layers[index].border.addClass('preloader');
			//this.layers[index].img.onload = function(){ self.layers[index].border.removeClass('preloader'); };
			//this.layers[index].img.onload = null;
			params.img.onload = this.layers[index].img.onload;
			delete this.layers[index].img;
			this.layers[index].img = params.img;
			this.layers[index].img.params = params;
			if (typeof this.layers[index].img.onload == 'function') this.layers[index].img.onload();
		}
		
		if (!nodraw) this.draw(index);
		if (typeof params.afterLoad == 'function') params.afterLoad();
	},
	
	this.addLayer = function(src, params){
		var self = this;
		if (typeof this._padding == 'undefined') this._padding = 0; else this._padding += 10;
		if (!params) params={}
		
		//проверим на существования такого слоя
		for(var i=0;i<this.layers.length;i++)
			if (this.layers[i].src == src) return;
		
		var layer = { index: this.layers.length, src: src, rotate: 0, align: { horizontal:'center', vertical:'top' } };
		var index = this.layers.length;
		
		//создаём область для рисования
		var b = this.workspace.border;
		
		var p = {
					left: Math.max((params.left?params.left:0),0),
					top: Math.max((params.top?params.top:0),0),
					width: Math.min((params.width?params.width:b.width()),b.width()),
					height: Math.min((params.height?params.height:b.height()),b.height())
				};
		
		if (!this.draggableInBox && params.width) 
			p.width = params.width;
		if (!this.draggableInBox && params.height) 
			p.height = params.height;
		
		if (!params.left && params.width) {
			p.left = this.workspace.border.width()/2 - params.width/2;			
		}
		
		//валидация, чтобы картинка влазила
		if (this.draggableInBox) {
			if ((p.left+p.width) > b.width()) p.left = b.width() - p.width;
			if ((p.top+p.height) > b.height()) p.left = b.height() - p.height;
		}
		
		layer.border = $('<div></div>').css({ border: '1px dashed silver', zIndex: 2, left: p.left+'px', top: p.top+'px', width: p.width+'px', height: p.height+'px' });
		layer.border.addClass('preloader');
		layer.left = parseInt(layer.border.css('left'));
		layer.top = parseInt(layer.border.css('top'));
		layer.width = parseInt(layer.border.css('width'));
		layer.height = parseInt(layer.border.css('height'));
		layer.maxWidth = parseInt(params.maxWidth?params.maxWidth:layer.width);
		layer.maxHeight = parseInt(params.maxHeight?params.maxHeight:layer.height);
		layer.border.click(function(){ self.setActiveLayer(layer.index); });

		params.left = layer.left;
		params.top = layer.top;
		params.width = layer.width;
		params.height = layer.height;
		params.maxWidth = layer.maxWidth;
		params.maxHeight = layer.maxHeight;
		
		//добавляем кнопки
		var bb = $('<div></div>').css({ position: 'relative', width:'100%', height:'100%' });
		var bbb = $('<div></div><div></div><div></div><div></div>').css(
					{ 
						position: 'absolute', 
						width:'16px', height:'16px', 
						cursor: 'pointer',
						zIndex: 199 
					}).click(function(e) {
						return self.handler($(this).attr('action'), { index: index, event: e });
					});
		bb.append(bbb);
		layer.border.append(bb);
		this.workspace.border.append(layer.border);
		bbb.each(function(){
			var o = $(this);
			
			if (o.index() == 0 || o.index() == 2)
				o.mousedown(function(e){ self.handler($(this).attr('action'), { mouse: 'down', index: index, event: e }); return false;});
			
			switch (o.index()) {
				case 0: o.addClass('btn_lt').css({left:'-6px', top:'-6px'}).attr('action','move');break;
				case 1: o.addClass('btn_rt').css({right:'-6px', top:'-6px'}).attr('action','rotate');break;
				case 2: o.addClass('btn_lb').css({right:'-6px', bottom:'-6px'}).attr('action','resize').css('cursor','default');break;
				case 3: o.addClass('btn_rb').css({left:'-6px', bottom:'-6px'}).attr('action','remove');break;
			}
		});		
		
		//сделаем наш слой таскаемым и изменяемым в размере
		if (this.draggableInBox)
			layer.border.draggable({ 
				containment: (typeof this.draggableInBox == 'boolean'?"parent":this.draggableInBox),
				//containment: [this.workspace.border.position().left,this.workspace.border.position().top,this.workspace.border.width(),this.workspace.border.height()],
				start: function(event, ui) { return self.handler('draggable', {action: 'start', ui: ui, event: event, layer: layer.index }); },
				drag: function(event, ui) { return self.handler('draggable', {action: 'drag',ui: ui, event: event, layer: layer.index}); },
				stop: function(event, ui) { return self.handler('draggable', {action: 'stop',ui: ui, event: event, layer: layer.index}); }
			})
		else
			layer.border.draggable({ 
				containment: "parent",
				//containment: [this.workspace.border.position().left,this.workspace.border.position().top,this.workspace.border.width(),this.workspace.border.height()],
				start: function(event, ui) { return self.handler('draggable', {action: 'start', ui: ui, event: event, layer: layer.index }); },
				drag: function(event, ui) { return self.handler('draggable', {action: 'drag',ui: ui, event: event, layer: layer.index}); },
				stop: function(event, ui) { return self.handler('draggable', {action: 'stop',ui: ui, event: event, layer: layer.index}); }
			})
		
		if (this.draggableInBox)
			layer.border.resizable({
				containment: (typeof this.draggableInBox == 'boolean'?"parent":this.draggableInBox),
				aspectRatio: true, 
				//minWidth: 30,
				//minHeight: 30,
				//maxWidth: layer.maxWidth,
				//maxHeight: layer.maxHeight,
				start: function(event, ui) { self.handler('resizable', {action: 'start', ui: ui, event: event, layer: layer.index}); },
				resize: function(event, ui) { self.handler('resizable', {action: 'resize',ui: ui, event: event, layer: layer.index}); },
				stop: function(event, ui) { self.handler('resizable', {action: 'stop',ui: ui, event: event, layer: layer.index}); }
			});
			else
			layer.border.resizable({
				//containment: "parent",
				//aspectRatio: true, 
				//minWidth: 30,
				//minHeight: 30,
				//maxWidth: layer.maxWidth,
				//maxHeight: layer.maxHeight,
				start: function(event, ui) { self.handler('resizable', {action: 'start', ui: ui, event: event, layer: layer.index}); },
				resize: function(event, ui) { self.handler('resizable', {action: 'resize',ui: ui, event: event, layer: layer.index}); },
				stop: function(event, ui) { self.handler('resizable', {action: 'stop',ui: ui, event: event, layer: layer.index}); }
			});
		
		
		this.workspace.border.append(layer.border);
		
		//инициализируем канву для рисования текста		
		layer.canvas = $('<canvas></canvas>').attr({ width:layer.border.width(), height:layer.border.height() }).css({ position: 'absolute', cursor: 'move', left: '0px', top: '0px' });
		layer.border.append(layer.canvas);
		layer.canvas = layer.canvas[0];		
		if ($.browser.msie && $.browser.version < 9) { if (typeof G_vmlCanvasManager != 'undefined') G_vmlCanvasManager.initElement(layer.canvas); } //костыль для ИЕ
		layer.context = layer.canvas.getContext('2d');
						
		this.layers.push(layer); 
		if (params) this.setLayer(layer.index, params, true);
				
		
		//загрузим нашу картинку
		layer.img = new Image();		
		layer.img.onload = function(){ 
			//debugger;
			if (this.params) { params = this.params; delete this.params; }
			
			if (self.layers[layer.index] && !self.layers[layer.index].scale_val) self.layers[layer.index].scale_val = 1;
			var scale_val = self.layers[layer.index].scale_val = this.width / this.height;
			//if (!params) params = {};
			//params.height = this.height * scale_val - this.height;
			//self.layers[layer.index].border.css('height', params.height);			
			//self.layers[layer.index].border.css('height', this.height * scale_val - this.height);			

			//проверим настроки в куках
			var d = $.cookie(self.cookieName);
			if (typeof d == 'string' && d.length > 0) {
				d = eval(d);
				for(var i=0;i<d.length;i++) 
				if (d[i].src == layer.src) {
					for(var n in d[i]) if (/*!params[n] && */n != 'src') params[n] = d[i][n];
					break;
				}
			}			
			
			if (self.src_resize_koef && !params.height && !params.width) {
				params.width 	= this.width / self.src_resize_koef;
				params.height 	= this.height / self.src_resize_koef;
			}
			
			if (self.draggableInBox) {
				if (params.width && !params.height) { 
					var p = 100 - (params.width * 100 / this.width);
					params.height = this.height - (this.height * p / 100);
				}
				if (!params.width && params.height) {
					var p = 100 - (params.height * 100 / this.height);
					params.width = this.width - (this.width * p / 100);
				}
				if (!params.left && params.width) {
					params.left = self.workspace.border.width()/2 - params.width/2;
				}
			}
			
			if (params) { 
				if (params.src) delete params.src;
				self.setLayer(layer.index, params);
			}
			else self.draw(layer.index);
			
			self.save();
			
			self.layers[layer.index].border.removeClass('preloader');			
			if (typeof params.afterLoad == 'function') params.afterLoad();
			if (typeof self.onEvent == 'function') self.onEvent('addLayer');
		};
		layer.img.src = layer.src;		
	},
	
	this.hideLayer = function(index){
		if (!this.layers[index]) return;
		this.layers[index].border.hide();
	},
	
	this.showLayer = function(index){
		if (!this.layers[index]) return;
		this.layers[index].border.show();
	},

	this.countLayers = function(){
		var cnt = 0;
		for(var i=0;i<this.layers.length;i++)
			if (this.layers[i].border.is(':visible')) cnt++;
		return cnt;
	},
	
	this.removeLayer = function(index){
		if (this.layers[index]) {
			if (typeof this.onDeleteLayer == 'function') this.onDeleteLayer(this.layers[index]);
			this.layers[index].border.remove();
			this.layers.splice(index,1);			
		}
		/*var i = 0;
		for(var n in this.layers) {
			if (this.layers[n] == layer) {
				this.layers[n].border.remove();
				this.layers.splice(i,1);
				break;
			}
			i++;
		}*/
	},

	this.getActive = function(){
		for(var i=0;i<this.layers.length;i++)
		if (parseInt(this.layers[i].border.css('opacity')) == 1) return this.layers[i];
		return null;
	},
	
	this.parseAlignLayer = function(layer){
		if (layer.align == 'left' && layer.left > 1) layer.align = 'center';
		if (layer.valign == 'top' && layer.top > 1) layer.align = 'center';
	},
	
	this.setAlignLayer = function(horz, vert, vector, cycle){ 
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
	
	this.setActiveLayer = function(index){
		//this.workspace.border.find('> div').css({ zIndex: '198', border: '1px dashed silver' }).css('opacity', '0.6');
		this.workspace.border.find('> div').css({ border: '1px dashed silver' }).css('opacity', '0.6');
		
		for(var n in this.layers) 
		if (this.layers[n] && this.layers[n].border) {
			this.layers[n].border.css({ zIndex: '198', border: '1px dashed silver' }).find('div div').css('opacity', '0.6');
		}
		
		if (this.layers[index])
			this.layers[index].border.css({ zIndex: '199', border: '1px dashed red', opacity: 1 }).find('div div').css('opacity', '1');		
	},
	
	this.handler = function(event, params){
		var self = this;
		switch(event) {
			case 'rotate': {
				this.layers[params.index].rotate = this.layers[params.index].rotate - this.rotateStep;
				if (Math.abs(this.layers[params.index].rotate)>=360) this.layers[params.index].rotate = 0;
				var t = this.layers[params.index];
				t.maxWidth = t.height;
				t.maxHeight = t.width;
				this.setLayer(params.index, {width: t.height, height: t.width});
				this.save();
				this.preSave();
				if (typeof this.onChange == 'function') this.onChange('rotate');
				break;
			}
			case 'draggable': {
				if (params.action == 'start') {
					params.ui.helper.css({opacity: '0.3'});
				} else
				if (params.action == 'stop') {
					params.ui.helper.css({opacity: '1'});
					this.save();
					this.preSave();
					if (typeof this.onChange == 'function') this.onChange('dragstop');
				} else
				if (params.action == 'drag') {
					var ui = params.ui.helper;
					var pos = params.ui.helper.position();
					var us = { lc: ui.width(), tc: ui.height() };
					var ws = { lc: this.workspace.border.width(), tc: this.workspace.border.height() };
					
					if (this.draggableInBox) {
						if (pos.left+us.lc > ws.lc) {
							params.ui.position.left = this.workspace.border.width() - ui.width();
						} else
						if (pos.left < 0) {
							params.ui.position.left = 0;
						}

						if (pos.top+us.tc > ws.tc) {
							params.ui.position.top = this.workspace.border.height() - ui.height();
						} else
						if (pos.top < 0) {
							params.ui.position.top = 0;
						}
					}
					//ui.position.top 	= ui.originalPosition.top 	- div_height;
					//ui.position.left 	= ui.originalPosition.left 	- div_height*scale_val;
					//console.log((pos.top+us.tc)+' > '+ws.tc+'=('+this.workspace.border.height()+'-'+ui.height()+')'+params.ui.position.top);
					this.layers[params.layer].left = params.ui.position.left;
					this.layers[params.layer].top = params.ui.position.top;
					
					if (typeof this.onDraw == 'function') this.onDraw(this.layers[params.layer]);
					//this.setLayer(params.layer, params.ui.position, true);
				}
				//return true;
				break;
			}
			case 'resizable': {
				
				if (!this.canvasImg)
					this.draw(params.layer);
			
				if (params.action == 'start') {
					params.ui.element.css({opacity: '0.3'});
				} else
				if (params.action == 'stop') {
					params.ui.element.css({opacity: '1'});
					this.save();
					this.preSave();
					if (typeof this.onChange == 'function') this.onChange('resizestop');
				} else
				if (params.action == 'resize') {
					var ui = params.ui;
					var pos = { left: ui.position.left, top: ui.position.top };
					//var scale_val = this.layers[params.layer].img.width / this.layers[params.layer].img.height;
					var scale_val = this.layers[params.layer].width / this.layers[params.layer].height;
					
					// получаем разницу высоты и ширины при растягивании картинки
					var div_height 		= ui.size.height - ui.originalSize.height; 
					var div_width  		= ui.size.width - ui.originalSize.width;
					var ui = params.ui;
					//

					if  ((ui.originalPosition.top - div_height)  > 0 &&
						( ui.originalPosition.left 	- div_height*scale_val) > 0 || !self.draggableInBox) { 
						ui.position.top 	= ui.originalPosition.top 	- div_height;
						ui.position.left 	= ui.originalPosition.left 	- div_height*scale_val;
						
						ui.size.height		= Math.max(ui.originalSize.height 	+ div_height*2,20);
						ui.size.width		= Math.max(ui.size.height*scale_val,20); // высоту пересчитываем в зависимости от соотношения сторон
						
					} else { // если уперлись в верхний левый угол - тогда увеличеваем только вправо-вних				
						ui.size.height		= Math.max(ui.originalSize.height 	+ div_height*2,20);
						ui.size.width		= Math.max(ui.size.height*scale_val,20);
					}

//console.log(this.layers[params.layer].width+1+'x'+this.layers[params.layer].maxWidth);
					if (!(this.workspace.width == this.layers[params.layer].maxWidth || this.workspace.height == this.layers[params.layer].maxHeight)) {
						if (ui.size.width > this.layers[params.layer].maxWidth || ui.size.height > this.layers[params.layer].maxHeight)
							$(this.no_resize_label).fadeIn(200);
						else $(this.no_resize_label).fadeOut(200);
					}
					
					if ((ui.size.width < this.layers[params.layer].minWidth && ui.size.height < this.layers[params.layer].minHeight) || (ui.size.width < 22 || ui.size.height < 22))
						$(this.small_resize_label).fadeIn(200);
					else $(this.small_resize_label).fadeOut(200);
						
					if (self.draggableInBox) {
						if (ui.size.height > this.layers[params.layer].maxHeight) { ui.size.height = this.layers[params.layer].maxHeight; ui.position.left = pos.left; ui.position.top = pos.top; }
						if (ui.size.width > this.layers[params.layer].maxHeight) { ui.size.width = this.layers[params.layer].maxWidth; ui.position.left = pos.left; ui.position.top = pos.top; }
					}
					
					var t = this.layers[params.layer].border;
					this.layers[params.layer].width = t.width();//ui.helper.width();
					this.layers[params.layer].height = t.height();//ui.helper.height();
					$(this.layers[params.layer].canvas).attr({width:t.width(), height:t.height()});

					if (params.setLayer)
						self.setLayer(params.layer, { left: ui.position.left, top: ui.position.top, width: ui.size.width, height: ui.size.height }, true);
					
					this.draw(params.layer);
				}
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
					var e = this.layers[params.index].border.offset();
					var b = self.workspace.border;
					var offset = { X: this.layers[params.index].start.offsetX, Y: this.layers[params.index].start.offsetY };
					if (typeof offset.X == 'undefined') offset = {X:7,Y:7};
					var css = {left: params.event.clientX, top: params.event.clientY};					
					if (!this.draggableInBox || (p.left <= css.left && p.top <= css.top && 
						p.left + b.width() >= css.left + this.layers[params.index].border.width() && p.top + b.height() >= css.top + this.layers[params.index].border.height())) {
							css.left = css.left - p.left;// + b.position().left// - offset.X;
							css.top = css.top - p.top;// + b.position().top;// - offset.Y;
							this.setLayer(params.index, css, true);
					}
				}
				if (params.mouse == 'up') {
					$(window).unbind('mousemove').unbind('mouseup');
					this.layers[params.index].start = null;
				}
				
				this.draw(this.layers[params.index]);
				break;
			}
			case 'resize': {
				if (params.mouse == 'down') {
					return true;
					var layer = params.layer;
					this.layers[params.index].start = params.event;
					var index = params.index;
					$(window).unbind('mousemove').bind('mousemove',function(e){ self.handler('resize', { mouse: 'move', index: index, event: e }); });
					$(window).unbind('mouseup').bind('mouseup',function(e){ self.handler('resize', { mouse: 'up', index: index, event: e }); });
				}
				if (params.mouse == 'move') {
					
					/*var p = this.workspace.border.offset();
					var e = this.layers[params.index].border.offset();
					var b = this.layers[params.index].border;
					var k = this.workspace.border;
					
					var offset = { X: this.layers[params.index].start.offsetX, Y: this.layers[params.index].start.offsetY, X1: this.layers[params.index].start.clientX, Y1: this.layers[params.index].start.clientY };
					if (typeof offset.X == 'undefined') offset = {X:7,Y:7};
					
					var p1 = (params.event.clientX * 100)/offset.X1;
					var p2 = (params.event.clientY * 100)/offset.Y1;
					
					var w = (b.width() * p1) / 100;//e.left
					var h = (b.height() * p2) / 100;
					//debugger;
					//var css = {width: params.event.clientX-e.left-offset.X, height: params.event.clientY-e.top-offset.Y};
					var css = {width: w, height: h};
					if (k.width() >= this.layers[params.index].left+css.width && k.height() >= this.layers[params.index].top+css.height && css.height>=20 ) {
						this.setLayer(params.index, css, true);
					}*/
				}
				if (params.mouse == 'up') {
					$(window).unbind('mousemove').unbind('mouseup');
					this.layers[params.index].start = null;
					this.save();
					this.preSave();
					if (typeof this.onChange == 'function') this.onChange('up');
				}
				this.draw(this.layers[params.index]);
				break;
			}
			case 'remove': {
				if (confirm('Вы уверены, что хотите удалить изображение?')) {
					var self = this;
					$.cookie('customize_img['+(is_front()?'front':'back')+']', null, { expires: -1, path:'/' });
					//$.get(this.url_delete+(is_front()?'front':'back')+'/', {}, function(q){ 
					//	if (q != 'ok') { alert('Ошибка при удалении, попробуйте ещё раз!'); return; }
						self.removeLayer(params.index);
						self.save();
						self.preSave();
						if (typeof self.onChange == 'function') self.onChange('remove');
						$('#threeStepsMenu a[hash="'+(is_front()?'front':'back')+'"]').parent().removeClass('pic');
						if (is_front())
							img_front = self.initImg;
						else 
							img_back = self.initImg;
						
						$('#step'+(is_front()?1:2)).parent().removeClass('pic');
						
						$(self.no_resize_label).fadeOut(200);
						
						if (!is_img() && window.location.hash.indexOf('text') < 0) uploadActive(true);
					//});
				}
			}
		}

		if (this.onEvent) this.onEvent(event, params);
		return true;
	},
	
	this.draw = function(p){
		var layer = {};
		if (p && p.border) layer = p; else layer = this.layers[p];
	
		if (typeof this.onDraw == 'function') this.onDraw(layer);
		
		if (!this.canvasImg) return;
				
		var w = layer.border.width();
		var h = layer.border.height();
		
		if (!w) w = layer.width;
		if (!h) h = layer.height;
	
		try{ 
			//очистка канвы
			layer.context.clearRect(0,0,w,h);
			
			//рисуем изображение
			if (layer.rotate) {
				var x = w / 2, y = h / 2;
				layer.context.translate(x, y);
				layer.context.rotate(layer.rotate * Math.PI / 180);
				if (layer.rotate == 0 || layer.rotate == -180){
					layer.context.drawImage(layer.img, -x, -y, w, h);
					layer.context.rotate(-layer.rotate * Math.PI / 180);
					layer.context.translate(-x, -y);
				} else {
					layer.context.drawImage(layer.img, -y, -x, h, w);
					layer.context.rotate(-layer.rotate * Math.PI / 180);
					layer.context.translate(-y, -x);
				}
			} else 
				layer.context.drawImage(layer.img,0,0,w,h);

		/*if ((layer.rotate-0) != ((layer._rotate?layer._rotate:0)-0)) {
			layer.border.css({width: layer.border.height(), height: layer.border.width()});
			$(layer.canvas).attr({ width:layer.border.height(), height:layer.border.width() });		
			layer.width = layer.border.height();
			layer.height = layer.border.width();
			layer._rotate = layer.rotate;
			this.draw(layer);return;
		}*/
				
		}catch(e) {}
		
		//сохраняем состояние в куки
		this.save();
	},
	
	this.save = function(){
		if (this.restoring) return;
		try{ 
			var s = '[';
			var i = 0;
			for(var n in this.layers) {
				var l = this.layers[i];
				if (!l) continue;
				s += (s.length>1?',':'') + '{';
				
				s += 'left: ' + l.border.position().left + ',';
				s += 'top: ' + l.border.position().top + ',';
				s += 'width: ' + l.border.width() + ',';
				s += 'height: ' + l.border.height() + ',';
				s += 'maxWidth: ' + (l.maxWidth?l.maxWidth:l.border.width()) + ',';
				s += 'maxHeight: ' + (l.maxHeight?l.maxHeight:l.border.height()) + ',';
				s += 'align: "' + (l.align?l.align:'') + '",';
				s += 'valign: "' + (l.valign?l.valign:'') + '",';
				s += 'src: "' + l.src + '",';
				s += 'rotate: "' + l.rotate + '"';
				
				s += '}';
				
				i++;
			}
			s += ']';
			
			if (typeof this.onEvent == 'function') this.onEvent('save');
			$.cookie(this.cookieName, s, { expires: 1, path: "/" });
		}catch(e) {}
	},

	this.restore = function(side, afterLoad){
		this.restoring = true;
		try {
			
			if (typeof side == 'string' && 'image_layers_'+side != this.cookieName) {
				this.cookieName = 'image_layers_'+side;
				this.restore();	
				return;				
			}
			
			var d = $.cookie(typeof side == 'string'?side:this.cookieName);
			if (typeof d == 'string' && d.length > 0) {
				d = eval(d);
				if (d) {
					for(var i=0;i<d.length;i++) { 
						if (!d[i].src || $.trim(d[i].src) == '') { 
							d.splice(i,1); i--; 
						}
					}
				}
				var g = false;//debugger;
				if (d.length != this.layers.length) {
					g = true;
					while (this.layers.length > 0) this.removeLayer(0);
				}
				for(var i=0;i<d.length;i++) {
					var d1 = { afterLoad: afterLoad,
								maxWidth: parseInt(d[i].maxWidth),
								maxHeight: parseInt(d[i].maxHeight)
							 }
					
					//var d2 = {};
					//for(var n in d1) if (d1[n]) d2[n]=d1[n];
					//for(var n in d[i]) if (d[i][n]) d2[n]=d[i][n];
					//this.addLayer(d[i].src, d2);
					for(var n in d[i]) if (d[i][n]) d1[n]=d[i][n];
					if (g) {
						delete d1.src;
						this.addLayer(d[i].src, d1);
					}
					else this.setLayer(i, d1);
					//this.setLayer(i, d[i]);
					//if (this.layers.length>0) this.draw(0);
				}
				if (typeof this.onEvent == 'function') this.onEvent('restore');
			}
		
		}finally {	this.restoring = false; }
	},
	
	this.clear = function(){
		$.cookie(this.cookieName, null);
	},
	
	this.order = function(side){		
		var cookieSave = this.cookieName;
		if (typeof side == 'string' && side != this.cookieName) {			
			this.cookieName = 'image_layers_'+side;
			this.restore();			
		}

		var s = [];		
		var i = 0;
		for(var n in this.layers) {
			var l = this.layers[i];

			//ctx.drawImage(l.canvas, this.workspace.border.position().left + l.border.position().left, this.workspace.border.position().top + l.border.position().top);
						
			var v = {
				src: l.src,
				left: l.border.position().left,
				top:  l.border.position().top,
				width: l.border.width(),
				height: l.border.height(),
				//align: l.align,
				rotate: l.rotate,
			}
			
			if (l.userData)
				for(var n in l.userData) v[n] = l.userData[n];

			s.push(v);
			i++;
		}
		
		var _side = /^image_layers_(\w+)/.exec(this.cookieName)[1];
		
		this.clear();
		
		if (cookieSave != this.cookieName) {
			this.cookieName = cookieSave;
			this.restore();
		}
		
		return { side: _side, layers: s };
	}
	
	this.order2 = function(side){
		var cookieName = (typeof side == 'string'?'image_layers_'+side:this.cookieName);
		var s = [];
		var d = $.cookie(cookieName);
		
		if (typeof d == 'string' && d.length > 0) {
			d = eval(d);
			for(var i=0;i<d.length;i++) {
				if (typeof d[i] == 'undefined' || d[i] == null) continue;
				s.push({
					src: d[i].src,
					left: d[i].left,
					top:  d[i].top,
					width: d[i].width,
					height: d[i].height,
					align: (d[i].align?d[i].align:''),
					valign: (d[i].valign?d[i].valign:''),
					rotate: d[i].rotate,
				});
			}
		}
		
		var _side = /^image_layers_(\w+)/.exec(cookieName)[1];
		
		return { side: _side, layers: s };
	},
	
	this.preSave = function(){
		if (typeof this.urlSaveChanged != 'string' || this.urlSaveChanged.length == 0) return;
		var imgData = this.order();
		$.post(this.urlSaveChanged, imgData, function(data){
			//alert('Сохранено!'); 
			//$("#action_save").css({visibility:'visible'});
			//$(".saveLoaderBG").addClass("preloader").hide();
		});
	}
	
	this.init(p);
	return this;
}
imagemove.prototype = {
	show: function(){ this.div.show(); return this; },
	hide: function(){ this.div.hide(); return this; }
}