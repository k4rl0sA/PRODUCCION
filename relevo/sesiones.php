<?php
require_once "../libs/gestion.php";
ini_set('display_errors','1');
$perf=perfil($_POST['tb']);
if (!isset($_SESSION['us_sds'])) die("<script>window.top.location.href='/';</script>");
else {
  $rta="";
  switch ($_POST['a']){
  case 'csv': 
    header_csv ($_REQUEST['tb'].'.csv');
    $rs=array('','');    
    echo csv($rs,'');
    die;
    break;
  default:
    eval('$rta='.$_POST['a'].'_'.$_POST['tb'].'();');
    if (is_array($rta)) json_encode($rta);
	else echo $rta;
  }   
}

function lis_session(){
	// print_r($_POST);
	// print_r($_REQUEST);
	$id=divide($_POST['id']);
	$info=datos_mysql("SELECT COUNT(*) total FROM `rel_sesion` WHERE rel_tipo_doc='{$id[0]}' and rel_documento='{$id[1]}'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=3;
	$pag=(isset($_POST['pag-session']))? ($_POST['pag-session']-1)* $regxPag:0;

	$sql="SELECT idsesion ACCIONES,`rel_validacion1` Sesion, `rel_validacion2` Fecha,rel_validacion3 perfil,FN_CATALOGODESC(301,`rel_validacion4`) Actividad,`rel_validacion5`
  	descripcion 
	FROM `rel_sesion`
	WHERE rel_tipo_doc='{$id[0]}' and rel_documento='{$id[1]}'";
	$sql.=" ORDER BY fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
		//  echo $sql;
		$datos=datos_mysql($sql);
		return create_table($total,$datos["responseResult"],"session",$regxPag,'sesiones.php');
}

function lis_medidaux(){
	// print_r($_POST);
	// print_r($_REQUEST);
	$id=divide($_POST['id']);
	$info=datos_mysql("SELECT COUNT(*) total FROM rel_signvitales WHERE tipo_doc='{$id[0]}' and idpersona='{$id[1]}'");
	$total=$info['responseResult'][0]['total'];
	$regxPag=3;
	$pag=(isset($_POST['pag-session']))? ($_POST['pag-session']-1)* $regxPag:0;

	$sql="SELECT idsignos ACCIONES,tas , tad,frecard Frecuencia,satoxi Saturación,fecha_create 'Fecha de Ingreso'
	FROM `rel_signvitales`
	WHERE tipo_doc='{$id[0]}' and idpersona='{$id[1]}'";
	$sql.=" ORDER BY fecha_create";
	$sql.=' LIMIT '.$pag.','.$regxPag;
		//  echo $sql;
		$datos=datos_mysql($sql);
		return create_table($total,$datos["responseResult"],"medidaux",$regxPag,'sesiones.php');
}

/* function lis_session() {
    $id = divide($_POST['id']);
    $regxPag = 3;
    $pag = isset($_POST['pag-session']) ? ($_POST['pag-session'] - 1) * $regxPag : 0;
    $sql = "SELECT idsesion ACCIONES, `rel_validacion1` Sesion, `rel_validacion2` Fecha, rel_validacion3 perfil, FN_CATALOGODESC(301, `rel_validacion4`) Actividad, `rel_validacion5` descripcion,
                (SELECT COUNT(*) FROM `rel_sesion` WHERE rel_tipo_doc='{$id[0]}' and rel_documento='{$id[1]}') AS total
            FROM `rel_sesion`
            WHERE rel_tipo_doc='{$id[0]}' and rel_documento='{$id[1]}'
            ORDER BY fecha_create
            LIMIT $pag, $regxPag";
            
    $datos = datos_mysql($sql);
    $total = $datos["responseResult"][0]['total'];
    return create_table($total, $datos["responseResult"], "session", $regxPag, 'sesiones.php');
} */


function cmp_sesiones() {
	$rta="";
	// $rta .="<div class='encabezado'>TABLA DE SESIONES</div>	<div class='contenido' id='sesion-lis' >".lis_sesiones()."</div></div>";
	$rta .="<div class='encabezado placuifam'>TABLA DE COMPROMISOS CONCERTADOS</div>
	<div class='contenido' id='session-lis' >".lis_session()."</div></div>";
	$info=datos_mysql("SELECT FN_PERFIL('{$_SESSION['us_sds']}') perfil;");
	$per=$info['responseResult'][0]['perfil'];
	/*$id=divide($_POST['id']);
	 $data=datos_mysql("SELECT count(*)+1 total FROM rel_sesion WHERE rel_tipo_doc='{$id[0]}' and rel_documento='{$id[1]}';");
	$nse=$data['responseResult'][0]['total']; */
	$w='sesiones';
	$d='';
	// $perf=($d['rel_validacion3']=='')? $perfil:$d['rel_validacion3'];
	// $u=($d['rel_tipo_doc']=='')?true:false;
	$o='infgen';
	$c[]=new cmp($o,'e',null,'Sesion de intervencion y/o Relevos',$w);	
	$aux = ($per=='AUXREL' || $per=='ADM') ? true : false ;//|| $per=='ADM'
	$c[]=new cmp('id','h','20',$_POST['id'],$w.' '.$o,'','',null,null,false,false,'','col-1');
	$c[]=new cmp('rel_validacion1','s','3',$d,$w.' '.$o,'Sesión','rel_sesiones',null,null,true,true,'','col-2');
	$c[]=new cmp('rel_validacion2','d','10',$d,$w.' '.$o,'Fecha de la sesion','rel_validacion2',null,null,true,true,'','col-3',"validDate(this,-22);dateSesion(this);");
	$c[]=new cmp('rel_validacion3','t','5',$per,$w.' '.$o,'Perfil','rel_validacion3',null,null,true,false,'','col-2');
	$c[]=new cmp('rel_validacion4','s','3',$d,$w.' act '.$o,'ACTIVIDAD DE RESPIRO','rel_validacion4',null,null,true,true,'','col-3');
	$c[]=new cmp('rel_validacion5','a','1500',$d,$w.' '.$o,'DESCRIPCION DE LA INTERVENCION','rel_validacion5',null,null,true,true,'','col-10');

	$o='infbit';
	$c[]=new cmp($o,'e',null,'BITACORA DE SESIÓN',$w);	
	$c[]=new cmp('autocuidado','s','3',$d,$w.' aux '.$o,'Autocuidado','autocuidado',null,null,$aux,$aux,'','col-3');
	$c[]=new cmp('activesparc','s','3',$d,$w.' aux '.$o,'Actividades de Esparcimiento','activesparc',null,null,$aux,$aux,'','col-3');
	$c[]=new cmp('infeducom','s','3',$d,$w.' aux '.$o,'Información, educación y Comunicación en salud','infeducom',null,null,$aux,$aux,'','col-4');

	/* $o='sigvit';
	$c[]=new cmp($o,'e',null,'SIGNOS VITALES',$w);
	$c[]=new cmp('momento','s',3,$d,$w.' aux '.$o,'Momento','momento',null,null,$aux,$aux,'','col-2');
	$c[]=new cmp('tas','n',3, $d,$w.' aux '.$o,'Tensión Sistolica Mín=40 - Máx=250','tas','rgxsisto','###',$aux,$aux,'','col-2');
	$c[]=new cmp('tad','n',3, $d,$w.' aux '.$o,'Tensión Diastolica Mín=40 - Máx=150','tad','rgxdiast','###',$aux,$aux,'','col-2');
	$c[]=new cmp('frecard','n',3, $d,$w.' aux '.$o,'Frecuencia Cardiaca Mín=60 - Máx=120','frecard',null,'##',$aux,$aux,'','col-2');
	$c[]=new cmp('satoxi','n',3, $d,$w.' aux '.$o,'saturación de Oxigeno Mín=60 - Máx=100','satoxi',null,'##',$aux,$aux,'','col-2'); */

	if($aux===true || $per=='ADM'){
		$rta .="<div class='encabezado'>TABLA DE MEDIDAS AUXILIAR</div>
	<div class='contenido' id='medidaux-lis' >".lis_medidaux()."</div></div>";
	}
	//ACTIVIDAD DE RESPIRO SE HABILITA PARA LOS PERFILES (SE HABILITARA PARA LOS SIGUIENTES PERFILES, LARREL, TOPREL, LEFREL, TSOREL)
	for ($i=0;$i<count($c);$i++) $rta.=$c[$i]->put();
	return $rta;
}

function get_sesiones(){
	// print_r($_POST);
	if($_REQUEST['id']==''){
		return "";
	}else{
		$id=divide($_REQUEST['id']);
		// print_r($_SESSION);
		// print_r($perfil=$info['responseResult'][0]['perfil']);
		$sql="SELECT concat(rel_tipo_doc,'_',rel_documento,'_',idsesion),`rel_validacion1`, `rel_validacion2`,`rel_validacion3`,
		`rel_validacion4`,`rel_validacion5`,autocuidado,activesparc,infeducom,'' as momento,' ' as tas,' ',' ' as frecard,' ' as satoxi
		FROM rel_sesion WHERE idsesion='{$id[0]}'";
		// echo $sql;
		$info=datos_mysql($sql);
		return json_encode($info['responseResult'][0]);
	} 
}

function get_sesiones_info(){
	if($_POST['id']=='0'){
		return "";
	}else{
		$id=divide($_POST['id']);
		$sql="SELECT rel_tipo_doc,rel_documento,rel_validacion1,rel_validacion2,rel_validacion3,rel_validacion4,estado
		FROM `rel_sesion` WHERE rel_tipo_doc='{$id[0]}' AND rel_documento='{$id[1]}'";
		$info=datos_mysql($sql);
    	// echo $sql."=>".$_POST['id'];
		return $info['responseResult'][0];
	} 
}

function focus_sesiones(){
	return 'sesiones';
}

function men_sesiones(){
 $rta=cap_menus('sesiones','pro');
 return $rta;
}
 
function cap_menus($a,$b='cap',$con='con') {
	$rta = "";
	$acc=rol($a);
	if ($a=='sesiones' && isset($acc['crear']) && $acc['crear']=='SI'){  
		$rta .= "<li class='icono $a grabar' title='Grabar' OnClick=\"grabar('$a',this);\"></li>"; //~ openModal();
  	}
  	$rta.= "<li class='icono $a actualizar' title='Actualizar' Onclick=\"mostrar('sesiones','pro',event,'','sesiones.php',7);\"></li>";
	return $rta;
}

function gra_sesiones(){
// print_r($_POST);
	$idrel=divide($_POST['id']);
	if(COUNT($idrel)== 1){ 
	$sql="UPDATE rel_sesion SET 
				rel_validacion5 = TRIM(upper('{$_POST['rel_validacion5']}')),
		`usu_update`=TRIM(UPPER('{$_SESSION['us_sds']}')),
		`fecha_update`=DATE_SUB(NOW(), INTERVAL 5 HOUR) 
		WHERE idsesion='$idrel[0]'"; 
	  //echo $x;
	//   echo $sql;
	} else {
		$sql="INSERT INTO rel_sesion VALUES (
			null,
			'$idrel[0]',
			'$idrel[1]',
			trim(upper('{$_POST['rel_validacion1']}')),
			trim(upper('{$_POST['rel_validacion2']}')),
			trim(upper('{$_POST['rel_validacion3']}')),
			trim(upper('{$_POST['rel_validacion4']}')),
			trim(upper('{$_POST['rel_validacion5']}')),
			trim(upper('{$_POST['autocuidado']}')),
			trim(upper('{$_POST['activesparc']}')),
			trim(upper('{$_POST['infeducom']}')),
			DATE_SUB(NOW(), INTERVAL 5 HOUR),
			{$_SESSION['us_sds']},
			NULL,
			NULL,
			'2')";
		// echo $sql;
	}

	$rta=dato_mysql($sql);
	//return "correctamente";
	return $rta; 
}

function opc_momento($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 58 and estado='A' ORDER BY 1",$id);
}
function opc_infeducom($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo =157  and estado='A' ORDER BY 1",$id);
}
function opc_activesparc($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 194 and estado='A' ORDER BY 1",$id);
}
function opc_autocuidado($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 103 and estado='A' ORDER BY 1",$id);
}
function opc_rel_sesiones($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 32 and estado='A' ORDER BY LPAD(idcatadeta,2,'0')",$id);
}
function opc_rel_validacion4($id=''){
	return opc_sql("SELECT `idcatadeta`,descripcion FROM `catadeta` WHERE idcatalogo = 301 and estado='A' ORDER BY 1",$id);
}
function bgcolor($a,$c,$f='c'){
	$rta="";
	return $rta;
}

function formato_dato($a,$b,$c,$d){
	$b=strtolower($b);
	$rta=$c[$d];
	// $rta=iconv('UTF-8','ISO-8859-1',$rta);
	// var_dump($a);
	// var_dump($c);
	if ($a=='session' && $b=='acciones'){
		$rta="<nav class='menu right'>";		
			$rta.="<li class='icono editar ' title='Editar Sesión' id='".$c['ACCIONES']."' Onclick=\"Color('session-lis');setTimeout(getData,300,'sesiones',event,this,['rel_validacion1','rel_validacion2','rel_validacion3','rel_validacion4'],'sesiones.php');setTimeout(auxSign,500,'rel_validacion3','aux');\"></li>";  //getData('plancon',event,this,'id');   act_lista(f,this);
		}
return $rta;
}
