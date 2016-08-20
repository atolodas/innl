jQuery(document).ready(function() {
	PTM.init();
});

// ***************** API OBJECT *******************
PTM = {
	// ********** options **********
	ptmContainerClass: 'ptm-container',
	selectionBoxShadow: '0 0 4px #FF0000',
	highlightBoxShadow: '0 0 4px #FF00FF',
	hoverBoxShadow: '0 0 4px #00FF00',
	
	// ********** fields **********
	selected: jQuery(),
	highlighted: jQuery(),
	hovered: jQuery(),
	ptmContainer: jQuery(),
	clipboard: jQuery(),
	
	// ********** tmp variables **********
	mode: 0,
	
	// ********** main methods **********
	deleteSeleted: function() {
		if (this.selected.length > 0) {
			this.selected.remove();
			this.removeSelection();
		}
	},
	duplicateSelected: function() {
		if (this.selected.length > 0) {
			var clone = this.selected.clone();
			clone.css('box-shadow', this.selected.data('baseShadow') || '');
			this.selected.after(clone);
		}
	},
	cutSelected: function() {
		if (this.selected.length > 0) {
			this.clipboard = this.selected;
			this.selected.remove();
			this.removeSelection();
		}
	},
	pasteBeforeSelected: function() {
		if (this.selected.length > 0 && this.clipboard.length > 0) {
			this.addBeforeSelected(this.clipboard);
		}
	},
	addBeforeSelected: function(markup) {
		if (this.selected.length > 0) {
			this.selected.before(markup);
		}
	},
	moveSelectedRowBefore: function() {
		if (this.selected.length > 0 && this.selected.hasClass('row')) {
			var prevEl = this.findPrevRow();
			if (prevEl.length > 0) {
				this.selected.after(prevEl.clone());
				prevEl.after(this.selected);
				prevEl.remove();
			}
		}
	},
	moveSelectedRowAfter: function() {
		if (this.selected.length > 0 && this.selected.hasClass('row')) {
			var nextEl = this.findNextRow();
			if (nextEl.length > 0) {
				this.selected.after(nextEl.clone());
				nextEl.after(this.selected);
				nextEl.remove();
			}
		}
	},
	moveSelectedLeft: function(selector) {
		if (this.selected.length > 0 && this.selected.is(selector) && this.selected.prev(selector).length) {
			this.selected.prev(selector).before(this.selected);
		}
	},
	moveSelectedRight: function(selector) {
		if (this.selected.length > 0 && this.selected.is(selector) && this.selected.next(selector).length) {
			this.selected.next(selector).after(this.selected);
		}
	},

	// ********** service private methods **********
	init: function() {
		jQuery('head').append('<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">');
		jQuery('body').append(this.htmlSnippet);
		this.ptmContainer = jQuery('.' + this.ptmContainerClass);
		
		this.bindSelection();
		this.bindAllTabs();
		
		this.initMenu();
		
		// draggable
		var cont = this.ptmContainer;
		// this.ptmContainer.draggable({snap: 'html'});
		// jQuery(window).resize(function() {
		// 	if (cont.offset().left + cont.outerWidth() > jQuery(window).width()) {
		// 		cont.css('left', jQuery(window).width() - cont.outerWidth() + 'px');
		// 	}
		// });
	},
	
	initMenu: function() {
		var self = this;
		var res = '';
		jQuery('header nav.nav-main ul.nav').children('li').each(function() {
			res += self.checkMenuItem(jQuery(this));
		});
		jQuery('#menuEditor .editor-wrapper .menu-list').append(res);
	},
	
	checkMenuItem: function(el) {
		var self = this;
		var res = '';
		res += '<li class="menu-item"><button type="button" class="btn btn-default-editor"><i class="fa fa-plus"></i></button><button type="button" class="btn btn-default-editor"><i class="fa fa-minus"></i></button><span>' + el.children('a').text() + '</span><button type="button" class="btn btn-default-editor pull-right" id="menuDel1"><i class="fa fa-remove"></i></button>';
		var hasSubmenu = el.children('ul').length;
		if (hasSubmenu) {
			res += '<ul class="sub-menu-list">';
			el.children('ul').children('li').each(function() {
				res += self.checkMenuItem(jQuery(this));
			});
			res += '</ul>';
		}			
		res += '</li>';
		return res;
	},
	
	select: function(el) {
		this.removeAllMarks();
		this.selected = el;
		el.css('box-shadow', this.selectionBoxShadow);
	},
	
	removeSelection: function() {
		this.ptmContainer.find('#contentTextarea').val('');
		this.selected.css('box-shadow', this.selected.data('baseShadow') || '');
		this.selected = jQuery();
	},
	
	highlight: function(el) {
		this.removeAllMarks();
		this.highlighted = el;
		el.data('baseShadow', el.css('box-shadow'));
		el.css('box-shadow', this.highlightBoxShadow);
	},
	
	removeHighlight: function() {
		this.highlighted.css('box-shadow', this.highlighted.data('baseShadow') || '');
		this.highlighted = jQuery();
	},
	
	removeHover: function() {
		this.hovered.css('box-shadow', this.hovered.data('baseShadow') || '');
		this.hovered = jQuery();
	},
	
	removeAllMarks: function() {
		this.removeSelection();
		this.removeHighlight();
		this.removeHover();
	},
	
	intersect: function(set1, set2) {
		var res = false;
		set1.each(function() {
			if (set2.index(jQuery(this)) != -1) {
				res = true;
				return;
			}
		});
		return res;
	},
	
	parseColClass: function() {
		var c = this.selected.attr('class');
		var cParts = c.split(' ');
		for (var i = 0; i < cParts.length; i++) {
			var tmp = cParts[i].split('-');
			if (tmp.length == 3) {
				var key = tmp[1];
				var value = tmp[2];
				this.ptmContainer.find('input[rel="' + key + '"]').val(value);
			}
		}
	},
	
	findPrevRow: function() {
		var allRows = jQuery('.row');
		var curI = allRows.index(this.selected);
		while (curI > 0) {
			curI--;
			var r = allRows.eq(curI);
			if (this.selected.parents('.row').index(r) == -1) {
				return r;
			}
		}
		return jQuery();
	},
	
	findNextRow: function() {
		var allRows = jQuery('.row');
		var curI = allRows.index(this.selected);
		while (curI < allRows.length - 1) {
			curI++;
			var r = allRows.eq(curI);
			if (this.selected.parents('.row').index(r) == -1) {
				return r;
			}
		}
		return jQuery();
	},
	
	// ********** service binding methods **********
	bindAllTabs: function() {
		this.bindRowTab();
		this.bindColTab();
		this.bindTextTab();
		this.bindMenuTab();
		this.bindCodeTab();
	},
	
	bindSelection: function() {
		var self = this;
		jQuery('body').on('click', function(e) {
			if (self.mode != 0 && !jQuery(e.target).hasClass(self.ptmContainerClass) && (jQuery(e.target).parents('.' + self.ptmContainerClass).length == 0)) {
				switch (self.mode) {
					case 1:
						if (jQuery(e.target).hasClass('row')) {
							self.mode = 0;
							self.select(jQuery(e.target));
						} else if (jQuery(e.target).parents('.row').length) {
							self.mode = 0;
							self.select(jQuery(e.target).parents('.row'));
						}
						break;
					case 2:
						if (jQuery(e.target).is('*[class^=col]')) {
							self.mode = 0;
							self.select(jQuery(e.target));
							self.parseColClass();
						} else if (jQuery(e.target).parents('*[class^=col]').length) {
							self.mode = 0;
							self.select(jQuery(e.target).parents('*[class^=col]'));
							self.parseColClass();
						}
						break;
					case 3:
						self.mode = 0;
						self.select(jQuery(e.target));
						jQuery('#contentTextarea').val(jQuery(e.target).html());
						break;
						
					case 4:
						break;
					
					case 5:
						self.mode = 0;
						self.select(jQuery(e.target));
						jQuery('#codeTextarea').val(jQuery(e.target).html());
						break;
				}
				return false;
			}
		});
	},
	
	bindHover: function() {
		var self = this;
		jQuery('body *').hover(function(e) {
			if (self.mode != 0 && !jQuery(e.target).hasClass(self.ptmContainerClass) && (jQuery(e.target).parents('.' + self.ptmContainerClass).length == 0)) {
				switch (self.mode) {
					case 1:
						if (jQuery(e.target).hasClass('row')) {
							self.removeHover();
							self.hovered = jQuery(e.target);
							self.hovered.data('baseShadow', jQuery(this).css('box-shadow'));
							self.hovered.css('box-shadow', self.hoverBoxShadow);
						} else if (jQuery(e.target).parents('.row').length) {
							self.removeHover();
							self.hovered = jQuery(e.target).parents('.row');
							self.hovered.data('baseShadow', jQuery(this).css('box-shadow'));
							self.hovered.css('box-shadow', self.hoverBoxShadow);
						}
						break;
					case 2:
						if (jQuery(e.target).is('*[class^=col]')) {
							self.removeHover();
							self.hovered = jQuery(e.target);
							self.hovered.data('baseShadow', jQuery(this).css('box-shadow'));
							self.hovered.css('box-shadow', self.hoverBoxShadow);
						} else if (jQuery(e.target).parents('*[class^=col]').length) {
							self.removeHover();
							self.hovered = jQuery(e.target).parents('.row');
							self.hovered.data('baseShadow', jQuery(this).css('box-shadow'));
							self.hovered.css('box-shadow', self.hoverBoxShadow);
						}
						break;
					case 3:
						self.removeHover();
						self.hovered = jQuery(e.target);
						self.hovered.data('baseShadow', jQuery(this).css('box-shadow'));
						self.hovered.css('box-shadow', self.hoverBoxShadow);
						break;
						
					case 4:
						break;
						
					case 5:
						self.removeHover();
						self.hovered = jQuery(e.target);
						self.hovered.data('baseShadow', jQuery(this).css('box-shadow'));
						self.hovered.css('box-shadow', self.hoverBoxShadow);
						break;
				}
			}
		}, function(e) {
			if (self.mode != 0) {
				if (self.highlighted.index(jQuery(e.target)) > 0 || self.intersect(jQuery(e.target).parents(), self.highlighted)) {
					self.highlight(self.highlighted);
				} else {
					self.removeHover();
				}
			}
		});
	},
	
	bindRowTab: function() {
		var self = this;
		this.ptmContainer.find('#rowOn').click(function() {
			self.highlight(jQuery('.row'));
			self.bindHover();
			self.mode = 1;
			return false;
		});
		this.ptmContainer.find('#rowCopy').click(function() {
			self.duplicateSelected();
			return false;
		});
		this.ptmContainer.find('#rowDel').click(function() {
			self.deleteSeleted();
			return false;
		});
		this.ptmContainer.find('#rowAdd').click(function() {
			self.addBeforeSelected('<div class="row">This is new empty row!</div>');
			return false;
		});
		this.ptmContainer.find('#rowUp').click(function() {
			self.moveSelectedRowBefore();
			return false;
		});
		this.ptmContainer.find('#rowDown').click(function() {
			self.moveSelectedRowAfter();
			return false;
		});
	},
	
	bindColTab: function() {
		var self = this;
		this.ptmContainer.find('#colOn').click(function() {
			self.highlight(jQuery('*[class^=col]'));
			self.bindHover();
			self.mode = 2;
			return false;
		});
		this.ptmContainer.find('#colCut').click(function() {
			self.cutSelected();
			return false;
		});
		this.ptmContainer.find('#colCopy').click(function() {
			self.duplicateSelected();
			return false;
		});
		this.ptmContainer.find('#colPaste').click(function() {
			self.pasteBeforeSelected();
			return false;
		});
		this.ptmContainer.find('#colDel').click(function() {
			self.deleteSeleted();
			return false;
		});
		this.ptmContainer.find('#colAdd').click(function() {
			self.addBeforeSelected('<div class="col-md-1">This is new empty col-1!</div>');
			return false;
		});
		this.ptmContainer.find('#colLeft').click(function() {
			self.moveSelectedLeft('*[class^=col]');
			return false;
		});
		this.ptmContainer.find('#colRight').click(function() {
			self.moveSelectedRight('*[class^=col]');
			return false;
		});
		
		// todo - объединить more и less
		this.ptmContainer.find('#colMore1, #colMore2, #colMore3, #colMore4').click(function() {
			if (self.selected.length > 0 && self.selected.is('*[class^=col]')) {
				var curVal = parseInt(jQuery(this).siblings('input').val()) || 0;
				if (curVal < 12) {
					jQuery(this).siblings('input').val(curVal + 1);
					var newClass = '';
					jQuery(this).parents('.editor-wrapper').find('input').each(function() {
						var v = parseInt(jQuery(this).val());
						if (v > 0) {
							newClass += (' col-' + jQuery(this).attr('rel') + '-' + v);
						}
					});
					newClass = newClass.trim();
					self.selected.attr('class', newClass);				
				}
			}
			return false;
		});
		this.ptmContainer.find('#colLess1, #colLess2, #colLess3, #colLess4').click(function() {
			if (self.selected.length > 0 && self.selected.is('*[class^=col]')) {
				var curVal = parseInt(jQuery(this).siblings('input').val()) || 0;
				if (curVal > 0) {
					jQuery(this).siblings('input').val(curVal - 1);
					var newClass = '';
					jQuery(this).parents('.editor-wrapper').find('input').each(function() {
						var v = parseInt(jQuery(this).val());
						if (v > 0) {
							newClass += (' col-' + jQuery(this).attr('rel') + '-' + v);
						}
					});
					newClass = newClass.trim();
					self.selected.attr('class', newClass);				
				}
			}
			return false;
		});
	},
	
	bindTextTab: function() {
		var self = this;
		this.ptmContainer.find('#textOn').click(function() {
			self.removeAllMarks();
			self.bindHover();
			self.mode = 3;
			return false;
		});
		this.ptmContainer.find('#textCopy').click(function() {
			self.duplicateSelected();
			return false;
		});
		this.ptmContainer.find('#textDel').click(function() {
			self.deleteSeleted();
			return false;
		});
		this.ptmContainer.find('#contentTextarea').on('change paste blur keyup', function() {
			if (self.selected.length > 0) {
				self.selected.html(jQuery(this).val());
			}
		});
	},
	
	bindMenuTab: function() {
	},
	
	bindCodeTab: function() {
		var self = this;
		this.ptmContainer.find('#codeOn').click(function() {
			self.removeAllMarks();
			self.bindHover();
			self.mode = 5;
			return false;
		});		
	},
	
	// ********** html **********
	htmlSnippet: ' \
	<div class="editor-container ptm-container"> \
        <div class="editor"> \
         <div class="tab-content"> \
                <div class="tab-pane active" id="rowEditor"> \
                    <div class="editor-box"> \
                        <button type="button" class="flex-item btn btn-default-editor" id="rowOn" title="выбрать"> \
                            <i class="fa fa-power-off fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="rowCopy" title="продублировать"> \
                            <i class="fa fa-copy fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="rowDel" title="удалить"> \
                            <i class="fa fa-remove fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="rowAdd" title="добавить новый"> \
                            <i class="fa fa-plus fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="rowUp" title="переместить вверх"> \
                            <i class="fa fa-chevron-up fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="rowDown" title="переместить вниз"> \
                            <i class="fa fa-chevron-down fa-lg"></i> \
                        </button> \
                    </div> \
                </div> \
                <div class="tab-pane" id="colEditor"> \
                    <div class="editor-box"> \
                        <button type="button" class="flex-item btn btn-default-editor" id="colOn" title="выбрать"> \
                            <i class="fa fa-power-off fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="colCut" title="вырезать"> \
                            <i class="fa fa-cut fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="colCopy" title="продублировать"> \
                            <i class="fa fa-copy fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="colPaste" title="вставить"> \
                            <i class="fa fa-paste fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="colDel" title="удалить"> \
                            <i class="fa fa-remove fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="colAdd" title="добавить новый"> \
                            <i class="fa fa-plus fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="colLeft" title="переместить влево"> \
                            <i class="fa fa-chevron-left fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="colRight" title="переместить вправо"> \
                            <i class="fa fa-chevron-right fa-lg"></i> \
                        </button> \
                    </div> \
                    <div class="editor-wrapper"> \
                        <div class="editor-box"> \
                            <label for="colInpXs">XS</label> \
                            <input type="text" rel="xs" class="flex-item form-control" placeholder="" id="colInpXs"> \
                            <button type="button" class="flex-item btn btn-default-editor" id="colMore1"> \
                                <i class="fa fa-plus fa-lg"></i> \
                            </button> \
                            <button type="button" class="flex-item btn btn-default-editor" id="colLess1"> \
                                <i class="fa fa-minus fa-lg"></i> \
                            </button> \
                        </div> \
                        <div class="editor-box"> \
                            <label for="colInpSm">SM</label> \
                            <input type="text" rel="sm" class="flex-item form-control" placeholder="" id="colInpSm"> \
                            <button type="button" class="flex-item btn btn-default-editor" id="colMore2"> \
                                <i class="fa fa-plus fa-lg"></i> \
                            </button> \
                            <button type="button" class="flex-item btn btn-default-editor" id="colLess2"> \
                                <i class="fa fa-minus fa-lg"></i> \
                            </button> \
                        </div> \
                        <div class="editor-box"> \
                            <label for="colInpMd">MD</label> \
                            <input type="text" rel="md" class="flex-item form-control" placeholder="" id="colInpMd"> \
                            <button type="button" class="flex-item btn btn-default-editor" id="colMore3"> \
                                <i class="fa fa-plus fa-lg"></i> \
                            </button> \
                            <button type="button" class="flex-item btn btn-default-editor" id="colLess3"> \
                                <i class="fa fa-minus fa-lg"></i> \
                            </button> \
                        </div> \
                        <div class="editor-box"> \
                            <label for="colInpLg">LG</label> \
                            <input type="text" rel="lg" class="flex-item form-control" placeholder="" id="colInpLg"> \
                            <button type="button" class="flex-item btn btn-default-editor" id="colMore4"> \
                                <i class="fa fa-plus fa-lg"></i> \
                            </button> \
                            <button type="button" class="flex-item btn btn-default-editor" id="colLess4"> \
                                <i class="fa fa-minus fa-lg"></i> \
                            </button> \
                        </div> \
                    </div> \
                </div> \
                <div class="tab-pane" id="textEditor"> \
                    <div class="editor-box"> \
                        <button type="button" class="flex-item btn btn-default-editor" id="textOn" title="выбрать"> \
                            <i class="fa fa-power-off fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="textCopy" title="продублировать"> \
                            <i class="fa fa-copy fa-lg"></i> \
                        </button> \
                        <button type="button" class="flex-item btn btn-default-editor" id="textDel" title="удалить"> \
                            <i class="fa fa-remove fa-lg"></i> \
                        </button> \
                    </div> \
                    <div class="editor-box"> \
                        <textarea class="form-control" rows="6" id="contentTextarea"></textarea> \
                    </div> \
                </div> \
                       <div class="tab-pane" id="codeEditor"> \
                    <div class="editor-box"> \
                        <button type="button" class="flex-item btn btn-default-editor" id="codeOn" title="выбрать"> \
                            <i class="fa fa-power-off fa-lg"></i> \
                        </button> \
                    </div> \
                    <div class="editor-box"> \
                        <textarea class="form-control" rows="6" id="codeTextarea"></textarea> \
                    </div> \
                </div> \
                 <div class="tab-pane" id="none"> \
                 </div> \
            </div> \
            <ul class="nav-tabs"> \
                <li class="active"><a href="#rowEditor" data-toggle="tab">Ряды</a></li> \
                <li><a href="#colEditor" data-toggle="tab">Блоки</a></li> \
                <li><a href="#textEditor" data-toggle="tab">Текст</a></li> \
                <li><a href="#codeEditor" data-toggle="tab">Код</a></li> \
                <li><a href="#none" data-toggle="tab">Закрыть всё</a></li> \
            </ul> \
        </div> \
	</div> \
	'
}


