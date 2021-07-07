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
function get_location($id_loc,$conexion) {
    
    //return '28231 Las Rozas Madrid prueba';
    
    $query = " SELECT t.`name`,t.`postal_code`,p.`name` as province FROM `augc_town` t, `augc_province` p WHERE t.`id`=$id_loc and t.`province_id` = p.`id`";	
	
    $result = mysqli_query($conexion, $query);

//print 'INICO ************************ MODO EXEC <br><br>';

    if (mysqli_num_rows($result) > 0) {
        $fila = mysqli_fetch_assoc($result);      
    }

    if(!is_array($fila)) {
        return 'error';
    } else {
         return $fila['postal_code'].' '.$fila['name'].' '.$fila['province'];
    }
}

function get_swift($iban,$conexion) {
    $query = " SELECT bic FROM `augc_bank_entities` WHERE `bank_code` like SUBSTRING('$iban', 5, 4) ";	
	
    $result = mysqli_query($conexion, $query);

//print 'INICO ************************ MODO EXEC <br><br>';

    if (mysqli_num_rows($result) > 0) {
        $fila = mysqli_fetch_assoc($result);      
    }

    if(!is_array($fila)) {
        return 'error';
    } else {
         return $fila['bic'];
    }
}


$token=$_REQUEST['token'];
$id=$_REQUEST['id'];

if($token=='' || $id=='') {
    print 'error parametros';
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
	

$query = " SELECT u.`firstname`,u.`lastname`,u.`address`,u.`iban`,u.`provinceId`,u.`townId`,s.createdat,s.id 
    FROM `fos_user_user` u,`augc_user_sepa` s WHERE u.`id` = s.`user_id`
    AND s.`aux`='$token' and s.`id`=$id";	
	
$result = mysqli_query($conexion, $query);

//print 'INICO ************************ MODO EXEC <br><br>';

if (mysqli_num_rows($result) > 0) {
    $fila = mysqli_fetch_assoc($result);      
}

if(!is_array($fila)) {
    print 'error token o id incorrecto';
    exit;
}

//print_r($fila);exit;
//Array ( [firstname] => Jonas [lastname] => Mellez [address] => Real,2 [iban] => NL29INGB7070755813 [provinceId] => 28 [townId] => 38400 )

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('AUGC');
$pdf->SetTitle('Mandato SEPA');
$pdf->SetSubject('Mandato SEPA');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' ', PDF_HEADER_STRING);

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
$pdf->Cell(0, 5, 'Orden de domiciliación de adeudo directo SEPA', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('helvetica', '', 12);

// First name
$pdf->Cell(77, 5, 'Referencia de la orden de domiciliación:');
//$pdf->TextField('firstname', 50, 5);
$pdf->Cell(50, 5, 'SP'.str_pad($fila['id'], 4, "0", STR_PAD_LEFT));
$pdf->Ln(6);

// Last name
$pdf->Cell(77, 5, 'Identificador del acreedor:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, 'AUGC');
$pdf->Ln(6);

$pdf->Cell(77, 5, '(A) Nombre del acreedor:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, 'Asociación Unificada de Guardias Civiles');
$pdf->Ln(6);

$pdf->Cell(77, 5, 'Dirección:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, 'Av. de la Reina Victoria, 37, 2º Centro');
$pdf->Ln(6);

//Código postal -Población -Provincia
$pdf->Cell(77, 5, 'Código postal - Población - Provincia:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, '28003 Madrid Madrid');
$pdf->Ln(6);

$pdf->Cell(77, 5, 'País:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, 'España');
$pdf->Ln(6);

$pdf->Ln(6);

$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(180, 5, 'Mediante la firma de esta orden de domiciliación, el deudor autoriza (A) al acreedor a enviar instrucciones a la entidad del deudor para adeudar su cuentay (B) a la entidad para efectuar los adeudos en su cuenta siguiendo las instrucciones del acreedor. Como parte de sus derechos, el deudor está legitimado al reembolso por su entidad en los términos y condiciones del contrato suscrito con la misma. La solicitud de reembolso deberá efectuarse dentro de las ocho semanas que siguen a la fecha de adeudo en cuenta.');
//$pdf->Ln(6);
$pdf->Cell(180, 5, 'Puede obtener información adicional sobre sus derechos en su entidad financiera.');
$pdf->SetFont('helvetica', '', 12);


//Array ( [firstname] => Jonas [lastname] => Mellez [address] => Real,2 [iban] => NL29INGB7070755813 [provinceId] => 28 [townId] => 38400 )
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Cell(77, 5, '(B) Nombre del deudor (titular cuenta):');
//$pdf->TextField('firstname', 50, 5);
$pdf->Cell(50, 5, ''.$fila['firstname'].' '.$fila['lastname']);
$pdf->Ln(6);

$pdf->Cell(77, 5, 'Dirección del deudor:');
//$pdf->TextField('firstname', 50, 5);
$pdf->Cell(50, 5, ''.$fila['address']);
$pdf->Ln(6);

//provinceId`,u.`townId`
$str_value=get_location($fila['townId'],$conexion);
$pdf->Cell(77, 5, 'Código postal - Población - Provincia:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, $str_value);
$pdf->Ln(6);

$pdf->Cell(77, 5, 'País del deudor:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, 'España');
$pdf->Ln(6);

$pdf->Cell(77, 5, 'Swift BIC:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, get_swift($fila['iban'],$conexion));
$pdf->Ln(6);

$pdf->Cell(77, 5, 'Número de cuenta - IBAN:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, ''.$fila['iban']);
$pdf->Ln(6);
//Núme ro de cuenta - IBAN

$pdf->Cell(77, 5, 'Tipo de pago:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, 'Pago recurrente');
$pdf->Ln(6);

//Fecha – Localidad:
$pdf->Cell(77, 5, 'Fecha – Localidad:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));

$arr_date=explode(' ',$fila['createdat']);
$pdf->Cell(50, 5, $arr_date[0].' - Madrid');
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
//Firma del deudor:

$pdf->Cell(77, 5, 'FIRMA DEL DEUDOR');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
//$pdf->Cell(50, 5, '');
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(120, 5, 'UNA VEZ FIRMADA ESTA ORDEN DE DOMICILIACIÓN DEBE SER ENVIADA AL ACREEDOR (AUGC) PARA SU CUSTODIA.');
$pdf->Ln(6);
$pdf->Cell(120, 5, 'DEBERÁ ENVIAR ESTA ORDEN FIRMADA AL MAIL GESTIONASOCIADOS@AUGC.ORG O POR CORREO ORDINARIO.');
//UNA VEZ FIRMADA EST A ORDEN DE DOMICILIACIÓN DEBE SER ENVIADA AL ACREEDOR PARA SU CUSTODIA.


//Close and output PDF document
$pdf->Output('mandato_sepa_augc.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
