<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_reporting{
	
	function generationHTML($results, $reporting_header, $tmpl_name, $prefix_title){
		
		ob_start();
		?>
		<table>
			<tr class="title_report">
				<td>
					<?php echo ucwords($prefix_title);?>
				</td>
			</tr>
			<tr>
				<td>
					<table class="after_header_info">
						<tr>
							<td class="date_title">
								<?php echo str_replace(':', '', _JLMS_DATE);?>:
							</td>
							<td class="date_value">
								<?php echo date("d F Y", time());?>
							</td>
						</tr>
						<?php
						if(isset($reporting_header['groups']) && count($reporting_header['groups'])){
							$groups = $reporting_header['groups'];
							foreach($groups as $n=>$group){
								?>
								<tr>
									<?php
									if(isset($reporting_header['name_groups'][$n])){
										?>
										<td class="usergroup_title">
											<?php echo $reporting_header['name_groups'][$n];?>:
										</td>
										<?php
									}
									?>
									<td class="usergroup_value">
										<?php echo $group;?>
									</td>
								</tr>
								<?php
							}
						}
						?>
						<?php
						if(isset($reporting_header['users']) && count($reporting_header['users'])){
							$users = $reporting_header['users'];
							foreach($users as $n=>$user){
								?>
								<tr>
									<?php
									if(isset($reporting_header['name_users'][$n])){
										?>
										<td class="usergroup_title">
											<?php echo $reporting_header['name_users'][$n];?>
										</td>
										<?php
									}
									?>
									<td class="usergroup_value">
										<?php echo $user;?>
									</td>
								</tr>
								<?php
							}
						}
						?>
						<?php
						if(isset($reporting_header['categories']) && count($reporting_header['categories'])){
							$categories = $reporting_header['categories'];
							foreach($categories as $n=>$category){
								?>
								<tr>
									<?php
									if(isset($reporting_header['name_categories'][$n])){
										?>
										<td class="category_title">
											<?php echo $reporting_header['name_categories'][$n];?>
										</td>
										<?php
									}
									?>
									<td class="category_value">
										<?php echo $category;?>
									</td>
								</tr>
								<?php
							}
						}
						?>
					</table>
					<br />
				</td>
			</tr>
			<?php
			if($tmpl_name == 'user_report'){
				if(isset($results['course_info']) && count($results['course_info'])){
					$course_info = $results['course_info'];
					foreach($course_info as $n=>$course){
						?>
						<tr>
							<td class="title">
								<?php echo $course['course_name'];?>
							</td>
						</tr>
						<tr>
							<td class="title_after">
								<?php echo _JLMS_REPORTING_ACCESSED_TIMES.': '.$course['hits'];?>
							</td>
						</tr>
						<?php
						if(isset($results['data'][$n]) && count($results['data'][$n])){
							?>
							<tr>
								<td>
									<table class="hits_data" cellspacing="0" cellpadding="0">
										<?php
										$data = $results['data'][$n];
										foreach($data as $m=>$tr){
											?>
											<tr>
												<?php
												foreach($tr as $td){
													if($m){
													?>
													<td class="hit_value" align="center">
														<?php echo $td;?>
													</td>
													<?php
													} else {
													?>
													<th class="hit_title">
														<?php echo $td;?>
													</th>
													<?php
													}
												}
												?>
											</tr>
											<?php
										}
										?>
									</table>
								</td>
							</tr>
							<?php
						}
						?>
						<tr>
							<td>
								&nbsp;
							</td>
						</tr>
						<?php
					}
				}
			} else {
			?>
			<tr>
				<td>
					<table cellspacing="0" cellpadding="0">
						<tr>
							<?php
							if($tmpl_name == 'access_report' || $tmpl_name == 'completion_report'){
							?>
							<td colspan="<?php echo count($results['results'][0]);?>">
								<table class="hits_data" cellspacing="0" cellpadding="0">
									<tr>
										<th class="header_title">
											<?php echo str_replace(':', '', _JLMS_UI_USERNAME);?>
										</td>
										<th class="header_title">
											<?php echo str_replace(':', '', _JLMS_UI_NAME);?>
										</td>
									</tr>
									<?php
									if(isset($results['results']) && count($results['results'])){
										$data = $results['results'];
										$k = 2;
										foreach($data as $item){
											?>
											<tr class="row_<?php echo $k;?>">
												<td class="header_value">
													<?php echo $item['username'];?>
												</td>
												<td class="header_value">
													<?php echo $item['name'];?>
												</td>
											</tr>
											<?php
											$k = 3 - $k;
										}
									}
									if(isset($results['results_total_hits']) && $results['results_total_hits']){
										?>
										<tr class="row_total">
											<th colspan="2" align="center">
												<?php echo _JLMS_REPORTING_TOTAL;?>:
											</th>
										</tr>
										<?php
									}
									?>
								</table>
							</td>
							<td>
								<table class="hits_data" cellspacing="0" cellpadding="0">
									<?php
									if(isset($results['title_courses']) && count($results['title_courses'])){
										?>
										<tr>
											<?php
											$title_courses = $results['title_courses'];
											foreach($title_courses as $title_course){
												?>
												<th class="hit_title">
													<?php echo $title_course['course_name'];?>
												</th>
												<?php
											}
											?>
										</tr>
										<?php
										if(isset($results['results_hit']) && $results['results_hit']){
											$results_hit = $results['results_hit'];
											$k = 2;
											foreach($results_hit as $items){
												?>
												<tr class="row_<?php echo $k;?>">
													<?php
													if(isset($items) && count($items)){
														foreach($items as $item){
															?>
															<td class="hit_value" align="center">
																<?php echo $item;?>
															</td>
															<?php
														}
													}
													?>
												</tr>
												<?php
												$k = 3 - $k;
											}
										}
										if(isset($results['results_total_hits']) && $results['results_total_hits']){
											$results_total_hits = $results['results_total_hits'];
											?>
											<tr class="row_total">
												<?php
												foreach($results_total_hits as $item){
													?>
													<th align="center">
														<?php echo $item['hits'];?>
													</th>
													<?php
												}
												?>
											</tr>
											<?php
										}
									}
									
									?>
								</table>
							</td>
							<?php
							} else
							if($tmpl_name == 'gradebook_report'){
							?>
							<td colspan="<?php echo count($results['data_info'][0]);?>">
								<table class="hits_data" cellspacing="0" cellpadding="0">
									<?php
									if(isset($results['data_info']) && count($results['data_info'])){
										$data_info = $results['data_info'];
										$k = 2;
										foreach($data_info as $n=>$items){
											$tr_class = '';
											if($n){
												$tr_class = 'class="row_'.$k.'"';
											}
											?>
											<tr <?php echo $tr_class;?>>
											<?php
												if(isset($items) && count($items)){
													foreach($items as $item){
														if($n){
															?>
															<td class="header_value">
																<?php echo $item;?>
															</td>
															<?php
														} else {
															?>
															<th class="header_title">
																<?php echo $item;?>
															</th>	
															<?php
														}
													}
												}
											?>
											</tr>
											<?php
											$k = 3 - $k;
										}
									}
									?>
								</table>
							</td>
							<td>
								<table class="hits_data" cellspacing="0" cellpadding="0">
									<?php
									if(isset($results['data_grade']) && count($results['data_grade'])){
										$data_grade = $results['data_grade'];
										$k = 2;
										foreach($data_grade as $n=>$items){
											$tr_class = '';
											if($n){
												$tr_class = 'class="row_'.$k.'"';
											}
											?>
											<tr <?php echo $tr_class;?>>
											<?php
												if(isset($items) && count($items)){
													foreach($items as $item){
														if($n){
															?>
															<td class="hit_value">
																<?php echo $item;?>
															</td>
															<?php
														} else {
															?>
															<th class="hit_title">
																<?php echo $item;?>
															</th>	
															<?php
														}
													}
												}
											?>
											</tr>
											<?php
											$k = 3 - $k;
										}
									}
									?>
								</table>
							</td>
							<?php
							}
							?>
						</tr>
					</table>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
		<?php
		$html = ob_get_contents();
		ob_get_clean();
		
		return $html;
	}
	
	function exportXLS($results, $reporting_header, $tmpl_name, $prefix_title){
		global $JLMS_DB, $JLMS_CONFIG, $JLMS_CONFIG;
		
		$text_to_xls = JLMS_reporting::generationHTML($results, $reporting_header, $tmpl_name, $prefix_title);
		
		$prefix_title = str_replace(' ', '_', $prefix_title);
		if(strlen($text_to_xls)){
			JLMS_reporting::outputXLS($text_to_xls, $prefix_title);
		}
	}
	
	function outputXLS($data_xls, $tmpl_name){
		global $JLMS_CONFIG;
		$file_name = $tmpl_name.'_'.date("dMY");
		if (preg_match('/Opera(\/| )([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
			$UserBrowser = "Opera";
		}
		elseif (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
			$UserBrowser = "IE";
		} else {
			$UserBrowser = '';
		}
						// UTF8 support
		$header_to_xls = '<meta http-equiv="content-type" content="text/html; charset=utf-8" />'."\n";
						//CSS
		$header_to_xls .= JLMS_reporting::addCSSStyles();
		header("Content-type: application/vnd.ms-excel");
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		header("Accept-Ranges: bytes");
		header("Content-Length: ".strlen(trim($header_to_xls.$data_xls)));
		header('Content-Disposition: attachment; filename="'.$file_name.'.xls"');
		if ($UserBrowser == 'IE') {
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Pragma: no-cache');
		}
		echo $header_to_xls.$data_xls;
		exit();
	}
	
	function outputCSV($data, $tmpl_name){
		$data_csv = '';
		if(count($data)){
			foreach($data as $n=>$d){
				$data_csv .= implode(",", $d). "\n";
			}
			
			$file_name = $tmpl_name.'_'.date("dMY");
			if (preg_match('/Opera(\/| )([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
				$UserBrowser = "Opera";
			}
			elseif (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) {
				$UserBrowser = "IE";
			} else {
				$UserBrowser = '';
			}
			header("Content-type: application/csv");
			header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header("Content-Length: ".strlen(trim($data_csv)));
			header('Content-Disposition: attachment; filename="'.$file_name.'.csv"');
			if ($UserBrowser == 'IE') {
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			} else {
				header('Pragma: no-cache');
			}
			echo $data_csv;
		}
		exit();
	}
	
	function addCSSStyles(){
		ob_start();
		?>
			<style type="text/css">
				*{
					font-family: Calibri;
					font-size: 11pt;
				}
				
				.title_report{
					background: #dbe5f1;
					font-size: 26pt;
					color: #0070c0;
				}
				
				.title_report td{
					border: 1pt dotted #0070c0;
				}
				
				.after_header_info td{
					border: 1pt dotted #0070c0;
				}
				
				.date_title{
					color: #0070d6;
					text-align: left;
					font-weight: bold;
				}
				
				.date_value{
					text-align: left;
				}
				
				.usergroup_title{
					color: #0070d6;
					text-align: left;
					font-weight: bold;
				}
				
				.usergroup_value{
					text-align: left;
				}
				
				.category_title{
					color: #0070d6;
					text-align: left;
					font-weight: bold;
				}
				
				.category_value{
					text-align: left;
				}
				
				.title{
					font-size: 20pt;
					color: #788901;
					padding: 10px 0;
					font-weight: bold;
				}
				
				.title_after{
				
				}
				
				.header_title{
					font-weight: bold;
					background: #538ed5;
					color: #ffffff;
					text-align: center;
					vertical-align: middle;
					height: 30pt;
					padding: 0 5px;
					white-space: nowrap;
				}
				
				.header_value{
					text-align: left;
					vertical-align: middle;
				}
				
				.hit_title{
					font-weight: bold;
					background: #808080;
					color: #ffffff;
					text-align: center;
					vertical-align: middle;
					height: 30pt;
					padding: 0 5px;
					white-space: nowrap;
				}
				
				.hit_value{
					text-align: center;
					vertical-align: middle;
					white-space: nowrap;
				}
				
				.row_1{
					background: #edf6f9;
				}
				
				.row_2{
					background: #ffffff;
				}
				
				.row_total{
					background: #c5d9f1;
					font-weight: bold;
				}
				
				.row_total .cell_total{
					text-align: left;
				}
				
				.row_total .cell_hit_total{
					text-align: center;
				}
				
				.user_data, .hits_data{
					border-collapse: collapse;
				}
				
				.user_data th, .hits_data th{
					border-top: 0.5pt solid #ffffff;
					border-right: 0.5pt solid #ffffff;
				}
				
				.user_data tr.row_total th.cell_total, 
				.hits_data tr.row_total th.cell_total,
				.hits_data tr.row_total th.cell_hit_total{
					border: 0.5pt solid #808080;
				}
				
				.user_data td, .hits_data td{
					border: 0.5pt solid #808080;
				}
				
				.hits_data td{
					border: 0px;	
				}
				#reports_quiz td.header_value,
				#reports_quiz td.hit_value{
					border: 0.5pt solid #808080;
				}
				
				#reports_quiz tr.row_1,
				#reports_quiz tr.row_2{
					background: #ffffff;
				}
				#reports_quiz .row_1 td.header_value,
				#reports_quiz .row_1 td.hit_value{
					background: #edf6f9;
				}
				#reports_quiz .row_2 td.header_value,
				#reports_quiz .row_2 td.hit_value{
					background: #ffffff;
				}
			</style>
		<?php
		$css = ob_get_contents();
		ob_get_clean();
		return $css;
	}
}
?>