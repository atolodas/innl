var good_id = 1;
var good_id = 1;

function isUrl(url){
	return /[\.](\w{3}|\w{4})$/.test(url);
}

//проверка можно ли загружать файлы
function checkUpload() {
	//проверяем, если до 5ти загрузок, то разрешаем
	var uploaded_counter = jQuery('input[name=uploaded_counter]').val();	
	if (parseInt(uploaded_counter) > 0) uploaded_counter = parseInt(uploaded_counter);
	else uploaded_counter = 0;
	if (uploaded_counter <= 5) return true;
	
	if (typeof checkRegistration == 'function') 
		return checkRegistration(location.pathname);
		//return checkRegistration('/customize/new/style,210/color,89/');		
	return true;
}

//обработчик пост загрузки файла
function uploadHandler(e) {
  var uploaded_counter = jQuery('input[name=uploaded_counter]').val();	
  if (parseInt(uploaded_counter) > 0) uploaded_counter = parseInt(uploaded_counter);
  else uploaded_counter = 0;

  jQuery('input[name=uploaded_counter]').val(++uploaded_counter);
  
  if (e && e.posiblesizes) 
	jQuery('select.good_stock_id').html('');
	
  if (e && e.status == 'ok') {
	jQuery('#startSellBlock').show();
	if (e.posiblesizes) {
		var opts = '';
		for(var n in e.posiblesizes) 
		if (e.posiblesizes[n].good_stock_id && e.posiblesizes[n].size_id){
			opts += '<option value="'+e.posiblesizes[n].good_stock_id+'" _size="'+e.posiblesizes[n].size_id+'">' + e.posiblesizes[n].size_name + ' — ' + e.posiblesizes[n].price + 'р. </option>';
		}
		jQuery('select.good_stock_id').html(opts);
	}
  }
  
  var date = new Date();
  date.setTime(date.getTime() + (30 * 60 * 1000));
  $.cookie('upload_files', ++uploaded_counter, { expires: date });
}

function changeImg(src, image_size, self) { 
	var _im = (typeof im != 'undefined'?im:self);
	if (typeof src == 'string' && /\.[\w]+$/.test(src)) {		 //src.indexOf('pictures_src') > 0
		uploadActive(false);
		if (_im.layers.length > 0) {
			image_size.src = src;
			_im.setLayer(0, image_size);
		}
		else
			_im.addLayer(src, image_size);			
		
		jQuery('#step'+(is_front()?1:2)).parent().addClass('pic');
		
		if (is_front())
			img_front = src;
		else
			img_back = src;
		
		jQuery('#startSellBlock').show();
		
	} else {
		
		if (window.location.hash.indexOf('text') < 0) 
			uploadActive(true);
		
		if (is_front())
			img_front = '';
		else
			img_back = '';		
	}
}

function changeImgObject(img, image_size, self) {
	var _im = (typeof im != 'undefined'?im:self);
	if (typeof src == 'object' && /\.[\w]+$/.test(img.attr('src'))) {		 //src.indexOf('pictures_src') > 0
		uploadActive(false);
		if (_im.layers.length > 0) {
			image_size.img = img;
			_im.setLayer(0, image_size);
		}
		else
			_im.addLayer(src, image_size);			
		
		jQuery('#step'+(is_front()?1:2)).parent().addClass('pic');
		
		if (is_front())
			img_front = img.attr('src');
		else
			img_back = img.attr('src');
		
		jQuery('#startSellBlock').show();
		
	} else {
		uploadActive(true);
		if (is_front())
			img_front = '';
		else
			img_back = '';		
	}
}


/* Загрузка содержимого табов */
function loadTab(tabID, ajax_link){
	jQuery(tabID).css({background:'url(http://www.maryjane.ru/images/buttons/ajax-loader-img.gif) no-repeat scroll 50% 10px transparent'});
	$.get(ajax_link, function(resp){
		jQuery(tabID).css({background:'none'}).html(resp);
	});
}

// Загрузка содержимого всех табов
function loadTabsCont (){	
	var ajax_tab_content = [
//		{tabID:'#tab-comments', 	link_param:'comments'},		
		{tabID:'#tab-sizes', 		link_param:'sizes'},
		{tabID:'#tab-composition', 	link_param:'composition'},
		{tabID:'#tab-delivery', 	link_param:'delivery'},
		{tabID:'#tab-moneyback', 	link_param:'moneyback'}
		];
	
	for (var key in ajax_tab_content) {
		var ajax_link = '/ajax/getTab/?good_id='+good_id+'&tab='+ajax_tab_content[key]['link_param']+(ajax_tab_content[key]['link_param'] == 'sizes'?'&faq_id='+faq_id:'');
		
		var tabID = ajax_tab_content[key]['tabID'];
		loadTab(tabID, ajax_link);
	}

	setTimeout(function(){
		if (jQuery(".b-we-recomended").length == 0 || jQuery("#recomendet-content".length == 0)) return;
		jQuery(".b-we-recomended").css({background:'url("http://printshop.maryjane.ru/img/reborn/thickbox/loading.gif") no-repeat scroll 50% 137px transparent'});
		$.get('/ajax/getRecomended/?good_id='+good_id, function (recomHtml){
			jQuery(".b-we-recomended").css({background:'none'});
			jQuery("#recomendet-content").html(recomHtml);
		})}, 1500);

}

//активность кнопок грудь/спина
function setModeActive(d){
	if (jQuery(d).length == 0) return false;
	
	jQuery('#threeStepsMenu div').removeClass('active');
	jQuery(d).parent().addClass('active');
	
	/*var src = is_front()?img_front:img_back;
	if (img_front && img_front.indexOf('pictures_src') > 0)
		jQuery(d).parent().addClass('pic');
	img_back*/
	
	
	var s = window.location.hash;
	s = s.replace('front','').replace('side','').replace('back','').replace('#','').replace(',','');
	window.location.hash = s+(s.length>0?',':'')+jQuery(d).attr('hash');

	var b = /[^\b]+.ru(\/[customize|stickermize|customize.dev|stickermize.dev][\w\.]+\/)/;
	if (!b.test(location.href))
		b = /[^\b]+.ru(\/[customize|stickermize|customize.dev|stickermize.dev]\/)/
	var n = b.exec(location.href);
	if (n.length>0) $.cookie('side', jQuery(d).attr('hash'),  { expires: 7, path: n[1]} );

	jQuery('#side').val(jQuery(d).attr('hash'));
	
	return true;
}

function is_front() {
	var h = window.location.hash;
	if (!h) h='';
	//if (h.length == 0 && (typeof initSide == 'undefined')) h = jQuery('#side').val();
	if (h.length == 0) h = jQuery('#side').val();
	if (!h) h='';
	if (h.length == 0) h = $.cookie('side');	
	//window.initSide = true;
	if (!h) h='';
	return (h.indexOf('front') >= 0 || /*h.indexOf('side') < 0 ||*/ h.indexOf('back') < 0);
}
jQuery(document).ready(function(){ window.initSide = true; })

function getUrlVars() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

//показ флеши для загрузки картинки
function uploadActive(st) {
	if (!st) { 
		jQuery('#actionforimg .slider-p, #actionforimg .delete, #actionforimg .rotate').removeAttr('disabled').removeClass('disabled');
		if (typeof jQuery().slider == 'function')
			jQuery('#actionforimg .slider-line').slider('enable');
		//jQuery('#actionforimg').show();
		//jQuery('#uploadifyQueue').hide();
		jQuery('#prv_uploadifyUploader').css({ visibility:'hidden', display: 'none'}).hide();					
		jQuery('#prv_uploadify_block').hide();
		//jQuery('#prv_uploadify').hide();
		jQuery('.loader-model-name').hide();
		jQuery('.span-inner-loader').hide();
		jQuery('#circleuploaderbox').hide();
	} else {
		jQuery('#actionforimg .slider-p, #actionforimg .delete, #actionforimg .rotate').attr('disabled','disabled').addClass('disabled');
		if (typeof jQuery().slider == 'function')
			jQuery('#actionforimg .slider-line').slider('disable');
		//jQuery('#actionforimg').hide();
		//jQuery('#uploadifyQueue').show();
		jQuery('#prv_uploadify_block').show();
		jQuery('#prv_uploadifyUploader').css({ visibility:'visible', display: 'block'}).show();
		jQuery('.loader-model-name').show();
		jQuery('.span-inner-loader').show();
		if (!authorized)
			jQuery('#circleuploaderbox').show();
	}
	
	//центруем загрузчик
	if (typeof ct != 'undefined') {
		try{
			var p = workspace[is_front()?'front':'back'];
			//var p = { left: ct.workspace.left, top: ct.workspace.top, width: ct.workspace.width, height: ct.workspace.height };
			var f = jQuery('#prv_uploadify_block_input');
			var _t = jQuery('#content-editor');
			var t = _t.position();
			var k = jQuery('#tees-shape');
			var left = t.left + p.left + (p.width / 2)  + ((_t.width()  - k.width())  / 2);
			var top  = t.top  + p.top  + (p.height / 2) + ((_t.height() - k.height()) / 2);
			
			left = left - (f.width()/2)  + 1 + (location.href.indexOf('stickermize')>0?5:0);
			top  = top  - (f.height()/2) - 71;
			
			//console.log(left+'x'+top);
			if (left && top)
				jQuery('#prv_uploadify_block_input').css({ left: left + 'px', top: top + 'px' });
		}catch(e){}
	}	
}


//Остался кусок с прошлого редактора
// центрируем ограничитель на футболке
initLimiter = function (pos) {
	var limiterID 		= '#limiter';
	//var offset			= '#tees-shape';
	var shapeLeftPos	= 0;
	var shapeTopPos  	= 0;	
	
	var offset = {left:0,top:0};//jQuery(offset).offset();
	
	jQuery(limiterID).width(pos.width).height(pos.height).css({top:(pos.top+offset.top)+"px", left:(pos.left+offset.left)+"px"}).show();	
	if (jQuery(".doubleBorder").length == 0)
		jQuery('<div class="doubleBorder"></div>').insertBefore(limiterID);
	jQuery(".doubleBorder").width(pos.width+2).height(pos.height+2).css({top:(pos.top+offset.top-1)+"px", left:(pos.left+offset.left-1)+"px"}).show();

	/*if (skin_size!= null) {
		if (jQuery("#side").val() == 'back') {
			var limiter_lft	 = (skin_size.back.x*1 - (skin_size.back.w*1)/2 + shapeLeftPos) - 1; // -1 это компенсация рамки
			var limiter_top	 = (skin_size.back.y*1 - (skin_size.back.h*1)/2 + shapeTopPos)  - 1; // -1 это компенсация рамки 
			var limiter_w 	 = skin_size.back.w*1;
			var limiter_h 	 = skin_size.back.h*1;
		} else {
			var limiter_lft	 = (skin_size.front.x*1 - (skin_size.front.w*1)/2 + shapeLeftPos) - 1; // -1 это компенсация рамки
			var limiter_top	 = (skin_size.front.y*1 - (skin_size.front.h*1)/2 + shapeTopPos)  - 1; // -1 это компенсация рамки 
			var limiter_w 	 = skin_size.front.w*1;
			var limiter_h 	 = skin_size.front.h*1;
		}
		
		
		jQuery(limiterID).width(limiter_w).height(limiter_h).css({top:limiter_top+"px", left:limiter_lft+"px"}).show();
		limiter_top--; limiter_lft--;limiter_h +=2; limiter_w +=2;
		jQuery('<div class="doubleBorder"></div>').insertBefore(limiterID);
		jQuery(".doubleBorder").width(limiter_w).height(limiter_h).css({top:limiter_top+"px", left:limiter_lft}).show();
		
	} else {
		jQuery(limiterID).css({top:0, left:0}).hide();
	}*/
}

initSaveBtn = function () {
	jQuery("#saveAndAdd").click(function(e){
		//trackUser('В корзину [customize]','customize','ОК');
		actionSaveAndAdd(function(){
				if (window.qBask != undefined) {
					qBask.showBasketAndScroll();
				}
			});
		return false;
		e.stopPropagation();
	});
}

MakeOrder = function(side){
/* 	var c = jQuery('<canvas></canvas>').attr({width: ct.img.width, height: ct.img.height});
	var ctx = c[0].getContext('2d');
	ctx.drawImage(ct.img, 0, 0);
 
	var ws = jQuery('#limiter').position();
	
	ct.restore(side);
	im.restore(side);
	
	//рисуем картинки
	for(var n in ct.layers) {
		var l = ct.layers[n];
		if (typeof l == 'undefined') continue;
		ctx.drawImage(l.canvas, ws.left + l.border.position().left, ws.top + l.border.position().top);
	}
	
	for(var n in im.layers) {
		var l = im.layers[n];
		if (typeof l == 'undefined') continue;
		ctx.drawImage(l.canvas, ws.left + l.border.position().left, ws.top + l.border.position().top);
	}
*/	
	//получаем данне по слоям
	return { text: ct.order2(side), image: im.order2(side)/*, preview: c[0].toDataURL()*/ };

}

/* Сохранить позиционирование и добавить в корзину */
actionSaveAndAdd = function (params, autosave) { 
	
		good_id = jQuery('input[name=good_id]').val();
		
		if (autosave) {
			if (typeof good_id == 'undefined') return;
			if (good_id <= 0) return;
		}
		
		jQuery("#action_save").css({visibility: 'hidden'});
		jQuery(".saveLoaderBG").addClass("preloader").show();
		
		//добавить в корзину
		var good_stock_id = jQuery(".good_stock_id").val();
		
		//модуль
		var mod = jQuery('.goodProperties input[name=module]').val();
		mod = (mod?mod:'');

		//стиль
		var style_id = jQuery('.goodProperties input[name=style_id]').val();
		style_id = (style_id?style_id:'');
		
		//носитель
		var cat = jQuery('#ul-basecats a.cat-selected').attr('cat');
		cat = (cat && cat.length > 0?cat:jQuery('input[name=cat]').val());
		cat = (cat && cat.length > 0?cat:'');
		
		//ширина и высота для ноутбука
		var comment = null; 
		if (jQuery('#exact_width').length > 0 && jQuery('#exact_height').length > 0) {
			var w = jQuery('#exact_width').val().trim();
			var h = jQuery('#exact_height').val().trim();
			if ((w.length == 0 && h.length > 0) || (w.length > 0 && h.length == 0) || isNaN(w) || isNaN(h) || (w.length==0 && h.length == 0)) { 
				alert('Вы ввели некорректный размер крышки. Проверьте данные');
				jQuery("#action_save").css({visibility:'visible'});
				jQuery(".saveLoaderBG").addClass("preloader").hide();
				jQuery('#exact_width').focus();
				return false; 
			}
			if (w.length > 0 && h.length > 0)
			comment = w+'x'+h;
		}
		
		var data = { 'good_id': (good_id?good_id:''), active: (is_front()?'front':'back'), 'good_stock_id': good_stock_id, 'quantity': 1, module: mod, style_id: style_id, cat: cat, comment: comment, r: new Date().getTime() };
		
		if (jQuery('input[name=safetySkin]').length > 0)
			if (jQuery('input[name=safetySkin]')[0].checked)
				data.safetySkin = jQuery('input[name=safetySkin]').val();
		
		try { 
			data.front 	= MakeOrder('front');
			data.back 	= MakeOrder('back');
		} catch (e) {
			jQuery("#action_save").css({visibility:'visible'});
			jQuery(".saveLoaderBG").addClass("preloader").hide();
			alert("Неизвестная ошибка");
		}
		finally {
			var side = is_front()?'front':'back';
			ct.restore(side);
			im.restore(side);
		}
		
		//debugger;
		//addgoodtobasket
		var url = "/ajax/add2basketCustomize/";
		if (typeof params == 'string')
			url = params;
		$.post(url, data, function(data) { //ajax/?action=addgoodtobasket-customize
			var pos         = data.indexOf(":");
			var state       = data.substring(0, pos);
			var message     = data.substring(pos+1);
			
			jQuery("#action_save").css({visibility:'visible'});
			jQuery(".saveLoaderBG").addClass("preloader").hide();

			if (state == "ok") {
				if (typeof params != 'string')
					getBasketInfo(params); 
				//if (typeof params == 'function') params(data);
			} 
			else if (state == "error") {alert(message);} 
			else {alert("unknown error: " + data);}
		});
	
}

//компонент кнопка
btn = function(p){
	var self = this;
	for(var n in p) this[n] = p[n];
	this.div = jQuery('<div class="button '+this.name+(this.selected==true?' select':'')+'">'+(this.innerHTML?this.innerHTML:'')+'</div>');
	if (p.noevent == true) return this;
	this.div.click(function(){ if (typeof self.onclick == 'function') return self.onclick(self, this); return true; });
	this.div.mousedown(function(){ if (typeof self.onmousedown == 'function') return self.onmousedown(self, this); return true; });
	this.div.mouseup(function(){ if (typeof self.onmouseup == 'function') return self.onmouseup(self, this); return true; });
	this.div.mousemove(function(){ if (self.hint) self.hint.show(this, (self.div.height() + 15)+'px 0px 0px 0px' ); return false; });
	this.div.mouseout(function(){ if (self.hint) self.hint.hide(); return false; });
	
	return this;
}

//компонент подписи при наведении
hint = function(p){
	var self = this;
	for(var n in p) this[n] = p[n];
	this.div = jQuery('<div class="btn_hint"><div class="t"></div>'+this.text+'</div>').hide();
	jQuery('body').append(this.div);
	return this;
}
hint.prototype = {
	show:function(self, margin) {
		//var d = jQuery(self).position();
		var f = jQuery(self).offset();
		var m = {left: (f.left + jQuery(self).width()/2) - (this.div.width()/2) - 10, top: f.top};
		if (margin) m.margin = margin;
		this.div.css(m).show();
	},
	hide: function() { this.div.hide(); }
}

//кнопки на сайте
btns = {
	items: [],
	
	uploadChange:function(self) {
		//jQuery('#allskins_form').attr('action', '/customize.dev/37410/');
		//jQuery('#allskins_form').submit();		
	},
	
	onclick: null,
	
	init: function(id, onclick) {
		this.onclick = onclick;
		var self = this;
		this.items = [
				new btn({name:'shirt', hint: new hint({text: 'Футболки'}), selected:(window.location.hash != "#text")}), 
				new btn({name:'text', hint: new hint({text: 'Добавить надпись'}), selected:(window.location.hash == "#text")}), 
				//new btn({name:'upload', hint: new hint({text: 'Загрузить файл'}), innerHTML: '<form action="'+linkUload+'" method="post"><div style="position:relative;width:100%;height:100%;"><input type="hidden" value="true" name="save_allskins_prv" /><input multiple="multiple" type="file" name="file" onchange="btns.uploadChange(this)" style="position:absolute;right:0px;top:0px;width:100%;height:100%;margin:0px;padding:0px;opacity:0;"></div></form>' })
				//new btn({name:'upload', hint: new hint({text: 'Загрузить файл'}), innerHTML: '<div style="position:relative;width:100%;height:100%;"><input type="hidden" value="true" name="save_allskins_prv" /><input multiple="multiple" type="file" name="file" onchange="btns.uploadChange(this)" style="position:absolute;right:0px;top:0px;width:100%;height:100%;margin:0px;padding:0px;opacity:0;"></div>' })
				new btn({name:'upload', hint: new hint({text: 'Загрузить файл'}), innerHTML: '<input id="src_uploadify_123" type="file" name="uploadify_prv" />' })
				//{ name: 'upload', hint: new hint({text: 'Загрузить файл'}), div: jQuery('<input type="file" value="true" name="save_allskins_prv" />') }
			]
		
		for(var i=0;i<this.items.length;i++) {
			this.items[i].onclick = function(n,r){
				if (n.name == 'upload') { 
					if (!authorized) {
						var r = checkUpload();

						if (!r) {
							if (event.stopPropagation) event.stopPropagation(); else event.cancelBubble = true;
							if (event.preventDefault) event.preventDefault(); else event.returnValue = false;						
						}
						
						return r;
						//if (typeof checkRegistration == 'function') 
						//	return checkRegistration('/customize/new/style,210/color,89/');
						//return true;
					}
					else return true;
				}
				for(var i=0;i<self.items.length;i++) self.items[i].div.removeClass('select');
				jQuery(r).addClass('select');
				
				if (n.name == 'text')  { 
					if ($.browser.msie && $.browser.version < 9) { alert('Для работы с такстовым конструктором необходимо обновить браузер'); return; }
					
					var s = window.location.hash.replace('text','').replace(',','');
					window.location.hash = (s.length>0?s+',':'')+'text';
					
					jQuery('#ul-basecats').hide(); 
					jQuery('#property').fadeIn(500); 
					
					uploadActive(false);
										
					if (ct.property.div) {
						var d = ct.property.div.find('.inputs input');
						if (d.length > 0) jQuery(d[0]).focus();
					}
					
					if (jQuery('.mailtoover').length>0) jQuery('.mailtoover').parent().hide();
				}
				if (n.name == 'shirt')  { 
					window.location.hash = window.location.hash.replace('text','').replace(',','');
					jQuery('#property').hide(); 
					jQuery('#ul-basecats').fadeIn(500);
					if (!is_img()) uploadActive(true);
					
					if (jQuery('.mailtoover').length>0) jQuery('.mailtoover').parent().show();
				}
				
				if (jQuery('#step1').length > 0 && jQuery('#step2').length > 0) {
					var h = jQuery('#step1').attr('href').replace(/#text/g,'').replace(/#/g,'');
					jQuery('#step1').attr('href', h+window.location.hash);
					var h = jQuery('#step2').attr('href').replace(/#text/g,'').replace(/#/g,'');
					jQuery('#step2').attr('href', h+window.location.hash);
				}
				
				if (typeof self.onclick == 'function') self.onclick(n,r);
			}
		jQuery(id).append(this.items[i].div);		
		}
		
		/*this.items[2].onmousedown = function(n,r){
			jQuery(r).removeClass('select').addClass('select');
			return false;
		}
		this.items[2].onmouseup = function(n,r){
			jQuery(r).removeClass('select');
			return false;
		}*/
		
		var _name = window.location.hash.replace('#','');
		var name = '';
		if (_name.indexOf('text') >= 0) name='text';
		if (_name.indexOf('shirt') >= 0) name='shirt';
		if (name.length==0) name = 'shirt';
		this.items[0].onclick({name: name}, this.items[name == 'text'?1:0].div);

		//var upload_url = '/'+jQuery('input[name=module]').val()+'/upload/'+jQuery('input[name=style_id]').val()+'/'+SESSIONID;
		var upload_url = '/customize/upload/'+jQuery('input[name=style_id]').val()+'/'+SESSIONID;
		
		var btn_upload = jQuery(this.items[2].div).find('input');
		if (!authorized && !checkUpload()) {
			jQuery(this.items[2].div).attr('onclick',jQuery('#circleuploaderbox').attr('onclick'));
			btn_upload.hide();
		} else
		
		//if (typeof dev == 'boolean' && dev) {
			
			jQuery('#src_uploadify_123').FileAPI({
				url: upload_url,
				fileExt: '.jpeg,.jpg,.png',
				uploadProgress: '#uploadifyQueue', 
				data: { side: (is_front()?'front':'back') },
				select: function() { 
					jQuery('#response > .error').hide();
					jQuery('#response > .success').hide();		
				},
				complete: function(file, response){
					var e = eval('('+response+')');
						
					uploadHandler(e);
						
					if (e.status == 'ok') {
						jQuery('#src').val(e.id);
						jQuery('#name').val(e.oldname);
						
						$.cookie('customize_img['+(is_front()?'front':'back')+']', e.id, { expires: 7, path:'/' });
						var image_size = { width: e.resize_w, height: e.resize_h }; 
						image_size.maxWidth = image_size.width;
						image_size.maxHeight = image_size.height;
						
						var p = e.path; 
						if (typeof e.resized == 'string' && e.resized.length>0) p = e.resized;
						if (typeof im != 'undefined' && im.initImg)
							changeImg((im.initImg + p)/*.replace('//','/')*/, image_size);
						//else if (!dev) changeImg('http://www.maryjane.ru/ajax/initImg/?url='+e.path, image_size);
						//else if (dev) changeImg('http://www.maryjane.ru/'+e.path, image_size);
						else changeImg('http://www.maryjane.ru/'+e.path, image_size);
						//jQuery('#allskins_form').submit();
					} else {
						jQuery('#response > .error').text('Ошибка: ' + e.message).show();
					}
					
					if (e.status == 'error') alert(e.message);					
				}
			});			
		
		/*} else {
			btn_upload.uploadify({
			'uploader'    : '/uploadify.swf',
			'script'      : upload_url,  //ajax/allskins_prv_upload/?sid=' + SESSIONID,
			'cancelImg'   : 'http://www.maryjane.ru/images/uploadify/cancel.png',
			//'buttonImg'   : '/images/customize/button_upload_v1.jpg',
			'hideButton' : true, 
			'wmode'       : 'transparent',
			'width'		  : 76,
			'height'	  : 45,
			'folder'      : 'upload',
			'queueID'     : 'uploadifyQueue',
			//'buttonText'  : '{/literal}{if $skins}EDIT{else}UPLOAD{/if}{literal}',
			'auto'        : true,
			'multi'       : false,
			'fileDesc'    : 'jpg;png',
			'fileExt'     : '*.jpg;*.png',
			'scriptData'  : {'side': (is_front()?'front':'back') },
			'onComplete'  : function(event,queueID,fileObj,response,data) 
			{
				jQuery('#uploadifyQueue').hide();
							
				var e = eval('('+response+')');
				
				uploadHandler(e);
				
				if (e.status == 'ok') {
					jQuery('#src').val(e.id);
					jQuery('#name').val(e.oldname);
					
					$.cookie('customize_img['+(is_front()?'front':'back')+']', e.id, { expires: 7, path:'/' });
					var image_size = { width: e.resize_w, height: e.resize_h }; 
					image_size.maxWidth = image_size.width;
					image_size.maxHeight = image_size.height;
					
					if (typeof im != 'undefined' && im.initImg)
						changeImg((im.initImg + e.path), image_size);
					//else changeImg('http://www.maryjane.ru/ajax/initImg/?url='+e.path, image_size);
					else if (!dev) changeImg('http://www.maryjane.ru/ajax/initImg/?url='+e.path, image_size);
					else if (dev) changeImg('http://www.maryjane.ru/'+e.path, image_size);
					
					
					//jQuery('#allskins_form').submit();
				} else {
					jQuery('#response > .error').text('Ошибка: ' + e.message).show();
				}
			},
			'onError'	  : function () { jQuery('#uploadifyQueue').hide(); },
			'onSelect'    : function (a,b,c) 
			{ 
				jQuery('#uploadifyQueue').show();
				//jQuery('#prv_uploadify').uploadifySettings('hideButton', true);
				jQuery('#response > .error').hide();
				jQuery('#response > .success').hide();
			}
		});
	}*/
		
	}
}

