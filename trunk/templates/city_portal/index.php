<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );
define( 'TEMPLATEPATH', dirname(__FILE__) );
/*
-----------------------------------------
City Portal - January 2009 Shape 5 Club Template
-----------------------------------------
Site:      www.shape5.com
Email:     contact@shape5.com
@license:  Copyrighted Commercial Software
@copyright (C) 2009 Shape 5

*/
// Template Configuration    



	$s5_menu = $this->params->get ("xml_s5_menu"); 
	$s5_body_width = $this->params->get ("xml_s5_body_width");
	$s5_left_width = $this->params->get ("xml_s5_left_width");
	$s5_right_width = $this->params->get ("xml_s5_right_width");
	$s5_tooltips = $this->params->get ("xml_s5_tooltips");
	$s5_lytebox = $this->params->get ("xml_s5_lytebox");
	$s5_tab1  = $this->params->get ("xml_s5_tab1");
	$s5_registerlink = $this->params->get ("xml_s5_registerlink");
	$s5_repeatback = $this->params->get ("xml_s5_repeatback");	
	$s5_colorback = $this->params->get ("xml_s5_colorback");	
	$s5_clr_fix = $this->params->get ("xml_s5_clr_fix");	
	$s5_color = $this->params->get ("xml_s5_color");
	$s5_box_width = $this->params->get ("xml_s5_box_width");

	

////////////////////////  DO NOT EDITBELOW THIS  ////////////////////////
// Middle content calculations
if (!$this->countModules("left") && !$this->countModules("right")) { $s5_mainbody_width = (($s5_body_width) - 36); }
else if ($this->countModules("left") && !$this->countModules("right")) { $s5_mainbody_width = $s5_body_width - ($s5_left_width + 36);}
else if (!$this->countModules("left") && $this->countModules("right")) { $s5_mainbody_width = $s5_body_width - ($s5_right_width + 36);}
else if ($this->countModules("left") && $this->countModules("right")) { $s5_mainbody_width = $s5_body_width - (($s5_left_width + $s5_right_width) + 36); }

// advert 1, 2, and 3 collapse calculations 
if ($this->countModules("advert1") && $this->countModules("advert2")  && $this->countModules("advert3")) { $advert="33"; }
else if ($this->countModules("advert1") && $this->countModules("advert2") && !$this->countModules("advert3")) { $advert="50"; }
else if ($this->countModules("advert1") && !$this->countModules("advert2") && $this->countModules("advert3")) { $advert="50"; }
else if (!$this->countModules("advert1") && $this->countModules("advert2") && $this->countModules("advert3")) { $advert="50"; }
else if ($this->countModules("advert1") && !$this->countModules("advert2") && !$this->countModules("advert3")) { $advert="100"; }
else if (!$this->countModules("advert1") && $this->countModules("advert2") && !$this->countModules("advert3")) { $advert="100"; }
else if (!$this->countModules("advert1") && !$this->countModules("advert2") && $this->countModules("advert3")) { $advert="100"; }

// advert 4, 5, and 6 collapse calculations 
if ($this->countModules("advert4") && $this->countModules("advert5")  && $this->countModules("advert6")) { $advert2="33"; }
else if ($this->countModules("advert4") && $this->countModules("advert5") && !$this->countModules("advert6")) { $advert2="50"; }
else if ($this->countModules("advert4") && !$this->countModules("advert5") && $this->countModules("advert6")) { $advert2="50"; }
else if (!$this->countModules("advert4") && $this->countModules("advert5") && $this->countModules("advert6")) { $advert2="50"; }
else if ($this->countModules("advert4") && !$this->countModules("advert5") && !$this->countModules("advert6")) { $advert2="100"; }
else if (!$this->countModules("advert4") && $this->countModules("advert5") && !$this->countModules("advert6")) { $advert2="100"; }
else if (!$this->countModules("advert4") && !$this->countModules("advert5") && $this->countModules("advert6")) { $advert2="100"; }

//user1 and 2 calculations
if ($this->countModules("user1") && $this->countModules("user2")) { $user23="50"; }
else if (!$this->countModules("user1") && $this->countModules("user2")) { $user23="100";  }
else if ($this->countModules("user1") && !$this->countModules("user2")) { $user23="100";  }

//user3, 4, 5, 6 and 7 calculations
if ($this->countModules("user3") && $this->countModules("user4") && $this->countModules("user5")  && $this->countModules("user6") && $this->countModules("user7")) { $bottom4="20"; }
else if ($this->countModules("user3") && $this->countModules("user4") && $this->countModules("user5")  && $this->countModules("user6") && !$this->countModules("user7")) { $bottom4="25"; }
else if ($this->countModules("user3") && $this->countModules("user4") && $this->countModules("user5")  && !$this->countModules("user6") && $this->countModules("user7")) { $bottom4="25"; }
else if ($this->countModules("user3") && $this->countModules("user4") && !$this->countModules("user5")  && $this->countModules("user6") && $this->countModules("user7")) { $bottom4="25"; }
else if ($this->countModules("user3") && !$this->countModules("user4") && $this->countModules("user5")  && $this->countModules("user6") && $this->countModules("user7")) { $bottom4="25"; }
else if (!$this->countModules("user3") && $this->countModules("user4") && $this->countModules("user5")  && $this->countModules("user6") && $this->countModules("user7")) { $bottom4="25"; }
else if (!$this->countModules("user3") && $this->countModules("user4") && $this->countModules("user5") && $this->countModules("user6") && !$this->countModules("user7")) { $bottom4="33";  }
else if ($this->countModules("user3") && !$this->countModules("user4") && $this->countModules("user5") && $this->countModules("user6") && !$this->countModules("user7")) { $bottom4="33";  }
else if ($this->countModules("user3") && $this->countModules("user4") && !$this->countModules("user5") && $this->countModules("user6") && !$this->countModules("user7")) { $bottom4="33";  }
else if ($this->countModules("user3") && $this->countModules("user4") && $this->countModules("user5") && !$this->countModules("user6") && !$this->countModules("user7")) { $bottom4="33";  }
else if ($this->countModules("user3") && $this->countModules("user4") && !$this->countModules("user5") && !$this->countModules("user6") && $this->countModules("user7")) { $bottom4="33";  }
else if (!$this->countModules("user3") && $this->countModules("user4") && $this->countModules("user5") && !$this->countModules("user6") && $this->countModules("user7")) { $bottom4="33";  }
else if (!$this->countModules("user3") && !$this->countModules("user4") && $this->countModules("user5") && $this->countModules("user6") && $this->countModules("user7")) { $bottom4="33";  }
else if ($this->countModules("user3") && !$this->countModules("user4") && $this->countModules("user5") && !$this->countModules("user6") && $this->countModules("user7")) { $bottom4="33";  }
else if ($this->countModules("user3") && !$this->countModules("user4") && !$this->countModules("user5") && $this->countModules("user6") && $this->countModules("user7")) { $bottom4="33";  }
else if (!$this->countModules("user3") && !$this->countModules("user4") && $this->countModules("user5") && $this->countModules("user6") && !$this->countModules("user7")) { $bottom4="50"; }
else if ($this->countModules("user3") && !$this->countModules("user4") && !$this->countModules("user5") && $this->countModules("user6") && !$this->countModules("user7")) { $bottom4="50"; }
else if ($this->countModules("user3") && $this->countModules("user4") && !$this->countModules("user5") && !$this->countModules("user6") && !$this->countModules("user7")) { $bottom4="50"; }
else if (!$this->countModules("user3") && $this->countModules("user4") && $this->countModules("user5") && !$this->countModules("user6") && !$this->countModules("user7")) { $bottom4="50"; }
else if (!$this->countModules("user3") && $this->countModules("user4") && !$this->countModules("user5") && $this->countModules("user6") && !$this->countModules("user7")) { $bottom4="50"; }
else if ($this->countModules("user3") && !$this->countModules("user4") && $this->countModules("user5") && !$this->countModules("user6") && !$this->countModules("user7")) { $bottom4="50"; }
else if ($this->countModules("user3") && !$this->countModules("user4") && !$this->countModules("user5") && !$this->countModules("user6") && $this->countModules("user7")) { $bottom4="50"; }
else if (!$this->countModules("user3") && $this->countModules("user4") && !$this->countModules("user5") && !$this->countModules("user6") && $this->countModules("user7")) { $bottom4="50"; }
else if (!$this->countModules("user3") && !$this->countModules("user4") && $this->countModules("user5") && !$this->countModules("user6") && $this->countModules("user7")) { $bottom4="50"; }
else if (!$this->countModules("user3") && !$this->countModules("user4") && !$this->countModules("user5") && $this->countModules("user6") && $this->countModules("user7")) { $bottom4="50"; }
else if ($this->countModules("user3") && !$this->countModules("user4") && !$this->countModules("user5") && !$this->countModules("user6") && !$this->countModules("user7")) { $bottom4="100"; }
else if (!$this->countModules("user3") && $this->countModules("user4") && !$this->countModules("user5") && !$this->countModules("user6") && !$this->countModules("user7")) { $bottom4="100"; }
else if (!$this->countModules("user3") && !$this->countModules("user4") && $this->countModules("user5") && !$this->countModules("user6") && !$this->countModules("user7")) { $bottom4="100"; }
else if (!$this->countModules("user3") && !$this->countModules("user4") && !$this->countModules("user5") && $this->countModules("user6") && !$this->countModules("user7")) { $bottom4="100"; }
else if (!$this->countModules("user3") && !$this->countModules("user4") && !$this->countModules("user5") && !$this->countModules("user6") && $this->countModules("user7")) { $bottom4="100"; }

if (($s5_menu  == "1") || ($s5_menu  == "3")){ 
require( TEMPLATEPATH.DS."s5_no_moo_menu.php");
}
else if ($s5_menu  == "2")  {
require( TEMPLATEPATH.DS."s5_suckerfish.php");
}
$menu_name = $this->params->get ("xml_menuname");
$LiveSiteUrl = JURI::base();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<jdoc:include type="head" />
<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link href="<?php echo $LiveSiteUrl;?>/templates/city_portal/css/template_css.css" rel="stylesheet" type="text/css" media="screen" />
<link href="<?php echo $LiveSiteUrl;?>/templates/city_portal/css/editor.css" rel="stylesheet" type="text/css" media="screen" />
<?php if ($s5_lytebox  == "yes") { ?>
<link href="<?php echo $LiveSiteUrl;?>/templates/city_portal/css/lytebox.css" rel="stylesheet" type="text/css" media="screen" />
<?php } ?>
<?php if (($s5_menu  == "1") || ($s5_menu  == "2") || ($s5_menu  == "3") ) { ?>
<link href="<?php echo $LiveSiteUrl;?>/templates/city_portal/css/suckerfish.css" rel="stylesheet" type="text/css" media="screen" />
<?php } ?>
<script type="text/javascript" src="<?php echo $LiveSiteUrl;?>/templates/city_portal/js/s5_effects.js"></script>
<?php if (($s5_menu  == "1") || ($s5_menu  == "2") || ($s5_menu  == "3")) { ?>
<script type="text/javascript" src="<?php echo $LiveSiteUrl;?>/templates/city_portal/js/IEsuckerfish.js"></script>
<?php } ?>
<?php if ($s5_lytebox  == "yes") { ?>
<script type="text/javascript" src="<?php echo $LiveSiteUrl;?>/templates/city_portal/js/lytebox.js"></script>
<?php } ?>
<?php if ($s5_color  == "blue") { ?>
<link href="<?php echo $LiveSiteUrl;?>/templates/city_portal/css/blue.css" rel="stylesheet" type="text/css" media="screen" />
<?php } ?>
<?php if ($s5_color  == "orange") { ?>
<link href="<?php echo $LiveSiteUrl;?>/templates/city_portal/css/orange.css" rel="stylesheet" type="text/css" media="screen" />
<?php } ?>

<?php
$br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser.
if(strrpos($br,"msie 6") > 1) {
$is_ie6 = "yes";
} 
else {
$is_ie6 = "no";
}

$br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser.
if(strrpos($br,"safari") > 1) {
$is_safari = "yes";
} 
else {
$is_safari = "no";
}

$br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser.
if(strrpos($br,"msie 7") > 1) {
$is_ie7 = "yes";
} 
else {
$is_ie7 = "no";
}
?>
	

<style type="text/css"> 
.s5_wrap {
	width:<?php echo ($s5_body_width);?>px;}
#s5_topmiddlebody {
	width:<?php echo ($s5_body_width) - 22;?>px;}
#s5_middleleft{
	width:<?php echo ($s5_body_width);?>px;}
#s5_middleright{
	width:<?php echo ($s5_body_width) - 15;?>px;}
#s5_bottommiddlebody {
	width:<?php echo ($s5_body_width) - 22;?>px;}
#s5_user34567 {
	width:<?php echo ($s5_body_width) - 45;?>px;}
<?php if ($is_ie6 == "no") { ?>
#s5_date {
	width:<?php echo ($s5_body_width);?>px;z-index:3;font-size:14px;position:absolute;text-align:center;}
<?php } ?>
#s5_middlebody {
	width:<?php echo ($s5_body_width) - 15?>px;}
.s5_backmiddlemiddle_m {
	width:<?php echo ($s5_mainbody_width) - 15;?>px;}	
.s5_backmiddleleft_m {
	width:<?php echo ($s5_mainbody_width) - 0;?>px;}	
.s5_backmiddleright_m {
	width:<?php echo ($s5_mainbody_width) - 0;?>px;}	
#s5_outerwrap {
	background: <?php echo $s5_colorback; ?> url(<?php echo $s5_repeatback; ?>) repeat-x;}
#s5_footermiddle {
	width:<?php echo ($s5_body_width);?>px;}
</style>
<?php if ($is_ie6 == "yes") { ?>
<style type="text/css"> 
#s5_middlebody {
	width:<?php echo ($s5_body_width) - 25?>px;}
#s5_middleleft{
	width:<?php echo ($s5_body_width) - 10;?>px;}
#s5_middleright{
	width:<?php echo ($s5_body_width) - 15;?>px;}
#s5_logo  {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/<?php if ($s5_color== "orange") { ?>orange/<?php } ?><?php if ($s5_color== "blue") { ?>blue/<?php } ?>s5_cityportal_logo.png', sizingmethod='crop');}
.s5_topleftsignup_right {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/<?php if ($s5_color== "orange") { ?>orange/<?php } ?><?php if ($s5_color== "blue") { ?>blue/<?php } ?>s5_cityportal_ls_r.png', sizingmethod='crop');}
#s5_topleftsignup_left {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/<?php if ($s5_color== "orange") { ?>orange/<?php } ?><?php if ($s5_color== "blue") { ?>blue/<?php } ?>s5_cityportal_signup_l.png', sizingmethod='crop');}
#s5_topleftsignup_left2 {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/<?php if ($s5_color== "orange") { ?>orange/<?php } ?><?php if ($s5_color== "blue") { ?>blue/<?php } ?>s5_cityportal_login_l.png', sizingmethod='crop');}
#s5_box_tm {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/s5_cityportal_sbtm.png', sizingmethod='scale');}
#s5_box_bm {
	height:11px;
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/s5_cityportal_sbbm.png', sizingmethod='scale');}
#s5_box_sl {
	background:none;}
#s5_box_sm {
	background:none;}
#s5_bottommiddlebody {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images//s5_cityportal_bottom_middle.png', sizingmethod='scale');}
#s5_box_tl {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/s5_cityportal_sbtl.png', sizingmethod='crop');}
#s5_box_tr {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/s5_cityportal_sbtr.png', sizingmethod='crop');}
#s5_box_bl {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/s5_cityportal_sbbl.png', sizingmethod='crop');}
#s5_box_br {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/s5_cityportal_sbbr.png', sizingmethod='crop');}
#s5_topleftbody {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/s5_cityportal_top_left.png', sizingmethod='crop');}
#s5_toprightbody {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/s5_cityportal_top_right.png', sizingmethod='crop');}
#s5_topmiddlebody {
	margin-top:-2px;
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/s5_cityportal_top_middle.png', sizingmethod='scale');}
#s5_bottomleftbody {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/s5_cityportal_bottom_left.png', sizingmethod='crop');}
#s5_bottomrightbody {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/s5_cityportal_bottom_right.png', sizingmethod='crop');}
#s5_middleleft {
	border-left:1px solid #cccccc;
	margin-left:14px;
	margin-top:-1px;
	background:none;}
#s5_middleright {
	border-right:1px solid #cccccc;
	margin-left:0px;
	margin-top:-1px;
	background:none;
	}
#s5_navv ul li.s5_menubottom, #s5_fm_ul0 ul li.s5_menubottom, #s5_navv ul li.s5_menubottom:hover, #s5_fm_ul0 ul li.s5_menubottom:hover  {
	background:none;
	filter:
	progid:dximagetransform.microsoft.alphaimageloader(src='<?php echo $LiveSiteUrl;?>/templates/city_portal/images/s5_cityportal_menubottom.png', sizingmethod='crop');}
#s5_middleleft, #s5_middleright {
	padding:0px;
	margin:0px;
	}
#s5_middleleft {
	margin-top:-4px;
	margin-left:6px;}
#s5_middlebodypadding {
	padding:0px;}
#s5_middlebody {
	margin-left:-6px;}
</style> 
<?php } ?>
</head>
<body style="background: <?php echo $s5_colorback; ?>;">


<!-- Menu and Search -->
<div id="s5_headerwrap">
	<?php if ($is_ie6 == "no") { ?>
	<div class="s5_wrap">
		<div id="s5_date">
			<?php echo JHTML::_('date', 'now', JText::_('DATE_FORMAT_LC')) ?>
		</div>
	</div>
	<?php } ?>
	<div class="s5_wrap" style="position:relative;z-index:4;">
		<div id="s5_email"></div> 
			<div class="s5_padding" onclick="window.document.location.href='index.php?option=com_contact&amp;view=contact&amp;id=1&amp;Itemid=3'" >Email</div>
		<div id="s5_homepage"></div>
			<div class="s5_padding" onclick="javascript:bookmarksite('<?php echo $mainframe->getCfg('sitename');?>', '<?php echo $LiveSiteUrl;?>')" >Make My Homepage</div>
		<div id="s5_rss" ></div>
			<div class="s5_padding" onclick="window.document.location.href='<?php echo $LiveSiteUrl;?>/index.php?format=feed&amp;type=rss'">RSS</div>
		<?php if ($is_ie6 == "yes") { ?>
		<div id="s5_date">
			<?php echo date("l");?>, <?php echo date("F j, Y");?>
		</div>
		<?php } ?>
		<?php if($this->countModules('top')) { ?>
		<div id="s5_topmenu">
			<jdoc:include type="modules" name="top" style="xhtml" />	
		</div>
		<?php } ?>
	</div>
</div>
<!-- End Menu and Search -->

<!-- Body Wrap -->
<div id="s5_outerwrap">
	<div class="s5_wrap">
		<div id="s5_logo" style="cursor:pointer;" onclick="window.document.location.href='index.php'"></div>	
		<?php if($this->countModules('banner')) { ?>
		<div id="s5_searchposition">
			<jdoc:include type="modules" name="banner" style="xhtml" />	
		</div>
		<?php } ?>
		<div id="s5_topleftbuttons">
			<div id="s5_topleftsignup">
				<?php if ($s5_tab1 != "") { ?>
				<?php  $user =& JFactory::getUser();   
				  $user_id = $user->get('id');   
				  if ($user_id) { } else { ?>
					<div class="s5_topleftsignup_right"></div>
					<div class="s5_topleftsignup_middle" onclick="window.document.location.href='<?php echo $s5_registerlink;?>'">	
						<?php echo $s5_tab1;?>
					</div>
					<div id="s5_topleftsignup_left"></div>
				<?php } ?>	
				<?php } ?>
				<div style="clear:both;height:18px;"></div>
				<?php if($this->countModules('cpanel')) { ?>
					<div class="s5_topleftsignup_right"></div>
					<div class="s5_topleftsignup_middle" id="s5_login" onclick="shiftOpacity('s5_box_outer');">
						
					</div>
					<div id="s5_topleftsignup_left2"></div>
					<div style="clear:both;"></div>
				<?php } ?>			
				<?php if($this->countModules('cpanel')) { ?>	
				<div id="s5_box_outer" style="margin-left:-<?php echo ($s5_box_width) - 46;?>px;width:<?php echo $s5_box_width;?>px;">
					<div id="s5_box_tl"></div>
					<div id="s5_box_tm" style="width:<?php echo ($s5_box_width) - 100;?>px;"></div>
					<div id="s5_box_tr" onclick="shiftOpacity('s5_box_outer');"></div>
					<div style="clear:both;"></div>
						<div id="s5_box_sl">
							<div id="s5_box_sm" style="width:<?php echo ($s5_box_width) - 40;?>px;">
								<div id="s5_box_sm_inner" style="width:<?php echo ($s5_box_width) - 48;?>px;">
									<div style="padding:12px;">
										<jdoc:include type="modules" name="cpanel" style="xhtml" />
									</div>
								</div>
							</div>
						</div>			
					<div style="clear:both;"></div>	
					<div id="s5_box_bl"></div>
					<div id="s5_box_bm" style="width:<?php echo ($s5_box_width) - 68;?>px;"></div>
					<div id="s5_box_br"></div>
				</div>
				<?php } ?>				
			</div>
		</div>
		<div style="clear:both;height:27px;"></div>	
		<div id="s5_topleftbody"></div>
		<div id="s5_topmiddlebody"></div>
		<div id="s5_toprightbody"></div>	
<div style="clear:both;"></div>	
		<div id="s5_middleleft">
		<div id="s5_middleright">
		<div id="s5_middlebody">
		<div id="s5_middlebodypadding" <?php if ($is_ie6 == "yes") { ?>style="margin-left:16px;"<?php } ?>>
			
	<?php if (($s5_menu  == "1") || ($s5_menu  == "2") || ($s5_menu  == "3")) { ?>	
	<!-- Start Menu -->
		<div id="s5_menu">
		<div id="s5_navv">
					<?php mosShowListMenu($menu_name);	?>
					<?php if ($s5_menu  == "1") { ?>
						<script type="text/javascript" src="<?php echo $LiveSiteUrl;?>/templates/city_portal/js/s5_no_moo_menu.js"></script>																		
					<?php } ?>
					<?php if ($s5_menu  == "3") { ?>
						<script type="text/javascript" src="<?php echo $LiveSiteUrl;?>/templates/city_portal/js/s5_fading_no_moo_menu.js"></script>																		
					<?php } ?>	
		</div>	
		</div>
	<!-- End Menu -->
	<?php } ?>
			
			
	<div style="clear:both;width:94%;<?php if ($is_ie6 == "no") { ?>height:30px;<?php } else {?>height:0px;<?php } ?>"></div>		
	
	<!-- Start Main Body -->
				<?php if($this->countModules('left')) { ?>	
				<div id="s5_leftcolumn" style="width:<?php echo ($s5_left_width) + 1;?>px;">
						<div class="s5_backmiddlemiddle_l" id="s5_modleft" style="padding-right:14px;">	
								<jdoc:include type="modules" name="left" style="rounded" />
							<div style="clear:both;"></div>		
						</div>
				</div>
				<?php } ?>


					<div class="s5_backmiddleright_m" style="float:left;">
						<div class="s5_backmiddlemiddle_m" id="s5_modmiddle">	
								<?php if($this->countModules('user1') || $this->countModules('user2')) { ?>	
									<div id="s5_positions">
												<?php if($this->countModules('user1')) { ?>	
												<div id="s5_user1_<?php echo $user23; ?>">
													<jdoc:include type="modules" name="user1" style="rounded" />
												</div>
												<?php } ?>
												<?php if($this->countModules('user2')) { ?>	
												<div id="s5_user2_<?php echo $user23; ?>">
													<jdoc:include type="modules" name="user2" style="rounded" />
												</div>
											<?php } ?>
									</div>
									<div style="clear:both;"></div>	
								<?php } ?>	
												
									<jdoc:include type="message" />
									<jdoc:include type="component" />
						<div style="clear:both;"></div>
		
					<?php if($this->countModules('advert1') || $this->countModules('advert2') || $this->countModules('advert3')) { ?>	
					<!-- Start User 1-3 -->
						<div class="s5_backmiddlemiddle">
										<?php if($this->countModules('advert1')) { ?>	
										<div id="s5_advert1_<?php echo $advert; ?>">
											<jdoc:include type="modules" name="advert1" style="rounded" />	
										</div>
										<?php } ?>
										<?php if($this->countModules('advert2')) { ?>	
										<div id="s5_advert2_<?php echo $advert; ?>">	
											<jdoc:include type="modules" name="advert2" style="rounded" />
										</div>
										<?php } ?>
										<?php if($this->countModules('advert3')) { ?>	
										<div id="s5_advert3_<?php echo $advert; ?>">
											<jdoc:include type="modules" name="advert3" style="rounded" />
										</div>
										<?php } ?>		
								<div style="clear:both;"></div>		
						</div>
					<div style="clear:both;padding-bottom:12px;"></div>
					<!-- EndUser 1-3 -->
					<?php } ?>	
						
						
						
						</div>
					</div>
				<?php if($this->countModules('right')) { ?>	
				<div id="s5_rightcolumn" style="width:<?php echo ($s5_right_width) + 1;?>px;">
						<div class="s5_backmiddlemiddle_r" id="s5_modright" style="padding-right:11px;">	
							<jdoc:include type="modules" name="right" style="rounded" />
							<div style="clear:both;"></div>
						</div>
				</div>	
				<?php } ?>
		<div style="clear:both;padding-bottom:12px;"></div>
	<!-- End Main Body -->			
			
		<?php if($this->countModules('user3') || $this->countModules('user4') || $this->countModules('user5') || $this->countModules('user6') || $this->countModules('user7')) { ?>
			<!-- Start User 3-7 -->
			<div id="s5_user34567">
				<div class="module">
				<div class="mod">
				<div class="mod">
				<div class="mod">
				<div class="s5_backtopmiddle">
				<div class="s5_backtopleft"></div>
				<div class="s5_backtopright"></div>
				</div>	
				<div style="clear:both;"></div>
					<div class="s5_backmiddleleft">
					<div class="s5_backmiddleright">
					<div class="s5_backmiddlemiddle">
				
						<?php if($this->countModules('user3')) { ?>	
							<div id="s5_user3_<?php echo $bottom4; ?>">
								<jdoc:include type="modules" name="user3" style="rounded" />
							</div>
						<?php } ?>
						<?php if($this->countModules('user4')) { ?>	
							<div id="s5_user4_<?php echo $bottom4; ?>">
							<jdoc:include type="modules" name="user4" style="rounded" />
							</div>
						<?php } ?>
						<?php if($this->countModules('user5')) { ?>	
							<div id="s5_user5_<?php echo $bottom4; ?>">
							<jdoc:include type="modules" name="user5" style="rounded" />
							</div>
						<?php } ?>
						<?php if($this->countModules('user6')) { ?>	
							<div id="s5_user6_<?php echo $bottom4; ?>">
							<jdoc:include type="modules" name="user6" style="rounded" />
							</div>
						<?php } ?>
						<?php if($this->countModules('user7')) { ?>	
							<div id="s5_user7_<?php echo $bottom4; ?>">
						<jdoc:include type="modules" name="user7" style="rounded" />
							</div>
						<?php } ?>
					<div style="clear:both;"></div>
                                            				
		<?php if (($is_ie6 == "yes") || ($is_ie7 == "yes")) { ?>
			
			</div>
			<?php } else {?>
			<script type="text/javascript">//<![CDATA[
            document.write('</div>');
            //]]></script>
			<?php } ?>
					</div>
					</div>
					</div>
				<div style="clear:both;"></div>	
				<div class="s5_backbottommiddle">
				<div class="s5_backbottomleft"></div>
				<div class="s5_backbottomright"></div>
				</div>
				<div style="clear:both;padding-bottom:22px;"></div>
			</div>
			</div>
			</div>
			</div>
			<!-- EndUser 3-7 -->
			<?php } ?>
			<div style="clear:both;"></div>
			<?php if($this->countModules('breadcrumb')) { ?>
				<div id="s5_pathway">
					<jdoc:include type="modules" name="breadcrumb" style="xhtml" />	
				</div>
			<?php } ?>
			</div>
		</div>
		</div>
		</div>
<div style="clear:both;"></div>
		<div id="s5_bottomleftbody"></div>
		<div id="s5_bottommiddlebody"></div>
		<div id="s5_bottomrightbody"></div>
	</div>
</div>
<!-- End Body Wrap -->

<!-- Start Footer -->	
<div class="s5_wrap" style="margin:0 auto;padding-top:23px;">
	<div style="clear:both;"></div>
	<div id="s5_footerleft"></div>
	<div id="s5_footermiddle">
		<?php if($this->countModules('bottom')) { ?>	
			<div id="s5_footermenu">
				<jdoc:include type="modules" name="bottom" style="xhtml" />	
			</div>
		<?php } ?>
		<div id="s5_footercopyright">
		<?php include("templates/city_portal/footer.php"); ?>
		</div>
	</div>
	<div id="s5_footerlogo" style="cursor:pointer;" onclick="window.document.location.href='index.php'"></div>
	<div id="s5_footerright"></div>
	<div style="clear:both;height:40px;"></div>
</div>
<!-- End Footer -->
</div>
<?php if (($s5_clr_fix  == "enabled")) { ?>
<script type="text/javascript" src="<?php echo $LiveSiteUrl;?>/templates/city_portal/js/s5_clr_fix.js"></script>
<?php } ?>
<?php if ($s5_tooltips  == "yes") { ?>
<script type="text/javascript" src="<?php echo $LiveSiteUrl;?>/templates/city_portal/js/tooltips.js"></script>
<?php } ?>
<?php if($this->countModules('cpanel')) { ?>	
<script type="text/javascript" src="<?php echo $LiveSiteUrl;?>/templates/city_portal/js/s5_s5box.js"></script>
<?php } ?>
</body>
</html>