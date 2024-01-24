<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once('tcpdf/tcpdf.php');
//require_once dirname(__FILE__).'tcpdf/tcpdf.php';
class Pdf_Library extends TCPDF {

    public function __construct() {
        parent::__construct();
    }

     //Page header
     public function Header() {
        // Logo
        $image_file = $_SERVER['DOCUMENT_ROOT'].'assets/images/Awarathon-Logo.png';
        $this->Image($image_file, 165, 15, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->ln(5);
        $this->SetFont('helvetica', '', 12);
        // set color for text
        $this->SetTextColor(0, 63, 127);
        // Title
        //$this->Cell(0, 15, 'Sales Readiness Report', 0, true, 'C', 0, '', 0, false, 'M', 'M');
        $this->Cell(0, 15, 'Awarathon Sales Readiness Reports', 0, false, '', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        //$headerline='<hr style="width:100%", size="3", color="red">';
        //$this->writeHTMLCell(0, 0, '', '', $headerline, 0, 1, 0, true, 'C', true);

        $this->SetFont('helvetica', 'I', 8);
        // Title
        //$this->Cell(0, 15, 'Sales Readiness Report', 0, true, 'C', 0, '', 0, false, 'M', 'M');
        $this->Cell(0, 15, ' © Awarathon. All rights reserved                info@awarthon.com', 0, false, '', 0, '', 0, false, 'M', 'M');
        $this->ln(5);
        //$this->Cell(10, 15, 'info@awarthon.com', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        // Position at 15 mm from bottom
        //$this->cells
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->ln(5);
        $this->Cell(170, 25, $this->getAliasNumPage(), 0, false, 'R', 0, '', 0, false, 'M', 'M');
    }

}
