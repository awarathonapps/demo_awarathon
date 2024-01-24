<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';
class Pdf extends TCPDF
{
    function __construct()
    {
        parent::__construct();
    }
 
	public function setHtmlHeader($htmlHeader) {
		$this->htmlHeader = $htmlHeader;
    }

    public function Header() 
    { 
		if ($this->page == 1) {
		} else{
			$this->writeHTMLCell(
			$w = 0, $h = 0, $x = '', $y = '', $this->htmlHeader, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
			$this->SetTopMargin(25);
		}
        
    } 

    public function Footer() 
    { 
    
        $this->SetFont('helvetica', 'I', 8);
		
            $image_file='assets/images/pod.png';
            //$image_file = K_PATH_IMAGES.'eNable.png';
            $this->Image($image_file, 20, 285, 41, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
			
			 
			$this->ln(5);
			
			//$this->Cell(0, 105, 'info@awarthon.com', 0, false, '', 0, '', 0, false, 'M', 'M');
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->ln(5);
        // $this->Cell(170, 25, $this->getAliasNumPage(), 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Cell(0, 0, $this->getAliasNumPage(), 0, 0, 'R');

        
    }
}
?>