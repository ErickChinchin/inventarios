<?php
include "../core/autoload.php";
include "../core/app/autoload.php";
Core::$root="../";

require('../fpdf/fpdf.php');

if(isset($_GET["id"]) && $_GET["id"]!=""){
    $sell = SellData::getById($_GET["id"]);
    $operations = OperationData::getAllProductsBySellId($_GET["id"]);
    $total = 0;

    class PDF extends FPDF {
        function Header() {
            $title_config = ConfigurationData::getByShort("title");
            $store_name = $title_config ? $title_config->val : "JAMAAL";
            $this->SetFont('Arial','B',16);
            $this->Cell(0,10,utf8_decode($store_name),0,1,'C');
            $this->SetFont('Arial','I',10);
            $this->SetTextColor(100, 100, 100);
            $this->Cell(0,5,utf8_decode('Sistema de Control de Inventario y Ventas'),0,1,'C');
            $this->SetTextColor(0, 0, 0);
            $this->Ln(5);
        }
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,'NOTA DE VENTA #'.$sell->id,0,1,'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial','B',10);
    if($sell->person_id!=""){
        $client = $sell->getPerson();
        $pdf->Cell(30,7,'Cliente: ',0,0);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(0,7,utf8_decode($client->name." ".$client->lastname),0,1);
    }
    if($sell->user_id!=""){
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(30,7,'Atendido por: ',0,0);
        $pdf->SetFont('Arial','',10);
        $user = $sell->getUser();
        $pdf->Cell(0,7,utf8_decode($user->name." ".$user->lastname),0,1);
    }
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30,7,'Fecha: ',0,0);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,7,$sell->created_at,0,1);
    $pdf->Ln(5);

    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(232,232,232);
    $pdf->Cell(35,10,utf8_decode('Código/Barras'),1,0,'C',1);
    $pdf->Cell(15,10,'Cant',1,0,'C',1);
    $pdf->Cell(70,10,'Producto',1,0,'C',1);
    $pdf->Cell(35,10,'Precio U.',1,0,'C',1);
    $pdf->Cell(35,10,'Total',1,1,'C',1);

    $pdf->SetFont('Arial','',10);
    foreach($operations as $operation){
        $product = $operation->getProduct();
        $code_to_show = ($product->barcode != "") ? $product->barcode : $product->id;
        $pdf->Cell(35,8,$code_to_show,1,0,'C');
        $pdf->Cell(15,8,$operation->q,1,0,'C');
        $pdf->Cell(70,8,utf8_decode($product->name),1,0,'L');
        $pdf->Cell(35,8,"$ ".number_format($product->price_out,2),1,0,'R');
        $op_total = $operation->q * $product->price_out;
        $pdf->Cell(35,8,"$ ".number_format($op_total,2),1,1,'R');
        $total += $op_total;
    }

    $pdf->Ln(5);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(155,8,'Descuento: ',0,0,'R');
    $pdf->Cell(35,8,"-$ ".number_format($sell->discount,2),1,1,'R');
    
    $pdf->Cell(155,8,'Subtotal: ',0,0,'R');
    $pdf->Cell(35,8,"$ ".number_format($total,2),1,1,'R');

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(155,8,'TOTAL: ',0,0,'R');
    $pdf->SetFillColor(200,255,200);
    $pdf->Cell(35,8,"$ ".number_format($total-$sell->discount,2),1,1,'R',1);

    $pdf->Output();
}
?>
