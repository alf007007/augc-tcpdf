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

function exist_certifi($user_id,$conexion) {
    //SELECT `id` FROM `augc_certificate` WHERE `user_id`= and `is_active`=1
    
    $query = "SELECT `id` FROM `augc_certificate` WHERE `user_id`=$user_id and `is_active`=1";	
	
    $result = mysqli_query($conexion, $query);

//print 'INICO ************************ MODO EXEC <br><br>';

    if (mysqli_num_rows($result) > 0) {
        $fila = mysqli_fetch_assoc($result);      
    }

    if(!is_array($fila)) {
        return false;
    } else {
         return true;
    }
}

function insert_certifi($user_id,$conexion) {
    //SELECT `id` FROM `augc_certificate` WHERE `user_id`= and `is_active`=1
    $hoy = date("Y-m-d H:i:s");
    
    $hoy2 = date("YmdHis");
    
    $hoy3 = date("Y-m-d");
    
    $valido = date("Y-m-d",strtotime($hoy3."+ 5 days"));
    
    $random_string =   chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90));

    $query = "INSERT INTO `augc_certificate` (`id`, `user_id`, `createdat`, `is_active`, `aux`, `auxint`, `auxb`, `auxdate`) "
            . "VALUES (NULL, '$user_id', '$hoy', '1', '$random_string', NULL, NULL, '$valido')";	
    
    //print $query;exit;
    
    $result = mysqli_query($conexion, $query);
    
    return true;

}


function get_token_certifi($user_id,$conexion) {
    //SELECT `id` FROM `augc_certificate` WHERE `user_id`= and `is_active`=1
    
    $query = "SELECT `aux` FROM `augc_certificate` WHERE `user_id`=$user_id and `is_active`=1";	
	
    $result = mysqli_query($conexion, $query);

//print 'INICO ************************ MODO EXEC <br><br>';

    if (mysqli_num_rows($result) > 0) {
        $fila = mysqli_fetch_assoc($result);      
    }

    if(!is_array($fila)) {
        return false;
    } else {
        return $fila['aux'];
    }
}

function get_certifi($user_id,$conexion) {
    //SELECT `id` FROM `augc_certificate` WHERE `user_id`= and `is_active`=1
    
    $query = "SELECT * FROM `augc_certificate` WHERE `user_id`=$user_id and `is_active`=1";	
    	
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

function get_token_user($user_id,$conexion) {
    //SELECT `id` FROM `augc_certificate` WHERE `user_id`= and `is_active`=1
    
    $query = "SELECT `created_at`,`socio_id` FROM `fos_user_user` WHERE `id`=$user_id";	
	
    $result = mysqli_query($conexion, $query);

//print 'INICO ************************ MODO EXEC <br><br>';

    if (mysqli_num_rows($result) > 0) {
        $fila = mysqli_fetch_assoc($result);      
    }

    if(!is_array($fila)) {
        return false;
    } else {
        $aux= str_replace('-','',$fila['created_at']);
        $aux2= str_replace(':','',$aux);
        $aux3= str_replace(' ','',$aux2);
        
        $token= $aux3.$fila['socio_id'];
        //print $token;exit;
        return $token;
    }
}

//SELECT * FROM `augc_certificate` WHERE `createdat` >= ' 2021-01-25 00:00:00' and `createdat` <= ' 2021-01-25 23:59:59'
function disable_certifi($conexion) {
    //SELECT `id` FROM `augc_certificate` WHERE `user_id`= and `is_active`=1
    
    $hoy = date("Y-m-d"); 
    $hace_5_dias = date("Y-m-d",strtotime($hoy."- 5 days"));
    
    $query = "UPDATE `augc_certificate` SET `is_active`=0 WHERE `createdat` < '$hace_5_dias 00:00:00' and `is_active`=1";	
	
    $result = mysqli_query($conexion, $query);

//print 'INICO ************************ MODO EXEC <br><br>';
}

function get_format($date) {
    
    $arr=explode(' ',$date);
    
    $arr2=explode('-',$arr[0]);
    return $arr2[2].'-'.$arr2[1].'-'.$arr2[0];
}

$token=$_REQUEST['token'];
$id=$_REQUEST['id'];
$type=$_REQUEST['type'];

if(/*$token=='' ||*/ $id=='') {
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

disable_certifi($conexion);


$query = " SELECT u.id,u.`firstname`,u.`lastname`,u.`address`,u.`iban`,u.`provinceId`,u.`townId`,
    u.dni, u.socio_id, d.name as delegacion, u.registration_date
    FROM `fos_user_user` u, augc_delegacion d WHERE u.`id`=$id and u.delegacionId = d.id "
        . " ";	

//print $query;exit;

$result = mysqli_query($conexion, $query);

//print 'INICO ************************ MODO EXEC <br><br>';

if (mysqli_num_rows($result) > 0) {
    $fila = mysqli_fetch_assoc($result);      
}

if(!is_array($fila) || $token!== get_token_user($fila['id'],$conexion) ) {
    print 'error token o id incorrecto';
    exit;
}

//https://augc.info/augc-tcpdf/serv/certifi.php?id=13&token=2014012300364815
if(!exist_certifi($fila['id'],$conexion)) {
    insert_certifi($fila['id'],$conexion);
    if(!exist_certifi($fila['id'],$conexion)) die('Error de datos insert');
    $data_certi=get_certifi($fila['id'],$conexion);
} else {
    $data_certi=get_certifi($fila['id'],$conexion);
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
$pdf->SetTitle('Certificado de Afiliación');
$pdf->SetSubject('Certificado de Afiliación');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'CERTIFICADO', PDF_HEADER_STRING);

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
$pdf->Cell(0, 5, 'Certificado de Afiliación Asociación Unificada De Guardias Civiles', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('helvetica', '', 12);

// First name
$pdf->Cell(77, 5, 'Referencia Certificado'
        . ':');
//$pdf->TextField('firstname', 50, 5);
$pdf->Cell(50, 5, str_pad($data_certi['id'], 3, "0", STR_PAD_LEFT));
$pdf->Ln(6);

$pdf->Cell(77, 5, 'Clave Certificado'
        . ':');
//$pdf->TextField('firstname', 50, 5);
$pdf->Cell(50, 5, $data_certi['aux']);
$pdf->Ln(6);

// Last name
$pdf->Cell(77, 5, 'Número De Socio:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, str_pad($fila['socio_id'], 3, "0", STR_PAD_LEFT));
$pdf->Ln(6);

$pdf->Cell(77, 5, 'Nombre:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, $fila['firstname']);
$pdf->Ln(6);

$pdf->Cell(77, 5, 'Apellidos:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, $fila['lastname']);
$pdf->Ln(6);

//Código postal -Población -Provincia
$pdf->Cell(77, 5, 'DNI:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, $fila['dni']);
$pdf->Ln(6);

$pdf->Cell(77, 5, 'Delegación:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$pdf->Cell(50, 5, $fila['delegacion']);
$pdf->Ln(6);

$pdf->Cell(77, 5, 'Fecha Alta:');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
$arr_aux=explode(' ',$fila['registration_date']);
$arr_fecha=explode('-',$arr_aux[0]);
$pdf->Cell(50, 5,$arr_fecha[2].'-'.$arr_fecha[1].'-'.$arr_fecha[0]);
$pdf->Ln(6);

$pdf->Ln(6);

$pdf->SetFont('helvetica', '', 8);
$pdf->MultiCell(180, 5, 'Mediante el presente certificado de afiliación AUGC acredita la pertenencia a la asociación del usuario indicado.');
//$pdf->Ln(2);
//$pdf->Cell(180, 5, 'Puede obtener información adicional sobre este certificado en augc@augc.org');
//$pdf->Ln(4);
$pdf->Cell(180, 5, 'Puede comprobar la validez de este certificado en https://validacertificado.augc.org');
$pdf->SetFont('helvetica', '', 12);


$pdf->Ln(6);
$pdf->Ln(6);
//Fecha – Localidad:
$createdat = get_format($data_certi['createdat']); 
$valido = get_format($data_certi['auxdate']);
$pdf->Cell(77, 5, $createdat.' (Válido hasta '.$valido.')');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));




//$pdf->Cell(50, 5, $hoy);
$pdf->Ln(6);
//$pdf->Ln(6);

//Firma del deudor:

//$pdf->Cell(77, 5, 'FIRMA');
//$pdf->TextField('lastname', 50, 5,array(),array('v'=>'Lorem ipsum'));
//$pdf->Cell(50, 5, '');

$image1 = "sello_peq.png";
$pdf->Image($image1, $pdf->GetX(), $pdf->GetY(), 33.78);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
$pdf->Ln(6);
//UNA VEZ FIRMADA EST A ORDEN DE DOMICILIACIÓN DEBE SER ENVIADA AL ACREEDOR PARA SU CUSTODIA.


//Close and output PDF document
if(isset($type)) {
    $pdf->Output('certificado_augc.pdf',$type);
} else {
    $pdf->Output('certificado_augc.pdf','I');
}

//============================================================+
// END OF FILE
//============================================================+
