<?
// Projeto RGM

	//--------------------------------------------------------
	//General
	define("cTituloSite","Projeto RGM");
	define("cDominio","projetorgm.com.br");
	define("cDominioFullURL","http://www.".cDominio);
	define("cDominioFullURLSSL","https://".cDominio);

	//--------------------------------------------------------
	//Database
	define("cDBHost",""); //hostname
	define("cDBName",""); //database
	define("cDBUser",""); //user
	define("cDBPass",""); //password

	$TbPrefix = "cal_";

	define("cTbCalendars",$TbPrefix."calendars");
	define("cTbStats",$TbPrefix."stats");

	//Fields from Calendars table
	$cFdCalID        = "ID";
	$cFdCalDate      = "Date";
	$cFdCalTime      = "Time";
	$cFdCalkeys      = "SourceKeys";
	$cFdCalType      = "Type";
	$cFdCalCountry   = "Country";

	$cFdCalStatsID     = "AutoID";
	$cFdCalStatsDate   = "Date";
	$cFdCalStatsTime   = "Time";
	$cFdCalStatsCal    = "Calendar";
	
	define("cMapillaryKeySeparator",",");
	define("LastCalendars",20);
	



?>