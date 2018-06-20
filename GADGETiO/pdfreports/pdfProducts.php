<?php
session_start();
require('fpdf.php');

	class PDF extends FPDF
	{
		function Header()
{
		//Logo
		$this->Image('GADGETiO-logo.jpg',25,5,40,20);
		//Arial bold 15
		$this->SetFont('Arial','B',15);
		//Move to the right
		$this->Cell(80);
		//Title
		$this->Cell(40,10,'Gadget.iO Products',0,0,'C');
		//Line break
		$this->Ln(20);
	}

	//Page footer
	function Footer()
	{
		//Position at 1.5 cm from bottom
		$this->SetY(-15);
		//Arial italic 8
		$this->SetFont('Arial','I',8);
		//Page number
		$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
	}
	
	
		function LoadData()
        {			
            $xml = new DOMDocument();
            $xml->Load('../products.xml');
            $products = $xml->getElementsByTagName('products')->item(0);
            $product = $products->getElementsByTagName('product');
            foreach ($product as $item) {
                $data[] = $item;
            }
            return $data;
		}
	
		//Colored table
		function FancyTable($header,$data)
		{
			//Colors, line width and bold font
			$this->SetFillColor(1,129,196);
			$this->SetTextColor(255);
			$this->SetDrawColor(132,138,150);
			$this->SetLineWidth(.3);
			$this->SetFont('','B');
			//Header
			$w=array(20,80,45,45);
			for($i=0;$i<count($header);$i++)
				$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
			$this->Ln();
			//Color and font restoration
			$this->SetFillColor(226,234,241);
			$this->SetTextColor(104,103,103);
			$this->SetFont('');
			//Data
			$fill=false;
                        
			foreach($data as $row)
			{
                $prodID = $row->getAttribute('prodID');
                $prodName = $row->getElementsByTagName('prodName')->item(0)->firstChild->nodeValue;
                $prodQty = $row->getElementsByTagName('prodQty')->item(0)->firstChild->nodeValue;
                $prodPrice = 'PHP '.number_format($row->getElementsByTagName('prodPrice')->item(0)->firstChild->nodeValue,2);
                
				$this->Cell($w[0],6,$prodID,'LR',0,'L',$fill);
				$this->Cell($w[1],6,$prodName,'LR',0,'L',$fill);
				$this->Cell($w[2],6,$prodQty,'LR',0,'L',$fill);
				$this->Cell($w[3],6,$prodPrice,'LR',0,'L',$fill);
				$this->Ln();
				$fill=!$fill;
			}
			$this->Cell(array_sum($w),0,'','T');
		}				
	}
	

$pdf=new PDF();
//Column titles
$header=array('ID','PRODUCT NAME','QUANTITY','PRICE');
//Data loading
$data=$pdf->LoadData();
$pdf->SetFont('Arial','',12);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
$pdf->Output();
?>