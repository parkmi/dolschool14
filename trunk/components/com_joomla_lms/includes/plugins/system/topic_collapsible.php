<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

define('_JLMS_PLUGIN_TC', 'read or write comments');

$_JLMS_PLUGINS = & JLMSFactory::getPlugins();

$_JLMS_PLUGINS->registerFunction( 'onBelowCourseDetailsPage', 'TopicCollapsible' );
$_JLMS_PLUGINS->registerFunction( 'onPluginAction', 'TopicCollapsible_Action' );

//ALTER TABLE `jos_lms_topics` ADD `collapsible` INT NOT NULL ;

function TopicCollapsible($course_id){

	$db = & JFactory::getDBO();
	$user = & JFactory::getUser();
	$document = & JFactory::getDocument();
	
	$cookie_name = 'u'.$user->id.'c'.$course_id.'topics_exp';
	$cookie_value = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : '';
	$topics_expand = explode(',', $cookie_value);
	
	$tec = $topics_expand;
	
	$query = "SELECT *"
	. "\n FROM #__lms_topics_collapsible"
	. "\n WHERE 1"
	. "\n AND user_id = '".$user->id."'"
	. "\n AND course_id = '".$course_id."'"
	;
	$db->setQuery($query);
	$data = $db->loadObject();
	$data->value = isset($data->value) ? $data->value : '';
	$topics_expand = explode(',', $data->value);
	
	$tedb = $topics_expand;
	
	$topics_expand = array_merge($tec, $tedb);
	setcookie($cookie_name, '');
	
	ob_start();
		?>
		<style type="text/css">
			div.btn_collapse{
				display: block;
				position: absolute;
				right: 5px;
				top: 5px;
				
				margin: 0;
				padding: 0;
				
				font-size: 14px;
				line-height: normal;
				text-align: center;
				vertical-align: middle;
				line-height: normal;
				cursor: pointer;
			}
			
			<?php
			if(isset($topics_expand) && count($topics_expand)){
				foreach($topics_expand as $t){
					?>
					div[id=topicmain_<?php echo $t;?>] .contentmain{
						display: none;
					}
					<?php
				}
			}
			?>	
		
			/*div[id^=topicmain] .contentmain{
				display: none;
			}*/
		</style>
		<?php
	$css = ob_get_contents();
	ob_get_clean();
	$css = str_replace('<style type="text/css">', '', $css);
	$css = str_replace('</style>', '', $css);
	$document->addStyleDeclaration($css);	
	
	JHTML::_('behavior.mootools');
	
	ob_start();
		?>
		<script type="text/javascript">
			var TopicCollapsible = new Class({
				
				options: {
					topic_main_id: 'topicmain',
					topic_contentheading_class: 'contentheading',
					topic_contentmain_class: 'contentmain'
				},
				
				initialize: function(options){
					this.setOptions(options);
					
					this.path_jlms = '<?php echo JURI::base().'components/com_joomla_lms';?>';
					
					this.user_id = <?php echo $user->id;?>;
					this.course_id = <?php echo $course_id;?>;
					
					this.btn_model = this.buildButton();
					
					this.topics = null;
					this.topics_heads = []
					this.topics_contents = []
					this.topics_btns = [];
					
					this.topics_expand_ids = [];
					
					this.cookie_name = 'u'+this.user_id+'c'+this.course_id+'topics_exp';
					
					this.topics_expand_str = '<?php echo $data->value;?>';
					
					this.topics_expand = [];
//					this.topics_expand = this.getData();
					this.topics_expand = this.topics_expand_str.length ? this.topics_expand_str.split(',') : [];
					
					this.searchElements();
					this.addButtons();
				},
				
				searchElements: function(){
					this.topics = $('jlms_mainarea').getElements('div[id^='+this.options.topic_main_id+']');
					this.topics.each(function(topic, index){
						this.topics_heads.include(topic.getElements('div[class='+this.options.topic_contentheading_class+']')[0]);
						this.topics_contents.include(topic.getElements('div[class='+this.options.topic_contentmain_class+']')[0]);
					}.bind(this));
				},
				
				addButtons: function(){
					this.topics.each(function(topic, index){
						var tid = topic.id.replace('topicmain_', '');
						var property_id = topic.id.replace('topicmain_', 'btn_');
						var btn = this.btn_model.clone().setProperty('id', property_id);
						
						this.topics_heads[index].setStyle('position', 'relative').adopt(btn);
						
						this.buttonState(btn);
						this.buttonEvent(btn, index);
						
						this.topics_btns.include(btn);
					}.bind(this));
				},
				
				buttonEvent: function(btn, index){
					btn.addEvent('click', function(){
						var tid = btn.id.replace('btn_', '');
						if(this.topics_contents[index].getStyle('display') == 'none'){
							this.topics_expand.remove(tid);
							this.topics_contents[index].setStyle('display', 'block');
							this.setView(btn, 0);
						} else {
							this.topics_expand.include(tid);
							this.topics_contents[index].setStyle('display', 'none');
							this.setView(btn, 1);
						}
//						this.setData(this.topics_expand); //work with cookie
						this.setDataDB(this.topics_expand);
					}.bind(this));
				},
				
				buttonState: function(btn){
					var bid = btn.id.replace('btn_', '');
					if(this.topics_expand.length){
						this.topics_expand.each(function(tid){
							if(tid == bid){
								this.setView(btn, 1);
							}
						}.bind(this));
					} else {
						this.setView(btn, 0);
					}
				},
				
				getData: function(){
					this.cookie_value = Cookie.get(this.cookie_name);
					if(this.cookie_value){
						this.topics_expand = this.cookie_value.split(',');
					}
					return this.topics_expand;
				},
				
				setData: function(expand){
					this.cookie_value = implode(',', expand);
					Cookie.set(this.cookie_name, this.cookie_value, {duration: 365});
				},
				
				setDataDB: function(expand){
					var url_request = 'index.php?option=com_joomla_lms&task=plugin_action';
					url_request += '&folder=system';
					url_request += '&plugin=topic_collapsible';
					url_request += '&user_id='+this.user_id;
					url_request += '&course_id='+this.course_id;
					url_request += '&value='+implode(',', expand);
					url_request += '&type=1';
					
					new Json.Remote(url_request, {
						method: 'post'
					}).send();
				},
				
				buildButton: function(){
					var btn = new Element('div', {
						'id': 'btn_',
						'class': 'btn_collapse'
					});
					var img = new Element('img', {
						'src': this.path_jlms+'/lms_images/expand.png',
						'width': 24,
						'heigth': 24,
						'border': 0
					});
					btn.adopt(img);
					return btn;
				},
				
				setView: function(btn, status){
					if(status == 1){
						btn.getElements('img')[0].setProperties({
							'src': this.path_jlms+'/lms_images/collapse.png',
							'title': 'expand'
						});
					} else {
						btn.getElements('img')[0].setProperties({
							'src': this.path_jlms+'/lms_images/expand.png',
							'title': 'collapse'
						});
					}
				},
				
				
			});
			TopicCollapsible.implement(new Options);
			//helpers
			function implode( glue, pieces ) {	// Join array elements with a string
				// 
				// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
				// +   improved by: _argos
			
				return ( ( pieces instanceof Array ) ? pieces.join ( glue ) : pieces );
			}
			
			window.addEvent('domready', function(){
				new TopicCollapsible({});
			});
		</script>
		<?php
	$js = ob_get_contents();
	ob_get_clean();
	$js = str_replace('<script type="text/javascript">', '', $js);
	$js = str_replace('</script>', '', $js);
	$document->addScriptDeclaration($js);
}

function TopicCollapsible_Action(){
	$db = & JFactory::getDBO();
	
	$type = JRequest::getVar('type', 0);
	$user_id = JRequest::getVar('user_id', 0);
	$course_id = JRequest::getVar('course_id', 0);
	$value = JRequest::getVar('value', '');
	
	if($user_id && $course_id){
		$query = "SELECT *"
		. "\n FROM #__lms_topics_collapsible"
		. "\n WHERE 1"
		. "\n AND user_id = '".$user_id."'"
		. "\n AND course_id = '".$course_id."'"
		;
		$db->setQuery($query);
		$data = $db->loadObject();
		
		if($type){
			if(isset($data->id) && $data->id){
				$query = "UPDATE #__lms_topics_collapsible"
				. "\n SET value = '".$value."'"
				. "\n WHERE 1"
				. "\n AND user_id = '".$user_id."'"
				. "\n AND course_id = '".$course_id."'"
				;
			} else {
				$query = "INSERT INTO #__lms_topics_collapsible"
				. "\n (id, user_id, course_id, value)"
				. "\n VALUES"
				. "\n ('', '".$user_id."', '".$course_id."', '".$value."')"
				;
			}
			
			$db->setQuery($query);
			if($db->query()){
				echo '{"status":"ok"}';
			}
		} else {
			if(isset($data->id) && $data->id){
				echo '{"status":"ok", "value": "'.$data->value.'"}';
			} else {
				echo '{"status":"ok"}';
			}
		}
	}
}