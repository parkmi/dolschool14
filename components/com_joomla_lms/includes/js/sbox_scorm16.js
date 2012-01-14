/**
 * jlmsSqueezeBox_scorm - Expandable Lightbox
 *
 * Allows to open various content as modal,
 * centered and animated box.
 *
 * Dependencies: MooTools 1.2
 *
 * Inspired by
 *  ... Lokesh Dhakar	- The original Lightbox v2
 *
 * @version		1.1 rc4
 *
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */
 
var jlmsSqueezeBox_scorm = {
	
	presets: {
			onOpen: $empty,
			onClose: $empty,
			onUpdate: $empty,
			onResize: $empty,
			onMove: $empty,
			onShow: $empty,
			onHide: $empty,
			size: {x: 600, y: 450},
			sizeLoading: {x: 200, y: 150},
			marginInner: {x: 20, y: 20},
			marginImage: {x: 50, y: 75},
			handler: false,
			target: null,
			closable: true,
			closeBtn: true,
			zIndex: 65555,
			overlayOpacity: 0.7,
			classWindow: '',
			classOverlay: '',
			overlayFx: {},
			resizeFx: {},
			contentFx: {},
			parse: false, // 'rel'
			parseSecure: false,
			shadow: true,
			document: null,
			ajaxOptions: {}
	},

	initialize: function(presets) {
		if (this.options) return this;

		this.presets = $merge(this.presets, presets);
		this.doc = this.presets.document || document;
		this.options = {};
		this.setOptions(this.presets).build();
		this.bound = {
			window: this.reposition.bind(this, [null]),
			scroll: this.checkTarget.bind(this),
			close: this.close.bind(this),
			key: this.onKey.bind(this)
		};
		this.isOpen = this.isLoading = false;
		return this;
	},

	build: function() {
		this.overlay = new Element('div', {
			id: 'sbox-overlay',
			styles: {display: 'none', zIndex: this.options.zIndex}
		});
		this.btnPrev = new Element('div', {
			id: 'sbox-btn-prev',
			styles: {
				'visibility': 'hidden'	
			}
		});
		this.btnNext = new Element('div', {
			id: 'sbox-btn-next',
			styles: {
				'visibility': 'hidden'	
			}
		});
		this.footerBtn = new Element('div', {
			id: 'sbox-btn'
		}).adopt(this.btnPrev, this.btnNext);
		
		this.td_title_left = new Element('td', {
			id: 'sbox-title-left'	
		});
		this.td_title_main = new Element('td', {
			id: 'sbox-title-main'	
		});
		this.td_title_right = new Element('td', {
			id: 'sbox-title-right'			
		});
		this.tr_title = new Element('tr').adopt(this.td_title_left, this.td_title_main, this.td_title_right);
		this.tbody_title = new Element('tbody').adopt(this.tr_title);
		this.table_title = new Element('table', {
			align: 'center',
			cellSpacing: '0',
			cellPadding: '0',
			border: '0'
		}).adopt(this.tbody_title);
		
		this.footerTitle = new Element('div', {
			id: 'sbox-title',
			styles: {
				'display': 'none'	
			}
		}).adopt(this.table_title);
						
		this.win = new Element('div', {
			id: 'sbox-window',
			styles: {display: 'none', zIndex: this.options.zIndex + 2}
		})		
		if (this.options.shadow) {
			if (Browser.Engine.webkit420) {
				this.win.setStyle('-webkit-box-shadow', '0 0 10px rgba(0, 0, 0, 0.7)');
			} else if (!Browser.Engine.trident4) {
				var shadow = new Element('div', {'class': 'sbox-bg-wrap'}).inject(this.win);
				var relay = function(e) {
					this.overlay.fireEvent('click', [e]);
				}.bind(this);
				['n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw'].each(function(dir) {
					new Element('div', {'class': 'sbox-bg sbox-bg-' + dir}).inject(shadow).addEvent('click', relay);
				});
			}
		}
		
		this.content = new Element('div', {id: 'sbox-content'}).inject(this.win);
		this.closeBtn = new Element('a', {id: 'sbox-btn-close', href: '#'}).inject(this.win);
		this.footerBtn.inject(this.win);		
		this.footerTitle.inject(this.win);
		
		this.fx = {
			overlay: new Fx.Tween(this.overlay, $merge({
				property: 'opacity',
				onStart: Events.prototype.clearChain,
				duration: 250,
				link: 'cancel'
			}, this.options.overlayFx)).set(0),
			win: new Fx.Morph(this.win, $merge({
				onStart: Events.prototype.clearChain,
				unit: 'px',
				duration: 750,
				transition: Fx.Transitions.Quint.easeOut,
				link: 'cancel',
				unit: 'px'
			}, this.options.resizeFx)),
			content: new Fx.Tween(this.content, $merge({
				property: 'opacity',
				duration: 250,
				link: 'cancel'
			}, this.options.contentFx)).set(0)
		};
		$(this.doc.body).adopt(this.overlay, this.win);
	},

	assign: function(to, options) {
		return ($(to) || $$(to)).addEvent('click', function(){
			return !jlmsSqueezeBox_scorm.fromElement(this, options);
		});
	},
	
	open: function(subject, options) {
		this.initialize();
		
		if (this.element != null) this.trash();
		this.element = $(subject) || false;
		
		/*Max moding LMS*/
		var reg = new RegExp('[^modal\\s].+', 'i');
		if(reg.test(this.element.className)){
			var arr_class = reg.exec(this.element.className);
			var grp_class = arr_class[0];
			
			var all_links = $$('.'+grp_class);
			var cur_index;
			var max_index;
			var curElement = this.element; 
			
			cur_index = 0;
			max_index = (all_links.length - 1);
			all_links.each(function(link, index){				
				if(link == curElement){					
					cur_index = index;	
				}
			});			
			if (cur_index > 0 && cur_index <= max_index) {
				this.btnPrev.setStyles({'visibility': 'visible'}).removeEvents("click").addEvent('click', function(e){
					new Event(e).stop();
					all_links[cur_index - 1].fireEvent('click');
				});
			} else if(cur_index == 0){
				this.btnPrev.setStyles({'visibility': 'hidden'});
			}
			if (cur_index >= 0 && cur_index < max_index) {
				this.btnNext.setStyles({'visibility': 'visible'}).removeEvents("click").addEvent('click', function(e){
					new Event(e).stop();					
					all_links[cur_index + 1].fireEvent('click');
				});
			} else if(cur_index == max_index){				
				this.btnNext.setStyles({'visibility': 'hidden'});
			}
		} else {
			this.btnPrev.setStyles({'visibility': 'hidden'});
			this.btnNext.setStyles({'visibility': 'hidden'});
		}
		if(this.element.title && this.element.title != ''){
			this.footerTitle.setStyles({'display': 'block'}).getChildren()[0].getChildren()[0].getChildren()[0].getChildren()[1].set( 'html', '<div>'+this.element.title+'</div>');	
		} else {
			this.footerTitle.setStyles({'display': 'none'});
		}		
		/*Max moding LMS*/
		
		this.setOptions($merge(this.presets, options || {}));
		
		if (this.element && this.options.parse) {
			var obj = this.element.getProperty(this.options.parse);					
			if (obj && (obj = JSON.decode(obj, this.options.parseSecure))) 
			{
				/* simon { */
				var box = this.doc.getSize();
				if( !obj.size.x || obj.size.x == 100 ) 
				{
					obj.size.x = (box.x - 50); 	
				}
				
				if( !obj.size.y || obj.size.y == 100 ) 
				{
					obj.size.y = (box.y - 100); 	
				}			
				/* } simon */				
								
				this.setOptions(obj);	
			};
		}
		this.url = ((this.element) ? (this.element.get('href')) : subject) || this.options.url || '';

		this.assignOptions();
		
		var handler = handler || this.options.handler;
		if (handler) return this.setContent(handler, this.parsers[handler].call(this, true));
		var ret = false;
		return this.parsers.some(function(parser, key) {
			var content = parser.call(this);
			if (content) {
				ret = this.setContent(key, content);
				return true;
			}
			return false;
		}, this);
	},
	
	fromElement: function(from, options) {
		return this.open(from, options);
	},

	assignOptions: function() {
		this.overlay.set('class', this.options.classOverlay);
		this.win.set('class', this.options.classWindow);
		if (Browser.Engine.trident4) this.win.addClass('sbox-window-ie6');
	},

	close: function(e) {
		var stoppable = ($type(e) == 'event');
		if (stoppable) e.stop();
		if (!this.isOpen || (stoppable && !$lambda(this.options.closable).call(this, e))) return this;
		this.fx.overlay.start(0).chain(this.toggleOverlay.bind(this));
		this.win.setStyle('display', 'none');
		var tmp_iframe = $('sbox-window-object-iframe');
		if (tmp_iframe) {
			var tmp_doc = null;  
			if(tmp_iframe.contentDocument)
				tmp_doc = tmp_iframe.contentDocument;
			else if(tmp_iframe.contentWindow)
				tmp_doc = tmp_iframe.contentWindow.document;
			else if(tmp_iframe.document)
				tmp_doc = tmp_iframe.document;
			if (tmp_doc) {
				if (tmp_doc.getElementById('main')) {
					// destroy SCORM iframe and fire onbeforeunload SCORM event
					tmp_doc.getElementById('main').parentNode.removeChild(tmp_doc.getElementById('main'));
				}
			}
		}
		this.fireEvent('onClose', [this.content]);
		this.trash();
		this.toggleListeners();
		this.isOpen = false;
		return this;
	},

	trash: function() {
		this.element = this.asset = null;
		this.content.empty();
		this.options = {};
		this.removeEvents().setOptions(this.presets).callChain();
	},

	onError: function() {
		this.asset = null;
		this.setContent('string', this.options.errorMsg || 'An error occurred');
	},

	setContent: function(handler, content) {
		if (!this.handlers[handler]) return false;
		this.content.className = 'sbox-content-' + handler;
		this.applyTimer = this.applyContent.delay(this.fx.overlay.options.duration, this, this.handlers[handler].call(this, content));
		if (this.overlay.retrieve('opacity')) return this;
		this.toggleOverlay(true);
		this.fx.overlay.start(this.options.overlayOpacity);
		return this.reposition();
	},

	applyContent: function(content, size) {
		if (!this.isOpen && !this.applyTimer) return;
		this.applyTimer = $clear(this.applyTimer);
		this.hideContent();
		if (!content) {
			this.toggleLoading(true);
		} else {
			if (this.isLoading) this.toggleLoading(false);
			this.fireEvent('onUpdate', [this.content], 20);
		}
		if (content) {
			if (['string', 'array'].contains($type(content))) this.content.set('html', content);
			else if (!this.content.hasChild(content)) this.content.adopt(content);
		}
		this.callChain();
		if (!this.isOpen) {
			this.toggleListeners(true);
			this.resize(size, true);
			this.isOpen = true;
			this.fireEvent('onOpen', [this.content]);
		} else {
			this.resize(size);
		}
	},

	resize: function(size, instantly) {
		this.showTimer = $clear(this.showTimer || null);
		var box = this.doc.getSize(), scroll = this.doc.getScroll();
		this.size = $merge((this.isLoading) ? this.options.sizeLoading : this.options.size, size);
		var to = {
			width: this.size.x,
			height: this.size.y,
			left: (scroll.x + (box.x - this.size.x - this.options.marginInner.x) / 2).toInt(),
			top: (scroll.y + (box.y - this.size.y - this.options.marginInner.y) / 2).toInt()
		};
		this.hideContent();
		if (!instantly) {
			this.fx.win.start(to).chain(this.showContent.bind(this));
		} else {
			this.win.setStyles(to).setStyle('display', '');
			this.showTimer = this.showContent.delay(50, this);
		}
		return this.reposition();
	},

	toggleListeners: function(state) {
		var fn = (state) ? 'addEvent' : 'removeEvent';
		this.closeBtn[fn]('click', this.bound.close);
		this.overlay[fn]('click', this.bound.close);
		this.doc[fn]('keydown', this.bound.key)[fn]('mousewheel', this.bound.scroll);
		this.doc.getWindow()[fn]('resize', this.bound.window)[fn]('scroll', this.bound.window);
	},

	toggleLoading: function(state) {
		this.isLoading = state;
		this.win[(state) ? 'addClass' : 'removeClass']('sbox-loading');
		if (state) this.fireEvent('onLoading', [this.win]);
	},

	toggleOverlay: function(state) {
		var full = this.doc.getSize().x;
		this.overlay.setStyle('display', (state) ? '' : 'none');
		this.doc.body[(state) ? 'addClass' : 'removeClass']('body-overlayed');
		if (state) {
			this.scrollOffset = this.doc.getWindow().getSize().x - full;
			this.doc.body.setStyle('margin-right', this.scrollOffset);
		} else {
			this.doc.body.setStyle('margin-right', '');
		}
	},

	showContent: function() {
		if (this.content.get('opacity')) this.fireEvent('onShow', [this.win]);
		this.fx.content.start(1);
	},

	hideContent: function() {
		if (!this.content.get('opacity')) this.fireEvent('onHide', [this.win]);
		this.fx.content.cancel().set(0);
	},

	onKey: function(e) {
		switch (e.key) {
			case 'esc': this.close(e);
			case 'up': case 'down': return false;
		}
	},

	checkTarget: function(e) {
		return this.content.hasChild(e.target);
	},

	reposition: function() {
		var size = this.doc.getSize(), scroll = this.doc.getScroll(), ssize = this.doc.getScrollSize();
		this.overlay.setStyles({
			width: ssize.x + 'px',
			height: ssize.y + 'px'
		});
		this.win.setStyles({
			left: (scroll.x + (size.x - this.win.offsetWidth) / 2 - this.scrollOffset).toInt() + 'px',
			top: (scroll.y + (size.y - this.win.offsetHeight) / 2).toInt() + 'px'
		});
		return this.fireEvent('onMove', [this.overlay, this.win]);
	},

	removeEvents: function(type){
		if (!this.$events) return this;
		if (!type) this.$events = null;
		else if (this.$events[type]) this.$events[type] = null;
		return this;
	},

	extend: function(properties) {
		return $extend(this, properties);
	},

	handlers: new Hash(),

	parsers: new Hash()

};

jlmsSqueezeBox_scorm.extend(new Events($empty)).extend(new Options($empty)).extend(new Chain($empty));

jlmsSqueezeBox_scorm.parsers.extend({

	image: function(preset) {
		return (preset || (/\.(?:jpg|png|gif)$/i).test(this.url)) ? this.url : false;
	},

	clone: function(preset) {
		if ($(this.options.target)) return $(this.options.target);
		if (this.element && !this.element.parentNode) return this.element;
		var bits = this.url.match(/#([\w-]+)$/);
		return (bits) ? $(bits[1]) : (preset ? this.element : false);
	},

	ajax: function(preset) {
		return (preset || (this.url && !(/^(?:javascript|#)/i).test(this.url))) ? this.url : false;
	},

	iframe: function(preset) {
		return (preset || this.url) ? this.url : false;
	},

	string: function(preset) {
		return true;
	}
});

jlmsSqueezeBox_scorm.handlers.extend({

	image: function(url) {
		var size, tmp = new Image();
		this.asset = null;
		tmp.onload = tmp.onabort = tmp.onerror = (function() {
			tmp.onload = tmp.onabort = tmp.onerror = null;
			if (!tmp.width) {
				this.onError.delay(10, this);
				return;
			}
			var box = this.doc.getSize();
			box.x -= this.options.marginImage.x;
			box.y -= this.options.marginImage.y;
			size = {x: tmp.width, y: tmp.height};
			for (var i = 2; i--;) {
				if (size.x > box.x) {
					size.y *= box.x / size.x;
					size.x = box.x;
				} else if (size.y > box.y) {
					size.x *= box.y / size.y;
					size.y = box.y;
				}
			}
			size.x = size.x.toInt();
			size.y = size.y.toInt();
			this.asset = $(tmp);
			tmp = null;
			this.asset.width = size.x;
			this.asset.height = size.y;
			this.applyContent(this.asset, size);
		}).bind(this);
		tmp.src = url;
		if (tmp && tmp.onload && tmp.complete) tmp.onload();
		return (this.asset) ? [this.asset, size] : null;
	},

	clone: function(el) {
		if (el) return el.clone();
		return this.onError();
	},

	adopt: function(el) {
		if (el) return el;
		return this.onError();
	},

	ajax: function(url) {
		var options = this.options.ajaxOptions || {};
		this.asset = new Request.HTML($merge({
			method: 'get',
			evalScripts: false
		}, this.options.ajaxOptions)).addEvents({
			onSuccess: function(resp) {
				this.applyContent(resp);
				if (options.evalScripts !== null && !options.evalScripts) $exec(this.asset.response.javascript);
				this.fireEvent('onAjax', [resp, this.asset]);
				this.asset = null;
			}.bind(this),
			onFailure: this.onError.bind(this)
		});
		this.asset.send.delay(10, this.asset, [{url: url}]);
	},

	iframe: function(url) {
		this.asset = new Element('iframe', $merge({
			src: (url + ((url.indexOf('?') == -1)?'?':'&')+'height=' + this.options.size.y),
			frameBorder: 0,
			width: this.options.size.x,
			height: this.options.size.y
		}, this.options.iframeOptions));
		if (this.options.iframePreload) {
			this.asset.addEvent('load', function() {
				this.applyContent(this.asset.setStyle('display', ''));
			}.bind(this));
			this.asset.setStyle('display', 'none').inject(this.content);
			return false;
		}
		return this.asset;
	},

	string: function(str) {
		return str;
	}

});

jlmsSqueezeBox_scorm.handlers.url = jlmsSqueezeBox_scorm.handlers.ajax;
jlmsSqueezeBox_scorm.parsers.url = jlmsSqueezeBox_scorm.parsers.ajax;
jlmsSqueezeBox_scorm.parsers.adopt = jlmsSqueezeBox_scorm.parsers.clone;