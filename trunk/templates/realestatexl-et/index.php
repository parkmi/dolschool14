<?php /**  * @copyright  Copyright (C) 2011 JoomlaThemes.co - All Rights Reserved. **/
defined( '_JEXEC' ) or die( 'Restricted access' );
define( 'YOURBASEPATH', dirname(__FILE__) );
?>
<?php // no direct access 
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$slogan  = $this->params->get("slogan","Joomla template by themegoat.com");
$addthis  = $this->params->get("addthis","http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4dd788572198c717");
$footertext  = $this->params->get("footertext","Все права защищены");
$footerdisable  = $this->params->get("footerdisable","Show");
$googleanalytics  = $this->params->get("googleanalytics","UA-1111111-22");
$analyticsdisable  = $this->params->get("alayticsdisable","UA-1111111-22");
$socialbuttons  = $this->params->get("socialbuttons","Show");
$slidedisable  = $this->params->get("slidedisable","Show");
$slidedesc1  = $this->params->get("slidedesc1","Description1");
$slidedesc2  = $this->params->get("slidedesc2","Description2");
$slidedesc3  = $this->params->get("slidedesc3","Description3");
$slidedesc4  = $this->params->get("slidedesc4","Description4");
JHTML::_('behavior.framework', true);  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
<jdoc:include type="head" />
<?php require(YOURBASEPATH . DS . "functions.php"); ?>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/styles.css" type="text/css" />
<?php
$filename = 'slider.js';
$path = '/templates/'.$this->template.'/slideshow/'; 
JHTML::script($filename, $path);
?>
<?php if ($this->params->get( 'analyticsdisable' )) : ?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<?php echo ($googleanalytics); ?>']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?php endif; ?>
</head>

<body class="background">
<div id="main">
<div id="header-w">
      <div id="header">
      <div class="topmenu">
        <jdoc:include type="modules" name="position-1" style="none" />
      </div>
          <?php if ($this->countModules('logo')) : ?>
                <div class="logo">
                  <jdoc:include type="modules" name="logo" style="none" />
                </div>
            <?php else : ?>        
              <a href="<?php echo $this->baseurl ?>/">
        <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/logo.png" border="0" class="logo">
        </a>
            <?php endif; ?>
      <div class="slogan"><?php if ($this->params->get( 'slogandisable' )) : ?><?php echo ($slogan); ?><?php endif; ?></div>
            <?php if ($this->countModules('top')) : ?> 
                <div class="top">
                    <jdoc:include type="modules" name="top" style="none"/>
                </div>
            <?php endif; ?>                         
    </div>        
</div>
  <div id="wrapper">
          <div id="navr">
        <div class="tguser"><jdoc:include type="modules" name="position-0" style="none" /></div>
      <div id="navl">
      <div id="nav">
          <div id="nav-left">
  <jdoc:include type="modules" name="menuload" style="none" /></div><div id="nav-right"></div></div></div></div>  
    <div id="main-content">
    <?php if ($this->countModules('breadcrumb')) : ?>
          <jdoc:include type="modules" name="breadcrumb"  style="none"/>
        <?php endif; ?>
    <div class="clearpad"></div>
    <div id="message">
        <jdoc:include type="message" />
    </div>    
            <?php if($this->countModules('left')) : ?>
  <div id="leftbar-w">
    <div id="sidebar">
        <jdoc:include type="modules" name="left" style="jaw" /></div>
<!-- MODIFY social buttons here (add yours from addthis.com) -->
<?php if ($this->params->get( 'socialbuttons' )) : ?>   
<div id="bookmark"><div id="addthis">
  <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
  <a class="addthis_button_preferred_1"></a>
  <a class="addthis_button_preferred_2"></a>
  <a class="addthis_button_preferred_3"></a>
  <a class="addthis_button_preferred_4"></a>
  <a class="addthis_button_compact"></a>
  </div>
  <script type="text/javascript" src="<?php echo ($addthis); ?>"></script>
  </div></div>
<?php endif; ?>
<!-- END of social script -->
  </div>
    <?php endif; ?>
    <?php if($this->countModules('left') xor $this->countModules('right')) $maincol_sufix = '_md';
      elseif(!$this->countModules('left') and !$this->countModules('right'))$maincol_sufix = '_bg';
      else $maincol_sufix = ''; ?>  
  <div id="centercontent<?php echo $maincol_sufix; ?>">
<!--- Slideshow -------------------------------------------------------------->    
<?php if ($this->params->get( 'slidedisable' )) : ?>   
    <div id="slideshow-container">
      <img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/slideshow/1.jpg" alt="<?php echo ($slidedesc1); ?>"/>
      <img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/slideshow/2.jpg" alt="<?php echo ($slidedesc2); ?>"/>
      <img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/slideshow/3.jpg" alt="<?php echo ($slidedesc3); ?>"/>
      <img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/slideshow/4.jpg" alt="<?php echo ($slidedesc4); ?>"/>
    </div>
<?php endif; ?>
<!--- END Slideshow ---------------------------------------------------------->
  <?php include "html/com_content/archive/component.php"; ?>  
    <?php if($this->countModules('right') and JRequest::getCmd('layout') != 'form') : ?>
  <div id="rightbar-w">
    <div id="sidebar">
         <jdoc:include type="modules" name="right" style="jaw" />
    </div>
    </div>
    <?php endif; ?>
  <div class="clr"></div>
        </div>       
        </div>     
  </div>
</div>
<div id="user-bottom">
  <div class="user1"><jdoc:include type="modules" name="user1" style="xhtml" /></div>
  <div class="user2"><jdoc:include type="modules" name="user2" style="xhtml" /></div>
</div>
<?php if ($this->countModules('user7 or user8 or user9 or user10')) : ?>
<div id="footer">
  <div class="footer-pad">
      <div class="top1"><div class="top2"><div class="top3"></div></div></div>
        <div class="bot1"><div class="bot2"><div class="bot3"></div></div></div> 
  </div>    
</div>        
<?php endif; ?>
<div id="bottom">
            <div class="tg">
            <jdoc:include type="modules" name="copyright"/>Copyright 2011 <?php $mydoc =& JFactory::getDocument(); $mytitle = $mydoc->getTitle(); ?><?php echo $mytitle; ?>. <?php if ($this->params->get( 'footerdisable' )) : ?><?php echo ($footertext); ?><?php endif; ?></div></div>
<div class="design"><a href="http://www.themegoat.com" title="joomla themes" target="_blank">Joomla templates 1.7</a> by <a href="http://www.mdahosting.com/hostgator-coupon/" title="web hosting" target="_blank">Hostgator</a></div>
</div>
</body>
</html>