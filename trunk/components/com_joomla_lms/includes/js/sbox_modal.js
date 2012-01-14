/**
 * jlmsSqueezeBox - Expandable Lightbox
 *
 * Allows to open various content as modal,
 * centered and animated box.
 *
 * Inspired by
 *  ... Lokesh Dhakar	- The original Lightbox v2
 *  ... Cody Lindley	- ThickBox
 *  ... Harald Kirschner	- The original SqueezeBox 1.0rc1
 *
 * @version		1.0
 *
 * @license		JoomlaLMS license
 * @author		Denis Pavlysh <den [at] joomlalms.com>
 * @copyright	JoomlaLMS
 */
var jlmsSqueezeBox = {

	presets: {
		size: {x: 600, y: 450},
		sizeLoading: {x: 200, y: 150},
		marginInner: {x: 20, y: 20},
		marginImage: {x: 150, y: 200},
		handler: false,
		adopt: null,
		closeWithOverlay: true,
		zIndex: 65555,
		overlayOpacity: 0.7,
		classWindow: '',
		classOverlay: '',
		disableFx: false,
		onOpen: Class.empty,
		onClose: Class.empty,
		onUpdate: Class.empty,
		onResize: Class.empty,
		onMove: Class.empty,
		onShow: Class.empty,
		onHide: Class.empty,
		fxOverlayDuration: 250,
		fxResizeDuration: 750,
		fxContentDuration: 250,
		ajaxOptions: {}
	},

	initialize: function(options) {
		if (this.options) return this;
		this.presets = $merge(this.presets, options)
		this.setOptions(this.presets);
		this.build();
		this.listeners = {
			//window: this.reposition.bind(this, [null]),	//old
			window: this.reposition.bind(this, null),		//new fix
			close: this.close.bind(this),
			key: this.onkeypress.bind(this)};
		this.isOpen = this.isLoading = false;
		this.window.close = this.listeners.close;
		return this;
	},

	build: function() {
		this.overlay = new Element('div', {
			id: 'sbox-overlay',
			styles: {
				display: 'none',
				zIndex: this.options.zIndex
			}
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
		this.btnClose = new Element('a', {
			id: 'sbox-btn-close',
			href: '#'
		});
		this.content = new Element('div', {
			id: 'sbox-content'
		});
		this.window = new Element('div', {
			id: 'sbox-window',
			styles: {
				display: 'none',
				zIndex: this.options.zIndex + 2
			}
		}).adopt(this.btnClose, this.content, this.footerBtn, this.footerTitle);

		if (!window.ie6) {
			this.overlay.setStyles({
				position: 'fixed',
				top: 0,
				left: 0
			});
			this.window.setStyles({
				position: 'fixed',
				top: '50%',
				left: '50%'
			});
		} else {
			this.overlay.style.setExpression('marginTop', 'document.documentElement.scrollTop + "px"');
			this.window.style.setExpression('marginTop', '0 - parseInt(this.offsetHeight / 2) + document.documentElement.scrollTop + "px"');

			this.overlay.setStyles({
				position: 'absolute',
				top: '0%',
				left: '0%'
				//,marginTop: "expression(document.documentElement.scrollTop + 'px')"
			});

			this.window.setStyles({
				position: 'absolute',
				top: '0%',
				left: '0%'
				//,marginTop: "(expression(0 - parseInt(this.offsetHeight / 2) + document.documentElement.scrollTop + 'px')"
			});
		}

		$(document.body).adopt(this.overlay, this.window);

		this.fx = {
			overlay: this.overlay.effect('opacity', {
				duration: this.options.fxOverlayDuration,
				wait: false}).set(0),
			window: this.window.effects({
				duration: this.options.fxResizeDuration,
				wait: false}),
			content: this.content.effect('opacity', {
				duration: this.options.fxContentDuration,
				wait: false}).set(0)
		};
	},

	addClick: function(el) {
		return el.addEvent('click', function() {
			if (this.fromElement(el)) return false;
		}.bind(this));
	},

	fromElement: function(el, options) {
		this.initialize();
		
		/*Max moding LMS*/
		var reg = new RegExp('[^jlms_modal\\s].+', 'i');
		if(reg.test(el.className)){
			var arr_class = reg.exec(el.className);
			var grp_class = arr_class[0];
			
			var all_links = $$('.'+grp_class);
			var cur_index;
			var max_index;
			cur_index = 0;
			max_index = (all_links.length - 1);
			all_links.each(function(link, index){
				if(link == el){
					cur_index = index;	
				}
			});
			if (cur_index > 0 && cur_index <= max_index) {
				this.btnPrev.setStyles({'visibility': 'visible'}).removeEvents("click").addEvent('click', function(e){
					new Event(e).stop();
					jlmsSqueezeBox.fromElement(all_links[cur_index - 1]);
				});
			} else if(cur_index == 0){
				this.btnPrev.setStyles({'visibility': 'hidden'});
			}
			if (cur_index >= 0 && cur_index < max_index) {
				this.btnNext.setStyles({'visibility': 'visible'}).removeEvents("click").addEvent('click', function(e){
					new Event(e).stop();
					jlmsSqueezeBox.fromElement(all_links[cur_index + 1]);
				});
			} else if(cur_index == max_index){
				this.btnNext.setStyles({'visibility': 'hidden'});
			}
		} else {
			this.btnPrev.setStyles({'visibility': 'hidden'});
			this.btnNext.setStyles({'visibility': 'hidden'});
		}
		if(el.title && el.title != ''){
			this.footerTitle.setStyles({'display': 'block'}).getChildren()[0].getChildren()[0].getChildren()[0].getChildren()[1].setHTML('<div>'+el.title+'</div>');	
		} else {
			this.footerTitle.setStyles({'display': 'none'});
		}
		/*Max moding LMS*/
		
		this.element = $(el);
		/*Simon LMS*/
		var sizes = window.getSize();
		var rel_options = Json.evaluate(this.element.rel);
		this.rel_size = {
			x: rel_options.size.x,
			y: rel_options.size.y
		}
		if( !rel_options.size.x ) { rel_options.size.x = sizes.size.x - 50; }
		if( !rel_options.size.y ) { rel_options.size.y = sizes.size.y - 80;	}
		/*Simon LMS*/

		//if (this.element && this.element.rel) options = $merge(options || {}, Json.evaluate(this.element.rel));
		if (this.element && this.element.rel) options = $merge(options || {}, rel_options );	
		
		this.setOptions(this.presets, options);
		this.assignOptions();
		this.url = (this.element ? (this.options.url || this.element.href) : el) || '';

		if (this.options.handler) {
			var handler = this.options.handler;
			return this.setContent(handler, this.parsers[handler].call(this, true));
		}
		var res = false;
		for (var key in this.parsers) {
			if ((res = this.parsers[key].call(this))) return this.setContent(key, res);
		}
		return this;
	},

	assignOptions: function() {
		this.overlay.setProperty('class', this.options.classOverlay);
		this.window.setProperty('class', this.options.classWindow);
	},

	close: function(e) {
		if (e) new Event(e).stop();
		if (!this.isOpen) return this;
		this.fx.overlay.start(0).chain(this.toggleOverlay.bind(this));
		this.window.setStyle('display', 'none');
		this.trashImage();
		this.toggleListeners();
		this.isOpen = null;
		this.fireEvent('onClose', [this.content]).removeEvents();
		this.options = {};
		this.setOptions(this.presets).callChain();
		return this;
	},

	onError: function() {
		if (this.image) this.trashImage();
		this.setContent('Error during loading');
	},

	trashImage: function() {
		if (this.image) this.image = this.image.onload = this.image.onerror = this.image.onabort = null;
	},

	setContent: function(handler, content) {
		this.content.setProperty('class', 'sbox-content-' + handler);
		this.applyTimer = this.applyContent.delay(this.fx.overlay.options.duration, this, [this.handlers[handler].call(this, content)]);
		if (this.overlay.opacity) return this;
		this.toggleOverlay(true);
		this.fx.overlay.start(this.options.overlayOpacity);
		this.reposition();
		return this;
	},

	applyContent: function(content, size) {
		this.applyTimer = $clear(this.applyTimer);
		this.hideContent();
		if (!content) this.toggleLoading(true);
		else {
			if (this.isLoading) this.toggleLoading(false);
			this.fireEvent('onUpdate', [this.content], 20);
		}
		this.content.empty()[['string', 'array', false].contains($type(content)) ? 'setHTML' : 'adopt'](content || '');
		this.callChain();
		if (!this.isOpen) {
			this.toggleListeners(true);
			this.resize(size, true);
			this.isOpen = true;
			this.fireEvent('onOpen', [this.content]);
		} else this.resize(size);
	},

	resize: function(size, instantly) {
		var sizes = window.getSize();
		this.size = $merge(this.isLoading ? this.options.sizeLoading : this.options.size, size);

		/*Simon LMS*/
		var mLeft = - this.size.x/2;
		if( this.rel_size.x ==  0 )
			mLeft = - ( this.size.x/2 + 15 ) ;

		var mTop = - this.size.y/2;
		if( this.rel_size.y ==  0 )
			mTop = - ( this.size.y/2 + 15 ) ;
		/*Simon LMS*/

		var to = {
			width: this.size.x,
			height: this.size.y,
			marginLeft: mLeft,
			marginTop: mTop
			//left: (sizes.scroll.x + (sizes.size.x - this.size.x - this.options.marginInner.x) / 2).toInt(),
			//top: (sizes.scroll.y + (sizes.size.y - this.size.y - this.options.marginInner.y) / 2).toInt()
		};
		$clear(this.showTimer || null);
		this.hideContent();
		if (!instantly) this.fx.window.start(to).chain(this.showContent.bind(this));
		else {
			this.window.setStyles(to).setStyle('display', '');
			this.showTimer = this.showContent.delay(50, this);
		}
		this.reposition(sizes);
	},

	toggleListeners: function(state) {
		var task = state ? 'addEvent' : 'removeEvent';
		this.btnClose[task]('click', this.listeners.close);
		if (this.options.closeWithOverlay) this.overlay[task]('click', this.listeners.close);
		document[task]('keydown', this.listeners.key);
		window[task]('resize', this.listeners.window);
		window[task]('scroll', this.listeners.window);
	},

	toggleLoading: function(state) {
		this.isLoading = state;
		this.window[state ? 'addClass' : 'removeClass']('sbox-loading');
		if (state) this.fireEvent('onLoading', [this.window]);
	},

	toggleOverlay: function(state) {
		this.overlay.setStyle('display', state ? '' : 'none');
		$(document.body)[state ? 'addClass' : 'removeClass']('body-overlayed');
	},

	showContent: function() {
		if (this.content.opacity) this.fireEvent('onShow', [this.window]);
		this.fx.content.start(1);
	},

	hideContent: function() {
		if (!this.content.opacity) this.fireEvent('onHide', [this.window]);
		this.fx.content.stop().set(0);
	},

	onkeypress: function(e) {
		switch (e.key) {
			case 'esc':
			case 'x':
				this.close();
				break;
		}
	},

	reposition: function(sizes) {
		sizes = sizes || window.getSize();
		
		this.overlay.setStyles({
			//'left': sizes.scroll.x, 'top': sizes.scroll.y,
			width: sizes.size.x,
			height: sizes.size.y
		});
		/*
		this.window.setStyles({
			left: (sizes.scroll.x + (sizes.size.x - this.window.offsetWidth) / 2).toInt(),
			top: (sizes.scroll.y + (sizes.size.y - this.window.offsetHeight) / 2).toInt()
		});
		*/
		this.fireEvent('onMove', [this.overlay, this.window, sizes]);
	},

	removeEvents: function(type){
		if (!this.$events) return this;
		if (!type) this.$events = null;
		else if (this.$events[type]) this.$events[type] = null;
		return this;
	},

	parsers: {
		'image': function(preset) {
			return (preset || this.url.test(/\.(jpg|jpeg|png|gif|bmp)$/i)) ? this.url : false;
		},
		'adopt': function(preset) {
			if ($(this.options.adopt)) return $(this.options.adopt);
			if (preset || ($(this.element) && !this.element.parentNode)) return $(this.element);
			var bits = this.url.match(/#([\w-]+)$/);
			return bits ? $(bits[1]) : false;
		},
		'url': function(preset) {
			return (preset || (this.url && !this.url.test(/^javascript:/i))) ? this.url: false;
		},
		'iframe': function(preset) {
			return (preset || this.url) ? this.url: false;
		},
		'string': function(preset) {
			return true;
		}
	},

	handlers: {
		'image': function(url) {
			this.image = new Image();
			var events = {
				loaded: function() {
					var win = {x: window.getWidth() - this.options.marginImage.x, y: window.getHeight() - this.options.marginImage.y};
					var size = {x: this.image.width, y: this.image.height};
					for (var i = 0; i < 2; i++)
						if (size.x > win.x) {
							size.y *= win.x / size.x;
							size.x = win.x;
						} else if (size.y > win.y) {
							size.x *= win.y / size.y;
							size.y = win.y;
						}
					size = {x: parseInt(size.x), y: parseInt(size.y)};
					if (window.webkit419) this.image = new Element('img', {'src': this.image.src});
					else $(this.image);
					this.image.setProperties({
						'width': size.x,
						'height': size.y});
					this.applyContent(this.image, size);
				}.bind(this),
				failed: this.onError.bind(this)
			};
			(function() {
				this.src = url;
			}).delay(10, this.image);
			this.image.onload = events.loaded;
			this.image.onerror = this.image.onabort = events.failed;
		},
		'adopt': function(el) {
			return el.clone();
		},
		'url': function(url) {
			this.ajax = new Ajax(url, this.options.ajaxOptions);
			this.ajax.addEvent('onSuccess', function(resp) {
				this.applyContent(resp);
				this.ajax = null;
			}.bind(this));
			this.ajax.addEvent('onFailure', this.onError.bind(this));
			this.ajax.request.delay(10, this.ajax);
		},
		'iframe': function(url) {
			return new Element('iframe', {
				'src': url,
				'id': 'sbox-window-object-iframe',
				'frameBorder': 0,
				'width': this.options.size.x,
				'height': this.options.size.y
			});
		},
		'string': function(str) {
			return str;
		}
	},

	extend: $extend
};

jlmsSqueezeBox.extend(Events.prototype);
jlmsSqueezeBox.extend(Options.prototype);
jlmsSqueezeBox.extend(Chain.prototype);