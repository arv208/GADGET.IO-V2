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
		$this->Cell(40,10,'Gadget.iO Transactions Report',0,0,'C');
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
            $xml->Load('../admin/transactions.xml');
            $transactions = $xml->getElementsByTagName('transactions')->item(0);
            $transaction = $transactions->getElementsByTagName('transaction');
            foreach ($transaction as $trans) {
                $data[] = $trans;
            }
            return $data;
		}
	
		//Colored table
		function FancyTable($header,$data)
		{
            $total = 0;
			//Colors, line width and bold font
			$this->SetFillColor(1,129,196);
			$this->SetTextColor(255);
			$this->SetDrawColor(132,138,150);
			$this->SetLineWidth(.3);
			$this->SetFont('','B');
			//Header
			$w=array(20,20,60,50,40);
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
                $transID = $row->getAttribute('transID');
                $custID = $row->getElementsByTagName('custID')->item(0)->firstChild->nodeValue;
                $custName = $row->getElementsByTagName('custName')->item(0)->firstChild->nodeValue;
                $custDate = $row->getElementsByTagName('custDate')->item(0)->firstChild->nodeValue;
                $custTotal = 'PHP '.number_format($row->getElementsByTagName('custTotal')->item(0)->firstChild->nodeValue,2);
                $total += $custTotal;
                
				$this->Cell($w[0],6,$transID,'LR',0,'L',$fill);
				$this->Cell($w[1],6,$custID,'LR',0,'L',$fill);
				$this->Cell($w[2],6,$custName,'LR',0,'L',$fill);
				$this->Cell($w[3],6,$custDate,'LR',0,'L',$fill);
                $this->Cell($w[4],6,$custTotal,'LR',0,'L',$fill);
				$this->Ln();
				$fill=!$fill;
			}
            $this->Cell(150,6,"TOTAL",'LR',0,'R',$fill);
            $this->Cell(40,6,'PHP '.number_format($_SESSION['totalT'],2),'LR',0,'L',$fill);
            $this->Ln();
            $fill=!$fill;
			$this->Cell(array_sum($w),0,'','T');
		}				
	}
	

$pdf=new PDF();
//Column titles
$header=array('TID','CID','CUSTOMER NAME','DATE','TOTAL');
//Data loading
$data=$pdf->LoadData();
$pdf->SetFont('Arial','',12);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
$pdf->Output();
?>