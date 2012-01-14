<?php
	/** Libchart - PHP chart library
	*	
	* Copyright (C) 2005-2006 Jean Marc Trémeaux (jm.tremeaux at gmail.com)
	* 	
	* This library is free software; you can redistribute it and/or
	* modify it under the terms of the GNU Lesser General Public
	* License as published by the Free Software Foundation; either
	* version 2.1 of the License, or (at your option) any later version.
	* 
	* This library is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	* Lesser General Public License for more details.
	* 
	* You should have received a copy of the GNU Lesser General Public
	* License along with this library; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	* 
	*/


	$lc_filepath = dirname(__FILE__);
	require_once ( $lc_filepath . '/classes/Point.php');
	require_once ( $lc_filepath . '/classes/Axis.php');
	require_once ( $lc_filepath . '/classes/Color.php');
	require_once ( $lc_filepath . '/classes/Primitive.php');
	require_once ( $lc_filepath . '/classes/Text.php');
	require_once ( $lc_filepath . '/classes/Chart.php');
	require_once ( $lc_filepath . '/classes/PieChart.php');
	require_once ( $lc_filepath . '/classes/BarChart.php');
	require_once ( $lc_filepath . '/classes/LineChart.php');
	require_once ( $lc_filepath . '/classes/VerticalChart.php');
	require_once ( $lc_filepath . '/classes/HorizontalChart.php');
	require_once ( $lc_filepath . '/classes/MultiVerticalChart.php');

	function JLMS_cleanLibChartCache($secs = 900) { // 15 min
		global $JLMS_CONFIG;
		if ($JLMS_CONFIG->get('temp_folder', '')) {
			$now = time();
			$cleaned = $now - $secs;
			$dir = $JLMS_CONFIG->getCfg('absolute_path') . "/".$JLMS_CONFIG->get('temp_folder', '')."/";
			$cache_dir = opendir( $dir );
			while ($entryname = readdir( $cache_dir )) {
				if ($entryname != '.' and $entryname != '..') {
					if (is_dir( $dir . $entryname )) {
						//do nothing
					} else {
						if (strlen($entryname) == 47) {
							if (intval(substr($entryname,0,10)) < $cleaned) {
								unlink( $dir . $entryname );
							}
						}
						/*
						
						if (preg()) {
							unlink( $dir . $entryname );
						}*/
					}
				}
			}
			closedir( $cache_dir );
		}
	}
?>