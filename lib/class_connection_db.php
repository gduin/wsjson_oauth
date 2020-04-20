<?php
class connection_db
{

	var $as_hostname ="";
	var $as_login    ="";
	var $as_password ="";
	var $as_database ="";
	var $as_gestor   ="";

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	function __construct()
	{
		require_once ('../vendor/adodb/adodb-php/adodb.inc.php');

		$this->as_hostname ="";
		$this->as_login    ="";
		$this->as_password ="";
		$this->as_database ="";
		$this->as_gestor   ="";
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	public function uf_connect_db() 
	{ 
		$conec= ADONewConnection($this->as_gestor);
		
		syslog(LOG_INFO, " Host ".$this->as_hostname." login ". $this->as_login." password ". $this->as_password." database ".$this->as_database);
		$conec->Connect($this->as_hostname, $this->as_login, $this->as_password,$this->as_database);
		
		$conec->SetFetchMode(ADODB_FETCH_ASSOC);
		if(!$conec->_connectionID)
		{			
			$data[] = array('Error'=>'No pudo conectar al servidor de base de datos, contacte al administrador del sistema');
			syslog(LOG_INFO,' No pudo conectar al servidor de base de datos, contacte al administrador del sistema ');				
			$conec=false;
		}
		return $conec;
	}
}
?>
