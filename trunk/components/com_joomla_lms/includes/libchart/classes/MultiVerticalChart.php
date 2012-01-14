<?php

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );


class MultiVerticalChart extends VerticalChart {
	
	function MultiVerticalChart($width = 600, $height = 250, $sections)
	{
    	parent::VerticalChart($width, $height);
		
		$this->sections = $sections;
		$this->setLabelMarginLeft(intval($height/5));
		$this->setLabelMarginRight(intval($height/8));
		$this->setLabelMarginTop(intval($height/5));
		$this->setLabelMarginBottom(intval($height/5));
		
		$this->setLabelMarginLeft(50);
        $this->setLabelMarginRight(20);
        $this->setLabelMarginTop(20);
        $this->setLabelMarginBottom(20);
	}
	
	/**
	* Compute the image layout
	*
	* @access       protected
	*/
                
	function computeLabelMargin($n)
	{
	//$this->axis = new Axis($this->yMinValue, $this->yMaxValue);
	$this->axis = new Axis(0, 90);
	$this->axis->computeBoundaries();
	$this->graphTLX = $this->margin + $this->labelMarginLeft;
	$this->graphTLY = intval(($n-1)*$this->height/$this->sections) + $this->margin + $this->labelMarginTop;
	$this->graphBRX = $this->width - $this->margin - $this->labelMarginRight;
	$this->graphBRY = intval($n*$this->height/$this->sections) - $this->margin - $this->labelMarginBottom;
	}	
	
	
	/**
	* Create the image on old image
	*
	* @access       protected
	*/
    
	        
	function createImage2()
	{
		$aquaColor = Array($this->aquaColor1, $this->aquaColor2, $this->aquaColor3, $this->aquaColor4);

		for($i = $this->graphTLY; $i < $this->graphBRY; $i++)
		{
			$color = $aquaColor[($i + 3) % 4];
			$this->primitive->line($this->graphTLX, $i, $this->graphBRX, $i, $color);
		}

		// Axis

		imagerectangle($this->img, $this->graphTLX - 1, $this->graphTLY, $this->graphTLX, $this->graphBRY, $this->axisColor1->getColor($this->img));
		imagerectangle($this->img, $this->graphTLX - 1, $this->graphBRY, $this->graphBRX, $this->graphBRY + 1, $this->axisColor1->getColor($this->img));
	}
	
	/**
	* Print the title to the image
	*
	* @access       private
	*/
                
	function printTitle($n)
	{
			if ($n == 1)
				$this->text->printCentered($this->img, 4 + ($this->labelMarginTop + $this->margin) / 2, $this->textColor, $this->title, $this->text->fontCondensedBold);
			else
				$this->text->printCentered($this->img, intval(($n-1)*$this->height/$this->sections) - 5 + ($this->labelMarginTop + $this->margin) / 2, $this->textColor, $this->title, $this->text->fontCondensedBold);
	}
	
	/**
	* Add a new sampling point to the chart
	*
	* @access       public
	* @param        Point           sampling point to add
	*/
                
	function addPoint($i, $point, $number)
	{
		if ( !isset($this->points[$i]) )
			$this->points[$i] = array();
		if ( !isset($this->numbers[$i]) )
			$this->numbers[$i] = array();
		if (function_exists('html_entity_decode')) {
   			$point->x = @html_entity_decode($point->x, ENT_QUOTES, 'UTF-8'); 
		}
		else { 
    		$trans_tbl = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
        	$trans_tbl = array_flip($trans_tbl);
        	$point->x = strtr($point->x, $trans_tbl);
    	}
		$point->x = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $point->x); 
    	$point->x = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $point->x);
		array_push($this->points[$i], $point);
		array_push($this->numbers[$i], $number);
	}
	
	function renderSection($n) {
		
		$this->computeBound();
		$this->CalcBottomMargin();
		$this->computeLabelMargin($n);
		if ( $n == 1 ) {
			$this->createImage();
			
			$this->text->printText($this->img, 0, 0, $this->textColor, $this->maintitle, $this->text->fontCondensedBold);
			
		}
		else 
			$this->createImage2();		
			
		$this->printLogo();
		$this->printTitle($n);
		$this->printAxis();
		$this->printBar();	
	}
	
	/**
	* Render the chart image
	*
	* @access       public
	* @param        string          name of the file to render the image to (optional)
	*/
	
	function CalcBottomMargin() 
	{
		$graphTLX = $this->margin + $this->labelMarginLeft;
		$graphBRX = $this->width - $this->margin - $this->labelMarginRight;
		$columnWidth = ($graphBRX - $graphTLX) / $this->sampleCount;
		
		foreach($this->point as $point) {
			if (ceil(strlen($point->getX())*6) > $columnWidth)
				$this->labelMarginBottom = 20 * ceil(strlen($point->getX())*6/$columnWidth);
		}		
	}
    	            
	function render($fileName = null)
	{	
			
		for ($i = 1; $i <= $this->sections; $i++) {
			$this->point = $this->points[$i];
			$this->number = $this->numbers[$i];			
			$this->usr_answer = $this->usr_answers[$i];
			$this->setTitle($this->titles[$i]);
			$this->renderSection($i);
			
		}

		if(isset($fileName))
			imagepng($this->img, $fileName);
		else
			imagepng($this->img);
	}
	function printAxis()
                {
                        // Check if some points were defined
                        
                        if(!$this->sampleCount)
                                return;
                        
                        $minValue = 0;
						//$this->axis->getLowerBoundary();
                        $maxValue = 100;
						//$this->axis->getUpperBoundary();
                        $stepValue = 25;
						//$this->axis->getTics();

                        // Vertical axis

                        for($value = $minValue; $value <= $maxValue; $value += $stepValue)
                        {
                                $y = $this->graphBRY - ($value - $minValue) * ($this->graphBRY - $this->graphTLY) / ($this->axis->displayDelta);

                                imagerectangle($this->img, $this->graphTLX - 3, $y, $this->graphTLX - 2, $y + 1, $this->axisColor1->getColor($this->img));
                                imagerectangle($this->img, $this->graphTLX - 1, $y, $this->graphTLX, $y + 1, $this->axisColor2->getColor($this->img));
								$value = ($value + $stepValue <= $maxValue ? $value."%": $value."% ");
                                $this->text->printText($this->img, $this->graphTLX - 5, $y, $this->textColor, $value, $this->text->fontCondensed, $this->text->HORIZONTAL_RIGHT_ALIGN | $this->text->VERTICAL_CENTER_ALIGN);
                        }

                        // Horizontal Axis

                        $columnWidth = ($this->graphBRX - $this->graphTLX) / $this->sampleCount;

                        reset($this->point);
						
                        for($i = 0; $i <= $this->sampleCount; $i++)
                        {
                                $x = $this->graphTLX + $i * $columnWidth;

                                imagerectangle($this->img, $x - 1, $this->graphBRY + 2, $x, $this->graphBRY + 3, $this->axisColor1->getColor($this->img));
                                imagerectangle($this->img, $x - 1, $this->graphBRY, $x, $this->graphBRY + 1, $this->axisColor2->getColor($this->img));

                                if($i < $this->sampleCount)
                                {
                                        $point = current($this->point);
                                        next($this->point);
        								
                                        $text = $point->getX();
										$fontFileName = $this->text->fontCondensed;
																				
										
										
                                        $this->text->printDiagonal_per($this->img, $x, $this->graphBRY + 10, $this->textColor, $text, $fontFileName, $columnWidth);
										
                                }
                        }
                }

                function printBar()
                {
                    // Check if some points were defined
                    
                    if(!$this->sampleCount)
                            return;
                    
                    reset($this->point);
					reset($this->number);
					$minValue = 0;
					//$this->axis->getLowerBoundary();
                    $maxValue = 100;
					//$this->axis->getUpperBoundary();
                    $stepValue = 25;
					//$this->axis->getTics();
                    /*$minValue = $this->axis->getLowerBoundary();
                    $maxValue = $this->axis->getUpperBoundary();
                    $stepValue = $this->axis->getTics();*/

                    $columnWidth = ($this->graphBRX - $this->graphTLX) / $this->sampleCount;

                    for($i = 0; $i < $this->sampleCount; $i++)
                    {
                        $x = $this->graphTLX + $i * ($this->graphBRX - $this->graphTLX) / $this->sampleCount;

                        $point = current($this->point);
                        next($this->point);

                        $value = $point->getY();
                        
                        $ymin = $this->graphBRY - ($value - $minValue) * ($this->graphBRY - $this->graphTLY) / ($this->axis->displayDelta);
						
						
						$fontFileName = $this->text->fontCondensed;
						
						
                        $this->text->printText($this->img, $x + $columnWidth / 2, $ymin - 5, $this->textColor, $value."% (".intval(current($this->number)).")", $fontFileName, $this->text->HORIZONTAL_CENTER_ALIGN | $this->text->VERTICAL_BOTTOM_ALIGN);
						next($this->number);
                        // Vertical bar

                        $x1 = $x + $columnWidth * 1 / 5;
                        $x2 = $x + $columnWidth * 4 / 5;

                        imagefilledrectangle($this->img, $x1, $ymin, $x2, $this->graphBRY - 1, $this->barColor2->getColor($this->img));
                        imagefilledrectangle($this->img, $x1 + 1, $ymin + 1, $x2 - 4, $this->graphBRY - 1, $this->barColor1->getColor($this->img));
                    }
            }

	
}

?>