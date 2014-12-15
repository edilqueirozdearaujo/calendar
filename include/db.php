<?

function DBServerConnect() {
	$Resource =  mysql_connect(cDBHost, cDBUser,cDBPass);
	return $Resource;  
}

function DBIsConnected($Resource) {
	$Result = TRUE;
	if( $Resource === FALSE  ) {
     	$Result = FALSE;	
	}
	return $Result;
}


function DBSelect($Db) {
	return mysql_select_db($Db);
}
	
function DBServerDisconnect($Link) {
	return mysql_close($Link);
}


//Retorna números de resultados de uma consulta. Retorna 0 em caso de não encontrar ou erro
//Count of results 
function MySQLResults($ExeSQL) {
	$Resultados = 0;
	if( $ExeSQL !== FALSE ) {
		$Resultados = mysql_num_rows($ExeSQL);
	}
	return $Resultados;
}


//Register calendar and return an Base36 ID
function RegisterCalendar($Tipo,$Country,$MapillaryKeys) {
	global 	$cFdCalID,$cFdCalDate,$cFdCalTime,$cFdCalkeys,$cFdCalType, $cFdCalCountry;
	
	$HoraAtual  = date("H:i:s");
	$DataDeHoje = date("Y-m-d");
	$ID = 0;

	function GetNextCalendarID(){
		$Result = mysql_query("SHOW TABLE STATUS LIKE '".cTbCalendars."'");
		$Row = mysql_fetch_array($Result);
		$NextID = $Row['Auto_increment'];
		return $NextID; 
	}

	//--------------------------------------------------------------------
	$Res = DBServerConnect();
	if( DBIsConnected($Res)) {
		if (DBSelect(cDBName)){
		   $ID = GetNextCalendarID();
			
			$SQL = "INSERT INTO ".cTbCalendars." ( $cFdCalDate, $cFdCalTime, $cFdCalkeys, $cFdCalType, $cFdCalCountry ) "
			     . "VALUES ( '$DataDeHoje', '$HoraAtual', '$MapillaryKeys', '$Tipo', '$Country' );";

			$ExeSQL = mysql_query($SQL) or die (mysql_error());
			if( $ExeSQL === FALSE ) {
				$ID = 0; //reset ID
			}
		}
		DBServerDisconnect($Res);
	}
	return ToBase36($ID);
}


//Search calendar and return as array
function SearchCalendarByID($ID) {
	global 	$cFdCalID,$cFdCalDate,$cFdCalTime,$cFdCalkeys,$cFdCalType, $cFdCalCountry;
	//--------------------------------------------------------------------
	$Resultado = FALSE; 
	$IDInt = FromBase36($ID);
	$Res = DBServerConnect();
	if( DBIsConnected($Res)) {
		if (DBSelect(cDBName)){
			$SQL = "SELECT * FROM ".cTbCalendars." WHERE $cFdCalID = $IDInt LIMIT 1;";

			$ExeSQL = mysql_query($SQL);
			if( MySQLResults($ExeSQL) > 0 ) {
				 $Registro = mysql_fetch_array($ExeSQL);
				
				 //Convert simple string to array of Mapillary keys				 
				 $KeysTemp = explode(cMapillaryKeySeparator, $Registro[$cFdCalkeys]);
				 $Total = count($KeysTemp);
				 for( $Cont = 1; $Cont <= $Total; $Cont++  ) {
				 	$MapillaryKeys[$Cont] = $KeysTemp[$Cont-1];
				 }
				 
			    $Resultado[$cFdCalDate] = $Registro[$cFdCalDate];  
			    $Resultado[$cFdCalTime] = $Registro[$cFdCalTime];  
			    $Resultado[$cFdCalkeys] = $MapillaryKeys;  
			    $Resultado[$cFdCalType] = $Registro[$cFdCalType];
			    $Resultado[$cFdCalCountry] = $Registro[$cFdCalCountry];  
			}
		}
		DBServerDisconnect($Res);
	}
	return $Resultado;
}

//Return an array of last n calendars
function GetLastcalendars($Limit) {
	global 	$cFdCalID,$cFdCalDate,$cFdCalTime,$cFdCalkeys,$cFdCalType, $cFdCalCountry;

	function CalUnit($Registro) {
		 global 	$cFdCalID,$cFdCalDate,$cFdCalTime,$cFdCalkeys,$cFdCalType, $cFdCalCountry;
		 //Convert simple string to array of Mapillary keys				 
		 $KeysTemp = explode(cMapillaryKeySeparator, $Registro[$cFdCalkeys]);
		 $Total = count($KeysTemp);
		 for( $Cont = 1; $Cont <= $Total; $Cont++  ) {
		 	$MapillaryKeys[$Cont] = $KeysTemp[$Cont-1];
		 }
		 
	    $Resultado[$cFdCalID]   = $Registro[$cFdCalID];  
	    $Resultado[$cFdCalDate] = $Registro[$cFdCalDate];  
	    $Resultado[$cFdCalTime] = $Registro[$cFdCalTime];  
	    $Resultado[$cFdCalkeys] = $MapillaryKeys;  
	    $Resultado[$cFdCalType] = $Registro[$cFdCalType];
	    $Resultado[$cFdCalCountry] = $Registro[$cFdCalCountry];
	    return $Resultado;
	}
	//--------------------------------------------------------------------
	$Resultado = FALSE; 
	$CalTemp["Total"] = 0; 
		
	$IDInt = FromBase36($ID);
	$Res = DBServerConnect();
	if( DBIsConnected($Res)) {
		if (DBSelect(cDBName)){
			$SQL = "SELECT * FROM ".cTbCalendars." ORDER BY $cFdCalDate DESC  LIMIT $Limit ;";

			$ExeSQL = mysql_query($SQL);
			$Total = MySQLResults($ExeSQL);
			$CalTemp["Total"] = $Total; 
			if( $Total > 0 ) {
				for( $Cont=0;$Cont < $Total; $Cont++ ) {
					 $Registro = mysql_fetch_array($ExeSQL);
					 $CalTemp[$Cont] = CalUnit($Registro);
				}   
			}
		}
		DBServerDisconnect($Res);
	}
	return $CalTemp;
}


?>