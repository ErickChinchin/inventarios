<?php
include "../core/autoload.php";
include "../core/app/autoload.php";
Core::$root="../";

require('../fpdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'JAMAAL Inventario',0,1,'C');
        $this->SetFont('Arial','',9);
        $this->Cell(0,6,'Generado: '.date('d/m/Y H:i'),0,1,'C');
        $this->Ln(3);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$branch_id = (isset($_GET['branch_id']) && $_GET['branch_id'] !== '') ? intval($_GET['branch_id']) : null;

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetMargins(10,15,10);

$products = ProductData::getAll();

if($branch_id) {
    // --- REPORTE POR SUCURSAL ESPECÍFICA ---
    $branch = BranchData::getById($branch_id);
    $branch_name = $branch ? $branch->name : 'Sucursal #'.$branch_id;

    $pdf->AddPage();
    $pdf->SetFont('Arial','B',13);
    $pdf->Cell(0,8,'INVENTARIO DE PRODUCTOS',0,1,'C');
    $pdf->SetFont('Arial','I',10);
    $pdf->Cell(0,6,'Sucursal: '.utf8_decode($branch_name),0,1,'C');
    $pdf->Ln(4);

    // Encabezado tabla
    $pdf->SetFont('Arial','B',9);
    $pdf->SetFillColor(220,220,220);
    $pdf->Cell(12, 8,'ID',   1,0,'C',1);
    $pdf->Cell(35, 8,'Codigo',1,0,'C',1);
    $pdf->Cell(90, 8,'Nombre',1,0,'C',1);
    $pdf->Cell(45, 8,'Sucursal',1,0,'C',1);
    $pdf->Cell(12, 8,'Cant.',1,1,'C',1);

    $pdf->SetFont('Arial','',9);
    foreach($products as $product){
        $q = OperationData::getQYesF($product->id, $branch_id);
        if($q <= 0) $pdf->SetTextColor(180,0,0);
        else $pdf->SetTextColor(0,0,0);

        $pdf->Cell(12, 7, $product->id,          1,0,'C');
        $pdf->Cell(35, 7, $product->barcode,      1,0,'C');
        $pdf->Cell(90, 7, utf8_decode($product->name), 1,0,'L');
        $pdf->Cell(45, 7, utf8_decode($branch_name),   1,0,'C');
        $pdf->Cell(12, 7, $q,                    1,1,'C');
    }

} else {
    // --- REPORTE GLOBAL: una sola tabla con todas las sucursales ---
    $branches = BranchData::getAllActive();

    $pdf->AddPage();
    $pdf->SetFont('Arial','B',13);
    $pdf->Cell(0,8,'INVENTARIO DE PRODUCTOS',0,1,'C');
    $pdf->SetFont('Arial','I',10);
    $pdf->Cell(0,6,'Todas las sucursales',0,1,'C');
    $pdf->Ln(4);

    // Encabezado tabla
    $pdf->SetFont('Arial','B',9);
    $pdf->SetFillColor(220,220,220);
    $pdf->Cell(12, 8,'ID',      1,0,'C',1);
    $pdf->Cell(32, 8,'Codigo',  1,0,'C',1);
    $pdf->Cell(70, 8,'Nombre',  1,0,'C',1);
    $pdf->Cell(45, 8,'Sucursal',1,0,'C',1);
    $pdf->Cell(35, 8,'Cant.',   1,1,'C',1);

    $pdf->SetFont('Arial','',9);
    foreach($products as $product){
        $total_q = 0;
        foreach($branches as $branch){
            $total_q += OperationData::getQYesF($product->id, $branch->id);
        }
        if($total_q <= 0) $pdf->SetTextColor(180,0,0);
        else $pdf->SetTextColor(0,0,0);

        $pdf->Cell(12, 7, $product->id,               1,0,'C');
        $pdf->Cell(32, 7, $product->barcode,           1,0,'C');
        $pdf->Cell(70, 7, utf8_decode($product->name), 1,0,'L');
        $pdf->Cell(45, 7, 'Todas las sucursales',      1,0,'C');
        $pdf->Cell(35, 7, $total_q,                    1,1,'C');
    }
    $pdf->SetTextColor(0,0,0);
}

$pdf->Output();
?>