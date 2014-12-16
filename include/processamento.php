<?
include_once "phpqrcode.php";

define("cTypeYear","year");
define("cTypeHalf","half");
define("cTypeMonth","month");
define("cSiteRGM","<a href='https://projetorgm.com.br/'><img class='alinhar-vertical' src='imagens/RGM-logo.png' width='32px' /> projetorgm.com.br</a>");
/*
$CalendarType = cTypeYear;

function SetCalendarType($Type) {
  global $CalendarType;
  $CalendarType = $Type;
}
*/

function GetMapillaryImgURL($ID) {
	return "http://www.mapillary.com/map/im/" . $ID; 
}

function GetDirectURL320($ID) {
	return "https://d1cuyjsrcm0gby.cloudfront.net/".$ID."/thumb-320.jpg"; 
}
function GetDirectURL1024($ID) {
	return "https://d1cuyjsrcm0gby.cloudfront.net/".$ID."/thumb-1024.jpg"; 
}
function GetDirectURL2048($ID) {
	return "https://d1cuyjsrcm0gby.cloudfront.net/".$ID."/thumb-1024.jpg"; 
}

function GetCalTableURLPrefix($CalendarType,$Country) {
	return "imagens/".$Country."/".$CalendarType."/";
}


function FormToCalendario($CalendarType,$IDPost,$Pais) {
    function UnitProcImage($ID) {
		$ImagemIDTemp = trim(filter_var($ID,FILTER_SANITIZE_STRING));
		return $ImagemIDTemp;		
    }
	  
	  $TemImagem = FALSE;
	  switch( $CalendarType ) {
			case cTypeYear:
				$ImID = UnitProcImage($IDPost[1]);
				if( !Vazio($ImID) ) { $TemImagem = TRUE; }
			break;  
			case cTypeHalf:
			     $Loop = 2;
				  for( $Cont = 1; $Cont <= $Loop; $Cont++ ) {
				  	   $ImID[$Cont] = UnitProcImage($IDPost[$Cont]);
						if( !Vazio($ImID[$Cont]) ) { 
							$TemImagem = TRUE; 
							$ImBK = $ImID[$Cont]; 
						}
				  }
			break;  
			case cTypeMonth:
			     $Loop = 12;
				  for( $Cont = 1; $Cont <= $Loop; $Cont++ ) {
				  	   $ImID[$Cont] = UnitProcImage($IDPost[$Cont]);
						if( !Vazio($ImID[$Cont]) ) { 
							$TemImagem = TRUE; 
							$ImBK = $ImID[$Cont]; 
						}
				  }
			break;
	  }  
	  
	  //Tem imagem e não é ano, verifica imagens vazias 
	  if( $TemImagem && isset($Loop) ) {
		  for( $Cont = 1; $Cont <= $Loop; $Cont++ ) {
				if( Vazio($ImID[$Cont]) ) { 
					$ImID[$Cont] = $ImBK; 
				}
		  }
	  }
 

   if( $TemImagem ) {
		return PremontarCalendario($ImID,$CalendarType,$Pais);
   }
	else { return $TemImagem; }
}

function PremontarCalendario($ID,$CalendarType,$Pais){
	
  $OK = TRUE;
  $CalendarioTemp = array();
  function CalendarUnit($ID,$Tipo,$Pais,$Indice) {
		$QRCodeW = 6;
		$URL = GetMapillaryImgURL($ID);
	   $TempDir = "temp/mapillary/";

	   if(TestarDiretorio($TempDir)){
		   $FileName =  $TempDir . md5($ID) . '.png';
		   if( !file_exists($FileName) ) {
				QRcode::png($URL,$FileName, "M", $QRCodeW, 2);
			}
		}  	
  	
		$CalendarUnit['Tabela']    = GetCalTableURLPrefix($Tipo,$Pais) . $Indice . ".svg";	
		$CalendarUnit['Imagem']    = GetDirectURL1024($ID);
		$CalendarUnit['Mapillary'] = GetMapillaryImgURL($ID);
		$CalendarUnit['ID']        = $ID;
		$CalendarUnit['QR']        = $FileName;
		return $CalendarUnit;
  }
  //---------------------------------------------------------------------	

  switch( $CalendarType ) {
		case cTypeYear:
		   $CalendarioTemp = CalendarUnit($ID,$CalendarType,$Pais,1);
		break;  
		case cTypeHalf:
		  for( $Cont = 1; $Cont <= 2; $Cont++ ) {
		  	   $CalendarioTemp[$Cont] = CalendarUnit($ID[$Cont],$CalendarType,$Pais,$Cont);
		  }
		break;  
		case cTypeMonth:
		  for( $Cont = 1; $Cont <= 12; $Cont++ ) {
		  	   $CalendarioTemp[$Cont] = CalendarUnit($ID[$Cont],$CalendarType,$Pais,$Cont);
		  }
		break;
		default:
		  $OK = FALSE;
		break;  
  }
  //Add more information  
  $CalendarioTemp["Country"] = $Pais; //Store country into calendar array
  $CalendarioTemp["Type"] = $CalendarType; //Store type into calendar array
  
  if( $OK ) {
	  return $CalendarioTemp;  
  }else{
  	 return $OK; //No caso, return = false
  }
}


function PrintCalendario($Calendario) {
	$Tabela        = $Calendario['Tabela'];
	$Imagem        = $Calendario['Imagem'];
	$QR            = $Calendario['QR'];
	$MapillaryAttr = $Calendario['Mapillary'];
	$CalID         = $_SESSION['CalendarioID']; 
	
	Linha("	<div class='page-area'>");
	Linha("			<div class='calendario-imagem' >");
	Linha("					<img src='$Imagem' >");
	Linha("			</div>");
	Linha("			<div class='calendario-titulo'>");
	Linha("					<div class='calendario-titulo-qr'>");
	Linha("							<img src='$QR'>");
	Linha("					</div>");
	Linha("					<div class='calendario-titulo-link'> <a href='$MapillaryAttr'>$MapillaryAttr <img src='imagens/link.svg' /></a> </div>");
	Linha("					<div class='arredondar calendario-titulo-mapillary'>");	
	Linha("							<img class='alinhar-vertical' src='imagens/mapillary-ccby.png' /><br><img src='imagens/mapillary-logo.jpg'>");
	Linha("					</div>");
	Linha("			</div>");
	Linha("			<div class='calendario-tabela'>");
	Linha("					<img src='$Tabela'> ");
	Linha("			</div>");
	Linha("			<p class='creditos'><small>".GetMsg('CalIDInfo')." <b class='id-featured'>$CalID</b> | ".GetMsg('Creditos')."</small></p>");
	Linha("	</div>");
	Linha("");
}

function CadastrarCalendario($Calendario) {
  $CalendarType = $Calendario['Type'];

  function GetKey($Calendario) {
  		return $Calendario['ID'];
  }     
  function Separator($Str,$Atual,$Total) {
 		//Se vazio,não precisa de separador antes!
 		//Se não vazio, precisa - a menos que que seja o último 
 		$Add = "";
 		if( !Vazio($Str) ) {
	 		if( $Atual <= $Total ) { 
	 			$Add = cMapillaryKeySeparator; 
	 		}
 		}
 		return $Add;
  }
  //-------------------------------------------

  $CalKeys = "";
  switch( $CalendarType ) {
		case cTypeYear:
		   $CalKeys = GetKey($Calendario);
		case cTypeHalf:
		  $Total = 2;
		  for( $Cont = 1; $Cont <= $Total; $Cont++ ) {
		  		$Add = Separator($CalKeys,$Cont,$Total);
		  		$CalKeys = $CalKeys .$Add. GetKey($Calendario[$Cont]);
		  }
		break;  
		case cTypeMonth:
		  $Total = 12;
		  for( $Cont = 1; $Cont <= $Total; $Cont++ ) {
		  		$Add = Separator($CalKeys,$Cont,$Total);
		  		if( $Cont > 1 ) { $Add = cMapillaryKeySeparator; }
		  		$CalKeys = $CalKeys .$Add. GetKey($Calendario[$Cont]);
		  }
		break;
  }  

  $ID = RegisterCalendar($CalendarType[0],$Calendario["Country"],$CalKeys);
  return $ID;
}


function GerarCalendario($Calendario) {
	Linha("		<div id='print-area'>");

	  switch( $Calendario["Type"] ) {
			case cTypeYear:
			   PrintCalendario($Calendario);
			break;  
			case cTypeHalf:
			  for( $Cont = 1; $Cont <= 2; $Cont++ ) {
			  	PrintCalendario($Calendario[$Cont]);
			  }
			break;  
			case cTypeMonth:
			  for( $Cont = 1; $Cont <= 12; $Cont++ ) {
			  	PrintCalendario($Calendario[$Cont]);
			  }
			break;
	  }  
	Linha("	</div>");
}


function DesenharForm($Action) {
   Linha("<form id='formtype' action='$Action' method='post' class='alinhar-centro'>");									
   Linha("   <p>".GetMsg('Part1Desc')."</p>");
   Linha("      <div class='item-alinhado langescolha' onclick=\"CheckElement('caltipoy',true);document.getElementById('formtype').submit();\" ><img src='imagens/cal-y.svg'><br><input id='caltipoy' type='radio' name='tipo' value='".cTypeYear."' checked='true'>".GetMsg('Type1')."</div>");									
   Linha("      <div class='item-alinhado langescolha' onclick=\"CheckElement('caltipos',true);document.getElementById('formtype').submit();\" ><img src='imagens/cal-s.svg'><br> <input id='caltipos' type='radio' name='tipo' value='".cTypeHalf."'>".GetMsg('Type2')."</div>");						
   Linha("      <div class='item-alinhado langescolha' onclick=\"CheckElement('caltipom',true);document.getElementById('formtype').submit();\" ><img src='imagens/cal-m.svg'><br> <input id='caltipom' type='radio' name='tipo' value='".cTypeMonth."'>".GetMsg('Type3')."</div>");
	if( DetectIEBrowser() ) {      								
  		Linha("<p class='showerror'><i>".GetMsg('Part1Browser')."</i></p>");
  	}
   Linha("<hr>");									
   Linha("<p><a href='".$Action."?calendar=recent'>".GetMsg('MostRecent')."</a></p>");									
   Linha("</form>");									
   Linha(" ");									
}


function DesenharFormIDs($Action,$Type) {
   Linha("		<form id='formids' action='$Action' method='post' class='alinhar-centro'>");									
   Linha("			<p>".GetMsg('Part2Desc')."</p>");
   Linha("			<p>".GetMsg('Part2Tip')."</p>");

	  switch( $Type ) {
			case cTypeYear:
				$Loop = 1;
			break;  
			case cTypeHalf:
				$Loop = 2;
			break;  
			case cTypeMonth:
				$Loop = 12;
			break;
	  }  

 //  Linha("   <p>");									
	for( $Cont=1;$Cont <= $Loop; $Cont ++  ) {
	   Linha("			<fieldset class='alinhar-esquerda'><legend><b>".GetMsg('Part2Page')." $Cont</b></legend>");									
	   Linha("					<b>".GetMsg('Part2ID')."</b>");									
   	Linha("					<input type='text' name='ID[$Cont]' />");									
	   Linha("			</fieldset>");			
	}						
//	Linha("      </p>");									

   Linha("			<input type='hidden' name='submitformids' value='go' />");									
   Linha("			<p class='alinhar-direita'><input type='submit' value='".GetMsg('Part2Next')."' /></p>");									
   Linha("		</form>");									
   Linha(" ");									
}



function DesenharFormSair($Action) {
   Linha("		<form id='formsair' action='$Action' method='post' class='item-alinhado itempadl alinhar-direita'>");									
   Linha("				<input type='hidden' name='exit' value='exit' />");									
   Linha("				<input type='submit' value='".GetMsg('BtnExit')."' />");									
   Linha("		</form>");									
   Linha(" ");									
}


function DrawHeader($MinhaURL) {
	Linha("<div class='header alinhar-direita'>");
	Linha( "		<h1 class='item-alinhado alinhar-centro' >".GetMsg('IntroTitle')."</h1>" );
   Linha("		<p class='item-alinhado itempadl' >".cSiteRGM."</p>");
   Linha("		<form id='formlang' class='item-alinhado itempadl' action='$MinhaURL' method='post'>");
   Linha("					<p class='item-alinhado'><img src='imagens/country-translate.png' alt='country...'/></p>");
   Linha("					<div class='item-alinhado langescolha' onclick=\"CheckElement('country-br',true);document.getElementById('formlang').submit();\" ><img src='imagens/country-br.png' title='Brasil, Português' ><br><input hidden='true' id='country-br' type='radio' name='country' value='BR'></div>");									
   Linha("     			<div class='item-alinhado langescolha' onclick=\"CheckElement('country-wd', true);document.getElementById('formlang').submit();\" ><img src='imagens/country-wd.png'   title='World, English'><br>   <input hidden='true' id='country-wd' type='radio' name='country' value='WD'></div>");
   Linha("     			<div class='item-alinhado langescolha' onclick=\"CheckElement('country-es', true);document.getElementById('formlang').submit();\" ><img src='imagens/country-es.png'   title='España, Español'><br>   <input hidden='true' id='country-es' type='radio' name='country' value='ES'></div>");
   Linha("		</form>");									
   Linha(" ");									

	Linha("</div>");
   Linha(" ");									
}


function Footer() {
   Linha("	<div class='footer'>");
   Linha("		<p class='alinhar-centro'> " . cSiteRGM . " | <img class='alinhar-vertical' src='imagens/git.png' /> " . GetMsg('GetSource')."</p>");
   Linha("		<p class='alinhar-centro'> <img class='alinhar-vertical' src='imagens/cc.png' /> ".GetMsg('CreditosMapillary')."</p>");
   Linha("	</div>");
   Linha(" ");									
}


function GetCalendarURL($MinhaURL,$ID) {
	return cDominioFullURLSSL . $MinhaURL."?id=".$ID;	
}

function ClearVars() {
 	 if( isset($_SESSION['CalendarioTipoEscolhido']) ) { unset($_SESSION['CalendarioTipoEscolhido']); }
 	 if( isset($_SESSION['CalendarioPronto']) ) { unset($_SESSION['CalendarioPronto']); }
 	 if( isset($_SESSION['CalendarioID']) )     { unset($_SESSION['CalendarioID']); }
 	 if( isset($_SESSION['CalendariosRecentes']) )   { unset($_SESSION['CalendariosRecentes']); }
}


function TwitterShare($URL) {
	Linha("		");
	Linha("		<a class='twitter-share-button' href='$URL'");
	Linha("		  	data-related='twitterdev'");
//	Linha("		  	data-size='large'");
	Linha("		  	data-count='horizontal'>");
	Linha("		Share");
	Linha("		</a>");
	Linha("		<script type='text/javascript'>");
	Linha("		window.twttr=(function(d,s,id){var t,js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id)){return}js=d.createElement(s);js.id=id;js.src='https://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);return window.twttr||(t={_e:[],ready:function(f){t._e.push(f)}})}(document,'script','twitter-wjs'));");
	Linha("		</script>");
}


function FBShare($URL) {
	Linha("		<div id='fb-root'></div>");
	Linha("		<script>(function(d, s, id) {");
	Linha("		  var js, fjs = d.getElementsByTagName(s)[0];");
	Linha("		  if (d.getElementById(id)) return;");
	Linha("		  js = d.createElement(s); js.id = id;");
	Linha("		  js.src = '//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.0';");
	Linha("		  fjs.parentNode.insertBefore(js, fjs);");
	Linha("		}(document, 'script', 'facebook-jssdk'));</script>");
	Linha("<div class='fb-share-button' data-href='$URL' data-layout='button_count'></div>");
}


?>