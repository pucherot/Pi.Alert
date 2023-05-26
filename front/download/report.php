<?php
require '../lib/fpdf/fpdf.php';
$regex = '/[0-9]+-[0-9]+_.*\\.txt/i';
$filename = str_replace(array('\'', '"', ',', ';', '<', '>', '.', '/', '&'), "", $_REQUEST['report']) . '.txt';
if (preg_match($regex, $filename) == True) {
	$headtitle = explode("-", $filename);
	$headeventtype = explode("_", $filename);
//echo getcwd();
	$headline = substr($headtitle[0], 6, 2) . '.' . substr($headtitle[0], 4, 2) . '.' . substr($headtitle[0], 2, 2) . '/' . substr($headtitle[1], 0, 2) . ':' . substr($headtitle[1], 2, 2) . ' - ' . substr($headeventtype[1], 0, -4);
	$downloadname = substr($headtitle[0], 6, 2) . substr($headtitle[0], 4, 2) . substr($headtitle[0], 2, 2) . '_' . substr($headtitle[1], 0, 2) . substr($headtitle[1], 2, 2) . '_' . substr($headeventtype[1], 0, -4);

	class PDF extends FPDF {
		function headline() {
			global $headline;
			// Sets font to Arial bold 15
			$this->SetFont('Arial', 'U', 16);
			// Calculate string length
			$w = $this->GetStringWidth('Pi.Alert Report') + 6;
			$this->SetX((210 - $w) / 2);
			// It defines the grey color for filling
			$this->SetFillColor(255, 255, 255);
			// Sets the text color
			$this->SetTextColor(255, 0, 0);
			// Set the line width to 1 mm)
			$this->SetLineWidth(0);
			// Prints a cell Title
			$this->Cell($w, 9, 'Pi.Alert Report', 0, 1, 'C', 1);
			// Line break
			$this->Ln(10);
		}
		function reportTitle($label) {
			// Sets font to Arial 12
			$this->SetFont('Arial', '', 12);
			$this->SetTextColor(0, 0, 0);
			$this->SetFillColor(211, 211, 211);
			$this->Cell(0, 6, "$label", 0, 1, 'L', 1);
			// Line break
			$this->Ln(4);
		}
		function reportContent($file) {
			// Read text file
			$f = fopen('../reports/' . $file, 'r');
			$txt = fread($f, filesize('../reports/' . $file));
			fclose($f);
			$this->SetFont('Courier', '', 10);
			$this->SetTextColor(0, 0, 0);
			// It prints texts with line breaks
			$this->MultiCell(0, 5, $txt);
		}
		function showReport($headline, $file) {
			// Add a new page
			$this->AddPage();
			$this->headline();
			$this->reportTitle($headline);
			$this->reportContent($file);
		}
	}

// Initiate a PDF object
	$pdf = new PDF();

// Sets the document title
	$pdf->SetTitle($headline);
// Sets the document author name
	$pdf->SetAuthor('Pi.Alert');

	$pdf->showReport(
		$headline,
		$filename
	);

	header('Content-type: application/download');
	header('Content-Disposition: attachment; filename="' . $downloadname . '.pdf"');
	header('Cache-Control: private, max-age=0, must-revalidate');
	header('Pragma: public');
//	$pdf->Output('I', $downloadname . '.pdf');
	echo $pdf->Output('S');
}

?>