<?php

    function flashfix_init( $flashfix_js_base_url ) {
	?>
		<script src="<?php echo $flashfix_js_base_url; ?>/AC_RunActiveContent.js" type="text/javascript"></script>
		<script src="<?php echo $flashfix_js_base_url; ?>/AC_ActiveX.js" type="text/javascript"></script>
	<?php
    }

    function flashfix_html( $movie, $width, $height, $arr_params = null ) {
        $codebase = "http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0";

        $basename =  $movie ;
		//$basename = basename( $movie );
        //if ( $pos = strripos( $basename, '.' ) )
           // $basename = substr( $basename, 0, $pos );

        if ( $arr_params == null )
            $arr_params = array();

        if ( ! isset( $arr_params["quality"] ) )
            $arr_params["quality"] = "high";

?>
<script type="text/javascript">

AC_FL_RunContent( 'codebase', '<?php echo $codebase;?>',
    'width', '<?php echo $width;?>',
    'height', '<?php echo $height;?>',
	<?php
		while ( list( $key, $val ) = each( $arr_params ) ) {?>
			'<?php echo $key; ?>', '<?php echo $val;?>',<?php
    	}

?>
    'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
    'src', '<?php echo $basename; ?>',
    'movie', '<?php echo $basename; ?>'
); //end AC code
</script>
<?php /*
<noscript>
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="<?php echo $codebase; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>">
        <param name="movie" value="<?php echo $movie; ?>" />
		<?php
        while ( list( $key, $val ) = each( $arr_params ) ) {
		?>
        <param name="<?php echo $key;?>" value="<?php echo $val;?>" />
		<?php
        }
		?>
        <embed src="<?php echo $movie;?>" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="<?php echo $width; ?>" height="<?php echo $height; ?>"></embed>
    </object>
</noscript>
*/?>
<?php

    }

?>