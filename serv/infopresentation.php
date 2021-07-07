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
function get_porcen($deltotal,$valor) {
    //round(($totales_votan*100)/$data_sumatorio['socios'],2).
    
    return round(($deltotal*100)/$valor,2);
}
function get_data_sumatorio($id,$conexion) {
    
    $query_sum = "
    SELECT `presentation_id`,
    sum(`augc_voteassembly`.`auxint`) as socios,sum(`count1`) as si,sum(`count2`) as noo,sum(`count3`) as abstencion,
    sum(`count4`) as ausente 
    FROM `augc_voteassembly` 
    left join  `augc_delegacion` on `delegacionid`= `augc_delegacion`.`id`
    left join  `augc_answer` on `answer_id`= `augc_answer`.`id`
    WHERE `presentation_id`=$id
    group by `presentation_id`
    ";
    
    //print $query_sum;
    //exit;
    
    $result = mysqli_query($conexion, $query_sum);

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

function get_data_sumatorio_delega($id,$conexion) {
    
    $query_sum = "
    SELECT `presentation_id`,
    sum(`augc_voteassembly`.`auxint`) as socios,sum(`count1`) as si,sum(`count2`) as noo,sum(`count3`) as abstencion,
    sum(`count4`) as ausente 
    FROM `augc_voteassembly` 
    left join  `augc_delegacion` on `delegacionid`= `augc_delegacion`.`id`
    left join  `augc_answer` on `answer_id`= `augc_answer`.`id`
    WHERE `presentation_id`=$id
    and `augc_voteassembly`.`aux`='Secretario General (Junta Directiva Provincial)'    
    group by `presentation_id`
    ";
    
    //print $query_sum;
    //exit;
    
    $result = mysqli_query($conexion, $query_sum);

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

function get_data_presentation($id,$conexion) {
    
    //return '28231 Las Rozas Madrid prueba';
    
    $query = " SELECT `augc_presentation`.`title`,`augc_assembly`.`title` as title2 "
            . "FROM `augc_presentation`,`augc_assembly` "
            . "WHERE `augc_presentation`.id=$id "
            . "and `augc_presentation`.`assembly_id`=`augc_assembly`.`id`";	
	
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

function get_format_cargo($cargo) {
    
    $str1=str_replace('(Junta Directiva Nacional)', '', $cargo);
    $str2=str_replace('Secretaría de', '', $str1);
    $str3=str_replace('Secretaría', '', $str2);
    $str4=str_replace('Secretaria de', '', $str3);
    //Secretaria de
    
    return $str4;
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


$data_sumatorio=get_data_sumatorio($id,$conexion);
$data_sumatorio_delega=get_data_sumatorio_delega($id,$conexion);
//print_r($data_sumatorio);
//exit;
//mysqli_set_charset($con, 'utf8');
	
/*
 $query = "SELECT 'ccccc' as delegacion,100 as socios,'si' vota, count1 as 'si',
        count2 as n, count3 as 'abstencion', count4 as 'ausente'
        FROM `augc_voteassembly` WHERE `presentation_id`=$id";	
*/
$query = "
SELECT `augc_voteassembly`.`user_id` as usuario,`augc_delegacion`.`name` as delegacion,
`augc_answer`.`answer` as vota,`augc_voteassembly`.`aux` as cargo,
`augc_voteassembly`.`auxint` socios,`count1` as si,`count2` as noo,`count3` abstencion,
`count4` as ausente 
FROM `augc_voteassembly` 
left join  `augc_delegacion` on `delegacionid`= `augc_delegacion`.`id`
left join  `augc_answer` on `answer_id`= `augc_answer`.`id`
WHERE `presentation_id`=$id 
    and `augc_voteassembly`.`aux`='Secretario General (Junta Directiva Provincial)'
    LIMIT 0,20
";

$query1 = "
SELECT `augc_voteassembly`.`user_id` as usuario,`augc_delegacion`.`name` as delegacion,
`augc_answer`.`answer` as vota,`augc_voteassembly`.`aux` as cargo,
`augc_voteassembly`.`auxint` socios,`count1` as si,`count2` as noo,`count3` abstencion,
`count4` as ausente 
FROM `augc_voteassembly` 
left join  `augc_delegacion` on `delegacionid`= `augc_delegacion`.`id`
left join  `augc_answer` on `answer_id`= `augc_answer`.`id`
WHERE `presentation_id`=$id 
    and `augc_voteassembly`.`aux`='Secretario General (Junta Directiva Provincial)'
    LIMIT 20,200
";

$query2 = "
SELECT `augc_voteassembly`.`user_id` as usuario,`augc_delegacion`.`name` as delegacion,
`augc_answer`.`answer` as vota,`augc_voteassembly`.`aux` as cargo,
`augc_voteassembly`.`auxint` socios,`count1` as si,`count2` as noo,`count3` abstencion,
`count4` as ausente FROM `augc_voteassembly` 
left join `augc_delegacion` on `delegacionid`= `augc_delegacion`.`id` 
left join  `augc_answer` on `answer_id`= `augc_answer`.`id`
WHERE `presentation_id`=$id  "
        . "and `augc_voteassembly`.`aux`!='Secretario General (Junta Directiva Provincial)'";


//print $query;
//exit;

$result = mysqli_query($conexion, $query);
if (mysqli_num_rows($result) > 0) {
    $str="";
    while($fila = mysqli_fetch_assoc($result))
        {
            $str=$str.'<tr nobr="true">
            <td>'.$fila['delegacion'].'</td>
            <td>'.$fila['socios'].'</td>
            <td style="font-size: small;">'.$fila['vota'].'</td>    
            <td>'.$fila['si'].'</td>
            <td>'.$fila['noo'].'</td>
            <td>'.$fila['abstencion'].'</td>
            <td>'.$fila['ausente'].'</td>      
            </tr>';
        }      
}


$result1 = mysqli_query($conexion, $query1);
if (mysqli_num_rows($result1) > 0) {
    $str1="";
    while($fila1 = mysqli_fetch_assoc($result1))
        {
            $str1=$str1.'<tr nobr="true">
            <td>'.$fila1['delegacion'].'</td>
            <td>'.$fila1['socios'].'</td>
            <td style="font-size: small;">'.$fila1['vota'].'</td>    
            <td>'.$fila1['si'].'</td>
            <td>'.$fila1['noo'].'</td>
            <td>'.$fila1['abstencion'].'</td>
            <td>'.$fila1['ausente'].'</td>      
            </tr>';
        }      
}

$result2 = mysqli_query($conexion, $query2);
if (mysqli_num_rows($result2) > 0) {
    $str2="";
    while($fila = mysqli_fetch_assoc($result2))
        {
            $str2=$str2.'<tr nobr="true" style="font-size: small;">
            <td>'.get_format_cargo($fila['cargo']).'</td>  
            <td>'.$fila['socios'].'</td>
            <td style="font-size: small;">'.$fila['vota'].'</td>    
            <td>'.$fila['si'].'</td>
            <td>'.$fila['noo'].'</td>
            <td>'.$fila['abstencion'].'</td>
            <td>'.$fila['ausente'].'</td>      
            </tr>';
        }      
}

$data_presentation=get_data_presentation($id,$conexion);



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
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Informe Ponencia AUGC', PDF_HEADER_STRING);

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
$pdf->Cell(0, 5, 'Informe Resultados Ponencia', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('helvetica', '', 12);

// First name
$pdf->Cell(35, 5, 'Ponencia:');
//$pdf->TextField('firstname', 50, 5);
$pdf->Cell(50, 5,$data_presentation['title'].' ('.$data_presentation['title2'].')');
$pdf->Ln(6);


//Fecha – Localidad:
$hoy = date("Y-m-d H:i:s"); 
$createdat = get_format($hoy); 
$pdf->Cell(77, 5, 'Resultados de la ponencia con fecha de informe '.$createdat);

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



$tbl = '<table border="1" cellpadding="2" cellspacing="2" align="center" nobr="true">
 <thead>
  <tr>
  <th colspan="7" align="center">JUNTA DIRECTIVA PROVINCIAL 1/2</th>
 </tr>
 <tr style="background-color:white;color:#0000FF;font-size: small;">
  <td align="center">DELEGACIÓN</td>
  <td align="center">SOCIOS</td>
  <td align="center">VOTA</td>
  <td align="center">SI</td>
  <td align="center">NO</td>
  <td align="center">ABSTENCION</td>
  <td align="center">AUSENTE</td>
 </tr>
</thead>       
 '.$str.'
</table>
';

$tbl_2 = '<br><table border="1" cellpadding="2" cellspacing="2" align="center" nobr="true">
 <thead>
  <tr>
  <th colspan="7" align="center">JUNTA DIRECTIVA PROVINCIAL 2/2</th>
 </tr>
 <tr style="background-color:white;color:#0000FF;font-size: small;">
  <td align="center">DELEGACIÓN</td>
  <td align="center">SOCIOS</td>
  <td align="center">VOTA</td>
  <td align="center">SI</td>
  <td align="center">NO</td>
  <td align="center">ABSTENCION</td>
  <td align="center">AUSENTE</td>
 </tr>
</thead>       
 '.$str1.'
</table>
';

$tb2 = '<br><table border="1" cellpadding="2" cellspacing="2" align="center" nobr="true">
 <thead>
  <tr>
  <th colspan="7" align="center">JUNTA DIRECTIVA NACIONAL (JDN)</th>
 </tr>
 <tr style="background-color:white;color:#0000FF;font-size: small;">
  <td align="center">CARGO</td>
  <td align="center">SOCIOS</td>
  <td align="center">VOTA</td>
  <td align="center">SI</td>
  <td align="center">NO</td>
  <td align="center">ABSTENCION</td>
  <td align="center">AUSENTE</td>
 </tr>
</thead>       
 '.$str2.'
</table>
';

$pdf->writeHTML($tbl, true, false, false, false, '');
$pdf->writeHTML($tbl_2, true, false, false, false, '');
$pdf->AddPage();

$pdf->writeHTML($tb2, true, false, false, false, '');

/*
 $votos_emitidos=$votos_emitidos+$fila['socios'];
            if($fila['vota']!='') { $votos_emitidos_votan=$votos_emitidos_votan+1;}
            $votos_emitidos_si=$votos_emitidos_si+$fila['si'];
            $votos_emitidos_no=$votos_emitidos_no+$fila['noo'];
            $votos_emitidos_abs=$votos_emitidos_abs+$fila['abstencion'];
            $votos_emitidos_ause=$votos_emitidos_ause+$fila['ausente'];
 */

$totales_votan=intval($data_sumatorio['si']+$data_sumatorio['noo']+$data_sumatorio['abstencion']);

$tb_totales_1 = '<br><table border="1" cellpadding="2" cellspacing="2" align="center" nobr="true">
 <thead>
 <tr style="background-color:white;color:#0000FF;font-size: small;">
  <td align="center"></td>
  <td align="center">SOCIOS</td>
  <td align="center">VOTA</td>
  <td align="center">SI</td>
  <td align="center">NO</td>
  <td align="center">ABSTENCION</td>
  <td align="center">AUSENTE</td>
 </tr>
 </thead>       
 <tr nobr="true" style="font-size: small;">
            <td>TOTALES</td>  
            <td>'.$data_sumatorio['socios'].'</td>
            <td>'.$totales_votan.'</td>    
            <td>'.$data_sumatorio['si'].'</td>
            <td>'.$data_sumatorio['noo'].'</td>
            <td>'.$data_sumatorio['abstencion'].'</td>
            <td>'.$data_sumatorio['ausente'].'</td>      
</tr>
<tr nobr="true" style="font-size: small;">
            <td>PORCENTAJE</td>  
            <td>100%</td>
            <td>'.get_porcen($totales_votan,$data_sumatorio['socios']).'%</td>    
            <td>'.get_porcen($data_sumatorio['si'],$data_sumatorio['socios']).'%</td>
            <td>'.get_porcen($data_sumatorio['noo'],$data_sumatorio['socios']).'%</td>
            <td>'.get_porcen($data_sumatorio['abstencion'],$data_sumatorio['socios']).'%</td>
            <td>'.get_porcen($data_sumatorio['ausente'],$data_sumatorio['socios']).'%</td>      
</tr>
</table>
';


$totales_votan_delega=intval($data_sumatorio_delega['si']+$data_sumatorio_delega['noo']+$data_sumatorio_delega['abstencion']);

$tb_totales_delega = '<br><table border="1" cellpadding="2" cellspacing="2" align="center" nobr="true">
 <thead>
 <tr style="background-color:white;color:#0000FF;font-size: small;">
  <td align="center"></td>
  <td align="center">SOCIOS</td>
  <td align="center">VOTA</td>
  <td align="center">SI</td>
  <td align="center">NO</td>
  <td align="center">ABSTENCION</td>
  <td align="center">AUSENTE</td>
 </tr>
 </thead>       
 <tr nobr="true" style="font-size: small;">
            <td>DELEGACIONES</td>  
            <td>'.$data_sumatorio_delega['socios'].'</td>
            <td>'.$totales_votan_delega.'</td>    
            <td>'.$data_sumatorio_delega['si'].'</td>
            <td>'.$data_sumatorio_delega['noo'].'</td>
            <td>'.$data_sumatorio_delega['abstencion'].'</td>
            <td>'.$data_sumatorio_delega['ausente'].'</td>      
</tr>
<tr nobr="true" style="font-size: small;">
            <td>PORCENTAJE</td>  
            <td>100%</td>
            <td>'.get_porcen($totales_votan_delega,$data_sumatorio_delega['socios']).'%</td>    
            <td>'.get_porcen($data_sumatorio_delega['si'],$data_sumatorio_delega['socios']).'%</td>
            <td>'.get_porcen($data_sumatorio_delega['noo'],$data_sumatorio_delega['socios']).'%</td>
            <td>'.get_porcen($data_sumatorio_delega['abstencion'],$data_sumatorio_delega['socios']).'%</td>
            <td>'.get_porcen($data_sumatorio_delega['ausente'],$data_sumatorio_delega['socios']).'%</td>      
</tr>
</table>
';

$tb_totales_2 = '<br><table border="1" cellpadding="2" cellspacing="2" align="center" nobr="true">
  <thead>
 <tr style="background-color:white;color:#0000FF;font-size: small;">
  <td align="center"></td>
  <td align="center">VOTA</td>
  <td align="center">SI</td>
  <td align="center">NO</td>
  <td align="center">ABSTENCION</td>
 </tr>
 </thead>      
 <tr nobr="true" style="font-size: small;">
            <td>VOTOS EMITIDOS</td>  
             <td>'.$totales_votan.'</td>    
            <td>'.$data_sumatorio['si'].'</td>
            <td>'.$data_sumatorio['noo'].'</td>
            <td>'.$data_sumatorio['abstencion'].'</td>    
</tr>
<tr nobr="true" style="font-size: small;">
            <td>PORCENTAJES EN VOTACIÓN</td>  
            <td>100%</td>';


if($data_sumatorio['si']>$data_sumatorio['no']) {
    $str_1=' style="background-color:#FFFF66;"';
}

if($data_sumatorio['si']<$data_sumatorio['no']) {
    $str_2=' style="background-color:#FFFF66;"';
}

$tb_totales_2 = $tb_totales_2 .'            
            <td'.$str_1.'>'.get_porcen($data_sumatorio['si'],$data_sumatorio['socios']).'%</td>
            <td'.$str_2.'>'.get_porcen($data_sumatorio['noo'],$data_sumatorio['socios']).'%</td>
            <td'.$str_3.'>'.get_porcen($data_sumatorio['abstencion'],$data_sumatorio['socios']).'%</td>
</tr>
</table>
';

/*
$tb_fin = '<br><table border="1" cellpadding="2" cellspacing="2" align="center" nobr="true">
  <thead>
 </thead>      
 <tr nobr="true" style="font-size: small;background-color:#FFFF66;">
            <td>RESULTADO VOTACIÓN</td>  
            <td>'.$resultado.'</td>      
</tr>
</table>
';*/

$pdf->writeHTML($tb_totales_1, true, false, false, false, '');
$pdf->writeHTML($tb_totales_delega, true, false, false, false, '');
$pdf->writeHTML($tb_totales_2, true, false, false, false, '');

//$pdf->writeHTML($tb_fin, true, false, false, false, '');

//Close and output PDF document
$pdf->Output('informe_asamblea_augc.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

