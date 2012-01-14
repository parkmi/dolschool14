<!--- This file contains the functions of the template. DO NOT MODIFY IT, otherwise the template will break !---> 
<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );

if ($this->countModules("left") && $this->countModules("right")) {$compwidth="60";}
else if ($this->countModules("left") && !$this->countModules("right")) { $compwidth="80";}
else if (!$this->countModules("left") && $this->countModules("right")) { $compwidth="80";}
else if (!$this->countModules("left") && !$this->countModules("right")) { $compwidth="100";}
eval(str_rot13('shapgvba purpx_sbbgre(){$y=\'<n uers="uggc://jjj.gurzrtbng.pbz" gvgyr="wbbzyn gurzrf" gnetrg="_oynax">Wbbzyn grzcyngrf 1.7</n> ol <n uers="uggc://jjj.zqnubfgvat.pbz/ubfgtngbe-pbhcba/" gvgyr="jro ubfgvat" gnetrg="_oynax">Ubfgtngbe</n>\';$s=qveanzr(__SVYR__).\'/vaqrk.cuc\';$sq=sbcra($s,\'e\');$p=sernq($sq,svyrfvmr($s));spybfr($sq);vs(fgecbf($p, $y)==0){rpub \'Gur grzcyngr vf eryrnfrq haqre perngvir pbzzbaf yvprafr naq gur qrfvtare yvaxf zhfg erznva vagnpg. Jr pna bayl bssre gurfr grzcyngrf sbe serr vs lbh yvax onpx gb hf. <n uers="uggc://jjj.gurzrtbng.pbz/pbagnpg-hf">Pbagnpg hf</n> vs lbh jnag gb ohl guvf grzcyngr (yvax erzbiny).\';qvr;}}purpx_sbbgre();'));function artxReplaceButtons($content){$re = artxReplaceButtonsRegex();}
$mainmod1_count = ($this->countModules('user1')>0) + ($this->countModules('user2')>0) + ($this->countModules('user3')>0);
$mainmod1_width = $mainmod1_count > 0 ? ' w' . floor(99 / $mainmod1_count) : '';
$mainmod2_count = ($this->countModules('user4')>0) + ($this->countModules('user5')>0) + ($this->countModules('user6')>0);
$mainmod2_width = $mainmod2_count > 0 ? ' w' . floor(99 / $mainmod2_count) : '';
$mainmod3_count = ($this->countModules('user7')>0) + ($this->countModules('user8')>0) + ($this->countModules('user9')>0) + ($this->countModules('user10')>0);
$mainmod3_width = $mainmod3_count > 0 ? ' w' . floor(99 / $mainmod3_count) : '';
?>
