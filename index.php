<?
// Projeto RGM
// 
//
//
//

//======= Includes
include_once "include/lang.php"; 	
include_once "include/config.php"; 	
include_once "include/funcoes.php"; 	
include_once "include/db.php"; 	
include_once "include/processamento.php"; 	


//======= Only for this page
	$MinhaURL 	   = $_SERVER['PHP_SELF'];
	$MinhaURL = NoIndexPHP($MinhaURL);	
	
	$ErrosNaPagina = "";
	function TryShowError($ErrorStr) {
		if( !Vazio($ErrorStr) ) {
			Linha ("<p class='showerror'><b>$ErrorStr</b></p>");		
		} 
	}
	

//======= Pre-process
	RedirectIfNotIsHTTPS(cDominioFullURLSSL . $MinhaURL); //Força HTTPS
	//session_start();
	$SemErro = FALSE;
	$SemErro = SecSessionStart("calendario",TRUE);
	if( !$SemErro ) {	$SemErro = SecSessionStart("calendario",FALSE);}	
	if( !$SemErro ) {	AddMsg("ErrSessStart",$ErrosNaPagina); session_start("calendario");}	

	$IsMobileBrowser = FALSE;	
	if( IsMobileBrowser() ) { $IsMobileBrowser = TRUE; }


	//Se não tiver configurado país, linguagem e tipo de calendário, configura e redireciona 
	// (geralmente, a primeira visita ao site)
//	if( !isset($_SESSION['Lang']) || !isset($_SESSION['Country']) || !isset($_SESSION['CalTipo']) ) {
	if( !isset($_SESSION['Lang']) || !isset($_SESSION['Country'])) {
	   $_SESSION['Lang']    = "pt";
	   $_SESSION['Country'] = "BR"; 
//	   $_SESSION['CalTipo'] = cTypeYear;
	}
	SetLanguage($_SESSION['Lang']);	
	//SetCalendarType($_SESSION['CalTipo']);					


//======= Pre-process
//	Pre-action

 //exit volta ao início da página
 if (filter_has_var(INPUT_POST,'exit')) { 	
	 ClearVars(); 	 
 	 RedirecionarPHP($MinhaURL);
 }
 elseif (filter_has_var(INPUT_POST,'country')) {
		$Pais = filter_input(INPUT_POST,'country',FILTER_SANITIZE_STRING);
		$Country = CountryFilter($Pais);
		$Lang    = CountryToLanguage($Country);		
	   $_SESSION['Lang']    = $Lang;
	   $_SESSION['Country'] = $Country; 
		RedirecionarPHP($MinhaURL);
 }
 elseif( (filter_has_var(INPUT_GET,'calendar')) ) {
 	$CalTemp = filter_input(INPUT_GET,'calendar',FILTER_SANITIZE_STRING);
	switch( $CalTemp ) {
			case "recent":
					$CalTemp = GetLastcalendars(LastCalendars);
					if( $CalTemp["Total"] > 0 ) {
						 $_SESSION['CalendariosRecentes'] = $CalTemp; 
					} 
			break;
	}  

 }		
 elseif (filter_has_var(INPUT_GET,'id')) {
 	   //Prevent first step
 	   ClearVars(); 
 	   
	 	$CalendarioID = filter_input(INPUT_GET,'id',FILTER_SANITIZE_STRING);
	 	$CalendarioTemp = SearchCalendarByID($CalendarioID); 
		if	( $CalendarioTemp !== FALSE ){
	 				$CalendarKeys = $CalendarioTemp[$cFdCalkeys];
					switch( $CalendarioTemp[$cFdCalType] ) {
							case "y":
									$CalendarioTipoEscolhido = cTypeYear;
							break;
							case "h":
									$CalendarioTipoEscolhido = cTypeHalf;
							break;
							case "m":
									$CalendarioTipoEscolhido = cTypeMonth;
							break;
					}  
					
					//Configura loclidade e linguagem
					$Country = CountryFilter($CalendarioTemp[$cFdCalCountry]);
					$Lang    = CountryToLanguage($Country);		
				   SetLanguage($Lang); 

			 		$CalendarioPronto = FormToCalendario($CalendarioTipoEscolhido,$CalendarKeys,$Country);
					if	( $CalendarioPronto !== FALSE ){
							$_SESSION['CalendarioPronto'] = $CalendarioPronto;		
							$_SESSION['CalendarioID']     = $CalendarioID; //Prevent errors | Evita recadastro		
					}else { 
							AddMsg("ErrNoImageKey",$ErrosNaPagina);	
					}
		}else { 
				AddMsg("ErrNoCalendarFound",$ErrosNaPagina);	
		}
 }


 elseif (filter_has_var(INPUT_POST,'submitformids')) {
 		$CalendarioPronto = FormToCalendario($_SESSION['CalendarioTipoEscolhido'],$_POST['ID'],$_SESSION['Country']);
		if	( $CalendarioPronto !== FALSE ){
				$_SESSION['CalendarioPronto'] = $CalendarioPronto;		
		}else { 
				AddMsg("ErrNoImageKey",$ErrosNaPagina);	
		}
 }
 elseif (filter_has_var(INPUT_POST,'tipo')) {
   $CalendarioTipoEscolhido = filter_input(INPUT_POST,'tipo',FILTER_SANITIZE_STRING);
	  switch( $CalendarioTipoEscolhido ) {
			case cTypeYear:
			case cTypeHalf:
			case cTypeMonth:
				$_SESSION['CalendarioTipoEscolhido'] = $CalendarioTipoEscolhido; //Registra na seção (isso é muito importante)
				//Note que a diferença é que CalendarioTipoEscolhido é usado nas etapas seguintes
			break;  
	  }  
	//se não combinar, apenas volta para página inicial 
 	Redirecionar($MinhaURL);
 }




?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
   <?
       Linha("	<title>".GetMsg('SiteTitle')." </title>");     
   ?>
	<link href="css/geral.css" rel="stylesheet" type="text/css"/>	
	<link rel="shortcut icon" href="imagens/favicon.png" type="image/png"/>
	<script src="include/funcoes.js"></script>
</head>
<body>
	<?	
		$AddStyleToPrint = "";
		if( isset( $_SESSION['CalendarioPronto'] )) { $AddStyleToPrint = "meio-print"; }

		//**** Action!
		DrawHeader($MinhaURL);
		Linha( "<div class='meio $AddStyleToPrint'>" );
			if( isset( $_SESSION['CalendariosRecentes'] ) ) {
   				Linha("<h2>".GetMsg('MostRecent')."</h2>");									
					DesenharFormSair($MinhaURL);		
					Linha( "<hr>" );
					$CalRec = $_SESSION['CalendariosRecentes'];
					$Total = $CalRec['Total'];
					for( $Cont=0;$Cont < $Total; $Cont++ ) {
							$Item = $CalRec[$Cont]; 		
							$CalendarLink = GetCalendarURL($MinhaURL,ToBase36($Item[$cFdCalID]));
							$Img = GetDirectURL320($Item[$cFdCalkeys][1]);
							$ImgInfo = $Item[$cFdCalType] .": ". CountryFull($Item[$cFdCalCountry]); 			
							Linha("<p class='item-alinhado calendarios-recentes'><a href='$CalendarLink'> <img src='$Img' width='128'> <br>" . $ImgInfo ."</a></p>");
					}
			
			}		
			elseif( isset( $_SESSION['CalendarioPronto'] )) {
					if( !isset( $_SESSION['CalendarioID'] )) {
						$_SESSION['CalendarioID'] = CadastrarCalendario($_SESSION['CalendarioPronto']);
					}			
					//$CalendarLink = cDominioFullURLSSL . $MinhaURL."?id=".$_SESSION['CalendarioID'];
					$CalendarLink = GetCalendarURL($MinhaURL,$_SESSION['CalendarioID']);					
								
					Linha("	<p class='item-alinhado'>");
					Linha("		&nbsp;&nbsp;".GetMsg('ShareMsg')." <small>LINK</small> <input type='text' size='30' onclick='this.focus();this.select()' readonly='true' value='".$CalendarLink."' />");
					Linha("	</p>");

					Linha("	<div class='item-alinhado'>");
						TwitterShare($CalendarLink);
					Linha("	</div>");

					Linha("	<div class='item-alinhado'>");
						FBShare($CalendarLink);
					Linha("	</div>");

					DesenharFormSair($MinhaURL);

					Linha("	<hr>");

				   if( FromBase36($_SESSION['CalendarioID']) > 0 ) {
						Linha("	<p class='alinhar-centro'>");
						Linha("		<img src='imagens/icon-print.png' class='flutuar-esquerda' ><input type='submit' value='".GetMsg('BtnPrint')."'  onclick=\"javascript:PrintDiv('print-area');\" />");
						Linha("		<br><small>".GetMsg('PrintTip')."</small>");
						Linha("	</p> ");
						GerarCalendario($_SESSION['CalendarioPronto']);
					   SetLanguage($Lang); 
				   }
			}		
			elseif( isset($_SESSION['CalendarioTipoEscolhido']) ) {
				DesenharFormSair($MinhaURL);		
				DesenharFormIDs($MinhaURL,$_SESSION['CalendarioTipoEscolhido']);
			}
			//Else, is the start
			else {
				DesenharForm($MinhaURL);
			}		
	
	    TryShowError($ErrosNaPagina);
	 Linha( "</div>" );
    Footer();
   ?>
</body>
</html>