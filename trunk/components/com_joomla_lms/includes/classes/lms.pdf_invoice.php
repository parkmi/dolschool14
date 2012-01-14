<?php
defined( '_JLMS_EXEC' ) or die( 'Restricted access' );

class JLMS_PDF_invoice {

	var $pdf; //pdf-handler		

	function JLMS_PDF_invoice()	{	
		include( JPATH_SITE . DS .'components'. DS .'com_joomla_lms'. DS .'includes'. DS .'pdf'. DS .'lms.pdf.php' );

		/* font generator		
		include( JPATH_SITE . DS .'components'. DS .'com_joomla_lms'. DS .'includes'. DS .'pdf'. DS .'tcpdf'. DS .'utils'. DS .'makefont.php' );		
		MakeFont( JPATH_SITE . DS .'components'. DS .'com_joomla_lms'. DS .'includes'. DS .'pdf'. DS .'tcpdf'. DS .'utils'. DS .'arial.ttf', JPATH_SITE . DS .'components'. DS .'com_joomla_lms'. DS .'includes'. DS .'pdf'. DS .'tcpdf'. DS .'utils'. DS .'freesans-oblique.afm' );
		*/
		
		$this->pdf = new JLMSPDF( 'P', 'mm', 'A4', true, 'UTF-8', false );  //A4 Portrait
		$this->pdf->setPrintHeader( false );		
		$this->pdf->SetXY( 0, 0 );
		$this->pdf->SetMargins( 10, 20, 10, true );		
		$this->pdf->setFont( 'freesans' ); //choose font	
		$this->pdf->SetDrawColor( 0, 0, 0 );
	}

	function makeInvoicePDF($print_row, $filename) {
		global $JLMS_CONFIG;
				
		$JLMS_LANGUAGE1 = array();		
		JLMS_require_lang($JLMS_LANGUAGE1, 'pdf.lang', $JLMS_CONFIG->get('default_language'), 'frontend');	
									
		JLMS_processLanguage($JLMS_LANGUAGE1);
		$image_logo = JPATH_SITE.DS.'images'.DS.'joomlalms_invoice_logo.jpg';
		$logo_offset = 0;
		
		$this->pdf->AddPage();
		
		$top = 20;
				
		if (file_exists($image_logo)) {					
			$this->pdf->Image($image_logo,'','',56);		
		}
		
		$imgRBY = $this->pdf->getImageRBY();
				
		$dYl = ( $imgRBY )?$imgRBY+4:$top;
													
		$this->pdf->SetFontSize( 12 );		
		$this->pdf->Text( 140, $top, $print_row->site_name );
		
		$this->pdf->SetFontSize( 14 );				 
				 
		$this->pdf->text( 10, $dYl, _JLMS_INVOICE_HEADER );
		
		$dYl +=8;		
		$this->pdf->setFont( 'freesansi', 'I' ); //choose font
		$th_text = _JLMS_INVOICE_NUMBER_TEXT.($print_row->invoice_number);		
		$this->pdf->SetFontSize( 6 );							
		$this->pdf->text( 10, $dYl, $th_text );
		
		$dYl +=4;
		$th_text = _JLMS_INVOICE_DATE_TEXT.JLMS_dateToDisplay($print_row->date);		
		$this->pdf->SetFontSize( 6 );		
		$this->pdf->text( 10, $dYl, $th_text );
		
		$this->pdf->setFont( 'freesans' ); //choose font
		$sdvig = 0;
		$site_descr = explode(chr(13),$print_row->site_descr);
		
		$this->pdf->SetFontSize( 8 );
		
		$dYr = ( $top + 8);
		for($i=0;$i<count($site_descr);$i++)
		{							
			$this->pdf->text( 140, $dYr + $sdvig, trim(stripslashes($site_descr[$i])) );
			$sdvig+=4;
		}
		$comp_descr = explode(chr(13),$print_row->comp_descr);
	
		$dYr += ( $sdvig + 8 );
		
		$sdvig = 0;		
		for($i=0;$i<count($comp_descr);$i++)
		{			
			$this->pdf->text( 140, $dYr + $sdvig, trim(str_replace(chr(13), '', stripslashes($comp_descr[$i]))) );
			$sdvig+=4;
		}
				
		$this->pdf->SetFontSize( 12 );
		$dYl += ($sdvig + 10);				
		$this->pdf->text( 10, $dYl, _JLMS_INVOICE_CUSTOMER_INFO_HEADER );
		
		$this->pdf->setFont('freesansb' ); //choose font		
		$custom_invoice_fields = $JLMS_CONFIG->get('custom_invoice_fields', array());
		
		$this->pdf->SetFontSize( 8 );
		
		$dYl += 4;	
		$left = 25;
		
		if (!empty($custom_invoice_fields)) 
		{			
			$sdvig = 0;		
			foreach ($custom_invoice_fields as $cif) {
				$ftext = $cif->lang_var;
				if (defined($ftext)) {
					$ftext = constant($ftext);
				}
				
				$this->pdf->text( $left, ($dYl + $sdvig), $ftext );				
				$sdvig += 4;
			}
			
			$this->pdf->setFont('freesans'); //choose font
			
			$sdvig = 0;			
			foreach ($custom_invoice_fields as $cif) {
				$fname = $cif->var_name;
				$ftext = $print_row->{$fname};				
				$this->pdf->text( $left+30, ($dYl + $sdvig), stripslashes($ftext) );
				$sdvig += 4;
			}
			
			$dYl += $sdvig;			
		} else {						
			$dYl2 = $dYl; 
						
			$left = 25;			
			$this->pdf->text( $left , $dYl, _JLMS_INVOICE_CUSTOMER_NAME_TEXT );						
			$dYl += 4;						
			$this->pdf->text( $left, $dYl, _JLMS_INVOICE_CUSTOMER_COMPANY_TEXT );						
			$dYl += 4;					
			$this->pdf->text( $left, $dYl, _JLMS_INVOICE_CUSTOMER_ADDRESS_TEXT );						
			$dYl += 4;						
			$this->pdf->text( $left, $dYl, _JLMS_INVOICE_CUSTOMER_CITY_TEXT );						
			$dYl += 4;						
			$this->pdf->text( $left, $dYl, _JLMS_INVOICE_CUSTOMER_PHONE_TEXT );			
			
			$this->pdf->setFont('freesans'); //choose font						
			$this->pdf->text( $left + 30, $dYl2, stripslashes($print_row->name) );
			$dYl2 +=4;	
			$this->pdf->text( $left + 30, $dYl2, stripslashes($print_row->company) );
			$dYl2 +=4;
			$this->pdf->text( $left + 30, $dYl2, stripslashes($print_row->address) );
			$dYl2 +=4;
			$this->pdf->text( $left + 30, $dYl2, stripslashes($print_row->city) );
			$dYl2 +=4;
			$this->pdf->text( $left + 30, $dYl2, stripslashes($print_row->phone) );		
		}
										
		if( chop($print_row->comments) ) 
		{			
			$this->pdf->SetFontSize( 12 );
			$dYl += 10;		
			$this->pdf->text( 10, $dYl, _JLMS_INVOICE_COMMENTS_HEADER );
			
			$comments = explode(chr(13),$print_row->comments);
		
			$this->pdf->SetFontSize( 8 );
			$sdvig = 0;
			for($i=0;$i<count($comments);$i++)
			{		
				$this->pdf->text( 10, $dYl + 6 + $sdvig, trim(str_replace(chr(13), '', stripslashes($comments[$i]))));
				$sdvig+=4;
			}	
		}		
		
		$dYl += ( $sdvig + 6 );
		
	
		if(isset($print_row->is_sales))
		{			
			$this->pdf->SetFontSize( 10 );
			
			$this->pdf->SetY( $dYl );
			$this->pdf->Ln();				
			$this->pdf->Cell(30, 8, 'SALESPERSON', 1, 0, 'C' );
			$this->pdf->Cell(30, 8,'P.O. NUMBER', 1, 0, 'C' );
			$this->pdf->Cell(30, 8, 'REQUISITIONER', 1, 0, 'C' );
			$this->pdf->Cell(30, 8, 'SHIPPED VIA', 1, 0, 'C' );
			$this->pdf->Cell(40, 8, 'F.O.B. POINT', 1, 0, 'C' );
			$this->pdf->Cell(30, 8, 'TERMS', 1, 0, 'C' );
			$this->pdf->Ln();
			$this->pdf->Cell(30, 8, $print_row->sales_name, 1, 0, 'C' );								
			$this->pdf->Cell(30, 8,$print_row->po_number, 1, 0, 'C' );			
			$this->pdf->Cell(30, 8, $print_row->requistioner, 1, 0, 'C' );			
			$this->pdf->Cell(30, 8, $print_row->shipped_via, 1, 0, 'C' );			
			$this->pdf->Cell(40, 8, $print_row->fob_point, 1, 0, 'C' );			
			$this->pdf->Cell(30, 8, $print_row->terms, 1, 0, 'C' );
			$this->pdf->Ln();		
			
			$dYl +=20;
		}
		
		$was_add_details = false;
		$data = array();
		
		$w1=40; $w2=65; $w3=45; $w4=40;
		
		$this->pdf->SetY( $dYl );
		$this->pdf->Ln();				
		$this->pdf->Cell($w1, 8, _JLMS_INVOICE_UNITS_TABLE_QUANTITY_COLUMN, 1, 0, 'C' );		
		$this->pdf->Cell($w2, 8,_JLMS_INVOICE_UNITS_TABLE_DESC_COLUMN, 1, 0, 'C' );
		$this->pdf->Cell($w3, 8, _JLMS_INVOICE_UNITS_TABLE_UNITPRICE_COLUMN, 1, 0, 'C' );
		$this->pdf->Cell($w4, 8, _JLMS_INVOICE_UNITS_TABLE_TOTALPRICE_COLUMN, 1, 0, 'C' );		
		$this->pdf->Ln();
		
		if (!empty($print_row->payment_details) && is_array($print_row->payment_details)) {
			foreach ($print_row->payment_details as $pr_pd) {
				$this->pdf->Cell($w1, 8, $pr_pd->quantity, 1, 0, 'C' );		
				$this->pdf->Cell($w2, 8,stripslashes($pr_pd->name), 1, 0, 'C' );
				$this->pdf->Cell($w3, 8, sprintf("%01.2f", $pr_pd->unit_price).$JLMS_CONFIG->get('jlms_cur_code'), 1, 0, 'C' );
				$this->pdf->Cell($w4, 8, sprintf("%01.2f", ($pr_pd->quantity * $pr_pd->price)).$JLMS_CONFIG->get('jlms_cur_code'), 1, 0, 'C' );
				$this->pdf->Ln();			
			}
			$was_add_details = true;
		}
		if (!empty($print_row->payment_details2) && is_array($print_row->payment_details2)) {
			foreach ($print_row->payment_details2 as $pr_pd2) {
				$this->pdf->Cell($w1, 8, $pr_pd2->quantity, 1, 0, 'C' );		
				$this->pdf->Cell($w2, 8,stripslashes($pr_pd2->name), 1, 0, 'C' );
				$this->pdf->Cell($w3, 8, sprintf("%01.2f", $pr_pd2->unit_price).$JLMS_CONFIG->get('jlms_cur_code'), 1, 0, 'C' );
				$this->pdf->Cell($w4, 8, sprintf("%01.2f", ($pr_pd2->quantity * $pr_pd->price)).$JLMS_CONFIG->get('jlms_cur_code'), 1, 0, 'C' );
				$this->pdf->Ln();
			}
			$was_add_details = true;
		}

		if (!$was_add_details) {
			$this->pdf->Cell($w1, 8, $print_row->quantity, 1, 0, 'C' );		
			$this->pdf->Cell($w2, 8,stripslashes($print_row->description), 1, 0, 'C' );
			$this->pdf->Cell($w3, 8, (sprintf("%01.2f", $print_row->price).$JLMS_CONFIG->get('jlms_cur_code')), 1, 0, 'C' );
			$this->pdf->Cell($w4, 8, (sprintf("%01.2f", ($print_row->quantity * $print_row->price))).$JLMS_CONFIG->get('jlms_cur_code'), 1, 0, 'C' );			
			$this->pdf->Ln();
		}		
			
		$this->pdf->Cell($w1+$w2+$w3, 8, _JLMS_INVOICE_UNITS_TABLE_SUBTOTALPRICE_COLUMN, 0, 0, 'R' );
		$this->pdf->Cell($w4, 8, (sprintf("%01.2f",$print_row->quantity * $print_row->price).$JLMS_CONFIG->get('jlms_cur_code')), 1, 0, 'C' );
		$this->pdf->Ln();
		$this->pdf->Cell($w1+$w2+$w3, 8,_JLMS_INVOICE_UNITS_TABLE_TAXPRICE_COLUMN, 0, 0, 'R' );
		$this->pdf->Cell($w4, 8,$print_row->tax_amount, 1, 0, 'C' );
		$this->pdf->Ln();
		$this->pdf->Cell($w1+$w2+$w3, 8, _JLMS_INVOICE_UNITS_TABLE_SHIPPINGPRICE_COLUMN, 0, 0, 'R' );
		$this->pdf->Cell($w4, 8, $print_row->shipping, 1, 0, 'C' );
		$this->pdf->Ln();
		$this->pdf->Cell($w1+$w2+$w3, 8, _JLMS_INVOICE_UNITS_TABLE_TOTALDUEPRICE_COLUMN, 0, 0, 'R' );
		$this->pdf->Cell($w4, 8, (sprintf("%01.2f",$print_row->quantity * $print_row->price + $print_row->tax_amount).$JLMS_CONFIG->get('jlms_cur_code')), 1, 0, 'C' );
		$this->pdf->Ln();	
			
		
		$invoice_descr = explode(chr(13),$print_row->invoice_descr);
		$mysdvig = 8;
		$this->pdf->SetFontSize( 8 );
		$cur_y = $this->pdf->GetY();
		for($i=0;$i<count($invoice_descr);$i++)
		{						
			$this->pdf->text( 10, $cur_y+10+$mysdvig, trim(str_replace(chr(13), '', stripslashes($invoice_descr[$i]))));
			$mysdvig+=4;
		}		
		$th_text = stripslashes($print_row->thanks_text);
		
		$this->pdf->Ln();
		$this->pdf->SetY( 265 );		
		$this->pdf->Cell(0, 8, $th_text, 0, 0, 'C' );		
				
		$this->pdf->Output( $filename, 'F' );	
	}
}
?>