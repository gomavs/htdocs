<?php
error_reporting(E_ALL);
set_include_path($_SERVER["DOCUMENT_ROOT"] . "/includes/");
date_default_timezone_set("America/Chicago");
session_start();
if (!isset($_SESSION['id'])) {
	if (!isset($nologin)) {
		header("Location:/login.php");
	}
} else {
	if (isset($nologin)) {
		header("Location:/");
	}
	$logged_in = 1;
}
require("fpdf.php");
class PDF extends FPDF {
	function __construct($orientation = "P", $unit = "mm", $size = "A4") {
		$this->FPDF($orientation, $unit, $size);
		$mysqli = new mysqli('localhost', 'root', '', 'pin', '3306') or die("Failed to connect to the database. Error log: <br/>" . mysqli_error());
		$sql = $mysqli->prepare("SELECT * FROM workorders WHERE id = ?");
		$sql->bind_param("i", $_GET["id"]);
		$sql->bind_result($this->id, $this->rid, $this->oid, $this->requester, $this->assigned, $this->time, $this->due, $this->worktype, $this->item, $this->issue, $this->urgency, $this->notes, $this->status, $this->estimate);
		$sql->execute();
		$sql->fetch();
	}

	function Header() {
		$this->setFont("Arial", "B", 12);
		$this->Cell(130);
		$this->Cell(30, 6, "Request ID:", 0, 0, "R");
		$this->Cell(30, 6, "#" . str_pad($this->rid, 10, "0", STR_PAD_LEFT), 0, 1, "R");
		if ($this->status != 0) {
			$this->Cell(130);
			$this->Cell(30, 6, "Order ID:", 0, 0, "R");
			$this->Cell(30, 6, "#" . str_pad($this->oid, 10, "0", STR_PAD_LEFT), 0, 1, "R");
		}
		$this->Cell(130);
		$this->Cell(30, 6, "Requested On:", 0, 0, "R");
		$this->Cell(30, 6, date("n/j/y", strtotime($this->time)), 0, 1, "R");
	}

	function Footer() {

	}
}
$pdf = new PDF("P", "mm", "A4");
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->setFont("Arial", "B", 12);
$pdf->Output();
?>