var TimeTracker = new Class({
	initialize: function(options){
		this.is_active = 0;
		
		this.interval = options.interval || 1;
		this.interval = this.interval * 1000;
		
		this.ajax_url_handler = options.url_handler || 'index.php';
		this.ajax_method = options.method || 'post';
		
		this.course_id = options.course_id || 0;
		this.user_id = options.user_id || 0;
		
		this.resource_type = options.resource_type || 0;
		this.resource_id = options.resource_id || 0;
		this.item_id = options.item_id || 0;
		this.item_type = 0;
		
		this.item_id_old = null;
		
		this.show_online = $defined(options.show_online) ? options.show_online : 0;
		this.show_online_pulse = $defined(options.show_online_pulse) ? options.show_online_pulse : 0;
		this.show_status = $defined(options.show_status) ? options.show_status : 0;
		
//		this.vposition = options.vposition || 'bottom';
//		this.hposition = options.hposition || 'right';
		
		this.opacity = 1;
		
		this.el_id = null;
		
		this.el_view = null;
		this.el_view_course = null;
		this.el_view_resource = null;
		this.el_online = null;
		this.el_status = null;
		
		this._auto = null;
		
		window.addEvents({
			'click': this.active_on.bind(this),
			'dblclick': this.active_on.bind(this),
			'mousemove': this.active_on.bind(this),
			'mousewheel': this.active_on.bind(this),
			'scroll': this.active_on.bind(this),
			'keypress': this.active_on.bind(this),
			'resize': this.active_on.bind(this),
			'move': this.active_on.bind(this)
		});
	},
	
	start: function(){
		this.build_view();
		this.unhide_online();
		if(this.show_online){
			this.loop(1);
		}
		this.period();
	},
	
	period: function(){
		$clear(this._auto);
		this._auto = this.loop.periodical(this.interval, this);	
	},
	
	stop: function(){
		$clear(this._auto);
	},
	
	getitemid: function(item_id, item_type){
		if(!$defined(item_type)){
			item_type = 0;
		}
		this.item_id_old = this.item_id;
		this.item_id = item_id;
		this.item_type = item_type;
	},
	
	getelid: function(pre_text){
		var elid = pre_text;
		elid += '_'+this.course_id;
		elid += '_'+this.user_id;
		elid += this.resource_type ? '_'+this.resource_type : '';
		elid += this.resource_id ? '_'+this.resource_id : '';
//		elid += this.item_id ? '_'+this.item_id : '';
		return elid;
	},
	
	build_view: function(){
		if(this.show_online || this.show_dump){
//			this.el_id = this.getelid('tt_view');
			this.el_id = 'tt_view';
			if($defined($(this.el_id))){
				this.el_view = $(this.el_id);
			} else {
				this.el_view = new Element('div', {
					'id': this.el_id,
					'styles': {
						'position': 'fixed', 
						'display': 'block',
						'right': '15px',
						'bottom': '15px',
						'z-index': 100,
						'width': 'auto'
					}
				}).setHTML('');
				$ES('body')[0].adopt(this.el_view);
			}
			if($defined(this.el_view)){
				this.el_id = 'tt_view_time';
				if($defined($(this.el_id))){
					this.el_view_time = $(this.el_id);
				} else {
					this.el_view_time = new Element('div', {
						'id': this.el_id,
						'styles': {
							'position': 'relative', 
							'display': 'block',
							'width': 'auto',
							'float': 'right'
						}
					}).setHTML('');
					this.el_view.adopt(this.el_view_time);
				}
				
				this.build_online();
				this.build_status();
			} 
		}
	},
	
	build_online: function(){
		if(this.show_online){
			this.el_id = this.getelid('tt_online');
			if($defined($(this.el_id))){
				this.el_online = $(this.el_id);
			} else {
				this.el_online = new Element('div', {
					'id': this.el_id,
					'styles': {
						'float': 'right',
						'display': 'block',
						'z-index': 100,
						'width': 'auto',
						'opacity': this.opacity, 
						//'color': '#339966',
						'margin-left': '20px',
						'padding': '0px 10px',
						'text-align': 'center',
//						'border': '2px dotted #cccccc',
						'-moz-border-radius': '10px 10px 10px 10px',
						'-moz-box-shadow': '0 0 8px 6px'
					}
				}).setHTML('');
				if(this.resource_id){
					this.el_online.setStyles({
						'float': 'left',
						'color': '#99CC33'
					});
				}
				this.el_view_time.adopt(this.el_online);
			}
			if($defined(this.el_online)){
				this.online_fx = new Fx.Style(this.el_online, 'opacity', {duration: 200, transition: Fx.Transitions.linear});
			}
		}
	},
	
	unhide_online: function(){
		if($defined(this.el_online)){
			this.el_online.setHTML('').setStyles({'display': 'block'});
		}
	},
	
	hide_online: function(){
		if($defined(this.el_online)){
			this.el_online.setHTML('').setStyles({'display': 'none'});
		}
	},
	
	toogle_online: function(){
		if($defined(this.el_online)){
			if(this.el_online.getStyle('display') == 'block'){
				this.hide_online();
			} else {
				this.unhide_online();
			}
		}
	},
	
	build_status: function(){
		if(this.show_status){
//			this.el_id = this.getelid('tt_status');
			this.el_id = 'tt_status';
			if($defined($(this.el_id))){
				this.el_status = $(this.el_id);
			} else {
				this.el_status = this.el_online.clone()
				.setProperties({
					'id': this.el_id
				})
				.setStyles({
					'margin': 0,
					'padding': '0px 5px'
				}).setHTML('');
			}
			this.el_view.adopt(this.el_status);
		}
	},
	
	status: function(){
		if($defined(this.el_status)){
			var status = 'active';
			this.el_status.setStyles({'color': '#cccccc'});
			if(this.is_active){
//				status = 'active';
				this.el_status.setStyles({'color': '#00cc00'});
			}
			this.el_status.setHTML(status);
		}
	},
	
	active_on: function(){
		this.is_active = 1;
		this.status();
	},
	
	active_off: function(){
		if(this.is_active && this.show_online_pulse && this.online_fx != null){
			this.online_fx.start(0, this.opacity);
		}
		this.is_active = 0;
		this.status();
	},
	
	requestData: function(data_vars){
		new Ajax(this.ajax_url_handler, {
			method: this.ajax_method,
			update: this.el_online,
			data: data_vars,
			onSuccess: this.active_off.bind(this)
		}).request();
	},
	
	loop: function(start){
		if(!$defined(start)){
			start = 0;
		}
		data_vars = 'no_html=1';
		data_vars += '&course_id='+this.course_id;
		data_vars += '&user_id='+this.user_id;
		data_vars += this.resource_type ? '&resource_type='+this.resource_type : '';
		data_vars += this.resource_id ? '&resource_id='+this.resource_id : '';
		data_vars += this.item_id ? '&item_id='+this.item_id : '';
		data_vars += this.item_type ? '&item_type='+this.item_type : '';
		if(start || (this.item_type == 6)){
			this.is_active = 1;
		}
		data_vars += '&is_active='+this.is_active;
		if(start || this.item_id != this.item_id_old){
			data_vars += '&start=1';
			this.item_id_old = this.item_id;
		}
		this.requestData(data_vars);
	},
	
	debug: function(){
		console.log('course_id= '+this.course_id);
		console.log('user_id= '+this.user_id);
		console.log('resource_type= '+this.resource_type);
		console.log('resource_id= '+this.resource_id);
		console.log('item_id= '+this.item_id);
		console.log('item_type= '+this.item_type);
		console.log('####################################');
		console.log(' ');
	}
});