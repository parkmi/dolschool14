<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

require_once( JPATH_SITE . DS .'components'. DS .'com_joomla_lms'. DS .'includes'. DS .'pdf'. DS .'tcpdf'. DS .'tcpdf.php');


class JLMSPDF extends TCPDF
{
	/**
	 * @var Header html text.
	 * @access protected
	 */
	protected $header_html = '';
	
	/**
	 * @var Footer html text.
	 * @access protected
	 */
	protected $footer_html = '';



	/**
 	 * Set header data.
	 * @param string $html header html text	 
	 * @access public
	 */
	public function setHeaderHTML( $html ) {
		$this->header_html = $html;		
	}
	
	/**
 	 * Set footer data.
	 * @param string $html footer html text	 
	 * @access public
	 */
	public function setFooterHTML(  $html ) {
		$this->footer_html = $html;
	}
	
	/**
 	 * This method is used to render the page header.
 	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
	 * @access public
	 */
	 
	public function Header() {
		$ormargins = $this->getOriginalMargins();
		$headerfont = $this->getHeaderFont();
		$headerdata = $this->getHeaderData();
		
		$cell_height = round(($this->getCellHeightRatio() * $headerfont[2]) / $this->getScaleFactor(), 2);
		$cell_height = max( $this->header_margin, $cell_height );
				
		$this->SetX(0);		
		// header string
		$this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);	
		
		if (empty($this->pagegroups)) {
			$pagenumtxt = $this->l['w_page'].' '.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
		} else {
			$pagenumtxt = $this->l['w_page'].' '.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
		}
		
		$this->header_html = str_replace('{pagination}', $pagenumtxt, $this->header_html );
				
		$this->MultiCell(0, $cell_height, $this->header_html, 0, '', 0, 1, '', '', true, 0, true);
		// print an ending header line
		$this->SetLineStyle(array('width' => 0.85 / $this->getScaleFactor(), 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$this->SetY((2.835 / $this->getScaleFactor()) + $this->GetY());		
		$this->Cell(0, 0, '', 'T', 0, 'C');
	}
	

	/**
 	 * This method is used to render the page footer.
 	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
	 * @access public
	 */
	 
	public function Footer() 
	{		
		$cur_y = $this->GetY();
		$footerfont = $this->getFooterFont();
		$ormargins = $this->getOriginalMargins();
		$this->SetTextColor(0, 0, 0);		
		$cell_height = round(($this->getCellHeightRatio() * $footerfont[2]) / $this->getScaleFactor(), 2);
		
		$cell_height = max( $this->footer_margin, $cell_height );
		//set style for cell border
		$line_width = 0.85 / $this->getScaleFactor();
		$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		//print document barcode
		$barcode = $this->getBarcode();
		if (!empty($barcode)) {
			$this->Ln($line_width);
			$barcode_width = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right'])/3);
			$this->write1DBarcode($barcode, 'C128B', $this->GetX(), $cur_y + $line_width, $barcode_width, (($this->getFooterMargin() / 3) - $line_width), 0.3, '', '');
		}
						
		$this->SetY( $cur_y - $cell_height );
				
		$this->SetX($ormargins['left']);
		$this->MultiCell(0, $cell_height, $this->footer_html, 0, 'T', 0, 1, '', '', true, 0, true);
	}
	
	function writeHTML( $html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='' ) 
	{
		$html = htmlspecialchars_decode( $html, ENT_QUOTES );
		
		return parent::writeHTML( $html, $ln, $fill, $reseth, $cell, $align);
	}	
} 

?>