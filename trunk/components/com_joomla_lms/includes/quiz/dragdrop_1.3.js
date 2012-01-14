var QuizDragDrop = new Class({
	initialize: function(el, options){
		var that = this;	
	
		this.box = $(el);
		
		//this.dropzone = $$('#'+this.box.id+' div.'+options.dropitems+'zone')[0];
		//this.dragzone = $$('#'+this.box.id+' div.'+options.dragitems+'zone')[0];
		
		this.dropitems = $$('#'+this.box.id+' div.'+options.dropitems) || [];
		this.dragitems = $$('#'+this.box.id+' div.'+options.dragitems) || [];
		
		this.drop_container = [];
		this.dropitems.each(function(drp, i){
			that.drop_container[i] = 0;
		});
		
		this.rundragdrop();
	},
	
	rundragdrop: function(){
		
		var draggableOptions = {
			droppables: this.dropitems,
			onStart: function(draggable, droppable){
				draggable.setStyles({'z-index': 2});
			},
			onDrag: function(draggable, droppable){
				draggable.setOpacity(.5);
				draggable.setStyles({'z-index': 2});
			},
			onComplete: function(draggable, droppable){
				draggable.setOpacity(1);
				draggable.setStyles({'z-index': 1});
			},
		    onEnter: function(draggable, droppable){
				droppable.setStyle('background', '#E79D35');
		    },
		    onLeave: function(draggable, droppable){
				droppable.setStyle('background', '#DDDDDD');
		    },
		    onDrop: function(draggable, droppable){
				if(droppable){
			    	droppable.setStyle('background', '#DDDDDD');
				}	
		    
		    	var drag_id = draggable ? this.getNID(draggable.id, 'drag') : 0;
				var drop_id = droppable ? this.getNID(droppable.id, 'drop') : 0;
				if(this.checkData(drag_id, drop_id) && droppable){
					//this.dropzone.adopt(el);
					var base_coordinates = this.box.getCoordinates();
					var base_styles = this.box.getStyles('margin-top', 'margin-left', 'padding-top', 'padding-left');
					var data_coordinates = droppable.getCoordinates();
					var data_styles = droppable.getStyles('margin-top', 'margin-left', 'padding-top', 'padding-left');
					var top = data_coordinates.top - data_styles['margin-top'].toInt() - data_styles['padding-top'].toInt() - base_coordinates.top - base_styles['margin-top'].toInt() - base_styles['padding-top'].toInt() + 2;
					var left = data_coordinates.left - data_styles['margin-left'].toInt() - data_styles['padding-left'].toInt() - base_coordinates.left - base_styles['margin-left'].toInt() - base_styles['padding-left'].toInt() + 2;
					draggable.setStyles({'position': 'absolute', 'top': top, 'left': left});
				} else {
					draggable.setStyles({'position': 'relative', 'top': 0, 'left': 0});
				}
				this.setData(drag_id, drop_id);
		    }.bind(this)
		}
			
		this.dragitems.makeDraggable(draggableOptions);
	},
	
	alldropwhite: function(){
		this.dropitems.each(function(drp){
			var dropFx = drp.effect('background-color', {wait: true});
			dropFx.set('#ffffff');
		});
	},
	
	checkData: function(drag_id, drop_id){
		if(drop_id){
			var indx_drop = drop_id - 1;
			if(this.drop_container[indx_drop] &&  this.drop_container[indx_drop] != drag_id){
				return false;
			} else 
			if(!this.drop_container[indx_drop] || this.drop_container[indx_drop] == drag_id){
				return true;
			}
		}
	},
	
	setData: function(drag_id, drop_id){
		if(drop_id){
			for(i=0;i<this.drop_container.length;i++){
				if(this.drop_container[i] == drag_id){
					this.drop_container[i] = 0;
				}
			}
			var indx_drop = drop_id - 1;	
			this.drop_container[indx_drop] = drag_id;
		} else {
			for(i=0;i<this.drop_container.length;i++){
				if(this.drop_container[i] == drag_id){
					this.drop_container[i] = 0;
				}
			}
		}
		this.setArrayForQuiz(drag_id, drop_id);
	},
	
	setArrayForQuiz: function(drag_id, drop_id){
		var index_drag = drag_id - 1;
		var index_drop = drop_id - 1;
		
		ids_in_cont[index_drop] = drag_id;
		cont_for_ids[index_drag] = drop_id;
		
		for(var i=0;i<ids_in_cont.length;i++){
			if(ids_in_cont[i] == drag_id && i != index_drop){
				ids_in_cont[i] = 0;
			}
		}
		
//		$('test').set('html', 'drag_index= '+index_drag+' drop_index= '+index_drop+'<br /> drag_id= '+drag_id+' drop_id= '+drop_id+'<br /> cont_for_ids= '+cont_for_ids+'<br /> ids_in_cont= '+ids_in_cont);
	},
	
	getNID: function(el_id, type){
		if(type == 'drag'){
			var reg=/ddiv_(\d+)/ 
		} else 
		if(type == 'drop'){
			var reg=/cdiv_(\d+)/ 
		}
		var result = reg.exec(el_id);
		
		return result[1].toInt();
	},
	
	debug: function(){
//		var my_test = new Element('div', {'id': 'test'});
//		my_test.injectInside($('jq_quiz_container'));	
	
//		console.log('dropzone');
//		console.log(this.dropzone);
//		console.log('dragzone');
//		console.log(this.dragzone);
//		console.log('dropitems');
//		console.log(this.dropitems);
//		console.log('dragitems');
//		console.log(this.dragitems);
		
		console.log(this.drop_container);
		console.log(ids_in_cont);
		console.log(cont_for_ids);
	}
});