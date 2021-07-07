<?php
//============================================================+
// File name   : example_014.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 014 for TCPDF class
//               Javascript Form and user rights (only works on Adobe Acrobat)
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Javascript Form and user rights (only works on Adobe Acrobat)
 * @author Nicola Asuni
 * @since 2008-03-04
 */
function get_data_assembly($id,$conexion) {
    
    //return '28231 Las Rozas Madrid prueba';
    
    $query = " SELECT `title`,`dateassembly` FROM `augc_assembly` WHERE id=$id";	
	
    $result = mysqli_query($conexion, $query);

//print 'INICO ************************ MODO EXEC <br><br>';

    if (mysqli_num_rows($result) > 0) {
        $fila = mysqli_fetch_assoc($result);      
    }

    if(!is_array($fila)) {
        return false;
    } else {
         return $fila;
    }
}

function get_format($date) {
    
    $arr=explode(' ',$date);
    
    $arr2=explode('-',$arr[0]);
    return $arr2[2].'-'.$arr2[1].'-'.$arr2[0];
}


$token=$_REQUEST['token'];
$id=$_REQUEST['id'];

if($token=='' || $id=='') {
    print 'error parametros';
    exit;
}

if($token!=='DuVYjUWs1wZuQw4kSQ3boZEDXRBcqJVU') {
    print 'error token';
    exit;
}

$server = "localhost";
$user = "sqladmin";
$password = "Augc10y10es20";
$bd = "augc_prod";

$conexion = mysqli_connect($server, $user, $password, $bd);

if (!$conexion){ 
    die('Error de Conexión: ' . mysqli_connect_errno());
}   

//mysqli_set_charset($con, 'utf8');
	

 $query = "SELECT id,title as ponencia,textvote as texto,is_active,
                (SELECT`count` FROM `augc_reportvote` WHERE `answer_id`=1 
                and `presentation_id`= `augc_presentation`.`id`) as si,
                (SELECT`count` FROM `augc_reportvote` WHERE `answer_id`=2 
                and `presentation_id`= `augc_presentation`.`id`) as n,
                (SELECT`count` FROM `augc_reportvote` WHERE `answer_id`=3 
                and `presentation_id`= `augc_presentation`.`id`) as abstencion,
                (SELECT`count` FROM `augc_reportvote` WHERE `answer_id` =4 
                and `presentation_id`= `augc_presentation`.`id`) as ausente
                FROM `augc_presentation` WHERE assembly_id =  $id
                order by id ASC";	



$result = mysqli_query($conexion, $query);

//print 'INICO ************************ MODO EXEC <br><br>';
if (mysqli_num_rows($result) > 0) {
    $str="";
    while($fila = mysqli_fetch_assoc($result))
        {
            $str=$str.'<tr nobr="true">
            <td>'.$fila['ponencia'].'</td>
            <td>'.$fila['si'].'</td>
            <td>'.$fila['n'].'</td>
            <td>'.$fila['abstencion'].'</td>
            <td>'.$fila['ausente'].'</td>      
            </tr>';
        }      
}

$data_assembly=get_data_assembly($id,$conexion);



//Array ( [firstname] => Jonas [lastname] => Mellez [address] => Real,2 [iban] => NL29INGB7070755813 [provinceId] => 28 [townId] => 38400 )

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('AUGC');
$pdf->SetTitle('Informe Asamblea');
$pdf->SetSubject('Informe Asamblea');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Informe Asamblea AUGC', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// IMPORTANT: disable font subsetting to allow users editing the document
$pdf->setFontSubsetting(false);

// set font
$pdf->SetFont('helvetica', '', 10, '', false);

// add a page
$pdf->AddPage();

/*
It is possible to create text fields, combo boxes, check boxes and buttons.
Fields are created at the current position and are given a name.
This name allows to manipulate them via JavaScript in order to perform some validation for instance.
*/

// set default form properties
$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));

$pdf->SetFont('helvetica', 'BI', 12);
$pdf->Cell(0, 5, 'Informe Resultados Asamblea', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('helvetica', '', 12);

// First name
$pdf->Cell(35, 5, 'Asamblea:');
//$pdf->TextField('firstname', 50, 5);
$pdf->Cell(50, 5,$data_assembly['title']);
$pdf->Ln(6);

// Last name
$pdf->Cell(35, 5, 'Fecha asamblea:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, get_format($data_assembly['dateassembly']));
$pdf->Ln(6);
$pdf->Ln(6);
//Fecha – Localidad:
$hoy = date("Y-m-d H:i:s"); 
$createdat = get_format($hoy); 
$pdf->Cell(77, 5, 'Resultados de la asamble con fecha de informe '.$createdat);

$pdf->Ln(6);

$image1 = "sello_peq.png";
$pdf->Image($image1, $pdf->GetX(), $pdf->GetY(), 33.78);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
//Array ( [id] => 1 [ponencia] => PONENCIA DE PRUEBAS 1 
//[texto] => ¿Está de acuerdo con la convocatoria de manifestación? 
//[is_active] => 0 [si] => 0 [n] => 0 [abstencion] => 0 [ausente] => 22414 [mivoto] => )



$tbl = '<table border="1" cellpadding="2" cellspacing="2" align="center">
 <thead>
 <tr style="background-color:white;color:#0000FF;">
  <td align="center">PONENCIA</td>
  <td align="center">SI</td>
  <td align="center">NO</td>
  <td align="center">ABSTENCION</td>
  <td align="center">AUSENTE</td>
 </tr>
</thead>       
 '.$str.'
</table>
';

$pdf->writeHTML($tbl, true, false, false, false, '');

//Close and output PDF document
$pdf->Output('informe_asamblea_augc.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

