<?php
/***********************************************************************
 ***********************************************************************
 ***********************************************************************
 *	INFORMACION REVISION SVN
 *	Proyecto	:	Proyecto Software Libre Sigesp
 *	Archivo		:	$Id: ws_rest_server_cfg_sql.php 1945 2015-05-20 16:56:51Z  $
 *	Fecha		:	$Date: 2015-05-20 12:26:51 -0430 (mié 20 de may de 2015) $
 *	Revision	:	$Rev: 1945 $
 *	Mantenedor	:	Ofimatica de Venezuela, c.a.
 *	Web			:	www.ofimatica.com.ve
 *	E-mail		:	info@ofimatica.com.ve
 *	Telf.		:	+58 0251 2620940
 ***********************************************************************
 ***********************************************************************
 **********************************************************************/
?>
<?php
/**
* 
*/
class ws_rest_api
{
	var $oauth_server;

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	function __construct()
	{
		require_once('../lib/oauth_server.php');

		$this->oauth_server = new oauth_server();
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	function API()
	{
		$request = OAuth2\Request::createFromGlobals();
		$response = new OAuth2\Response();
		$headers = apache_request_headers();
		foreach ($headers as $header => $value) 
		{
			syslog(LOG_INFO, "Server handleTokenRequest header:value ". $header.":".  $value);
			if($header=="Authorization")
			{
				$code=substr($value, strlen('Bearer '));
				syslog(LOG_INFO, "Server handleTokenRequest code ". $code);
				$_REQUEST['code']=$code;
			}
			else if($header=="grant_type")
				$request->request['grant_type']=$value;
		}
		syslog(LOG_INFO, "Headers ".json_encode($headers));
		if(isset($headers['php_auth_user']))
		{
			$_REQUEST["client_id"]=$request->request['client_id']=$headers['php_auth_user'];
		}
		if(isset($headers['php_auth_pw']))			$request->request['client_secret']=$headers['php_auth_pw'];
		if(isset($headers['username']))			$_REQUEST['username']=$headers['username'];
		if(isset($headers['content_type']))			$_SERVER['CONTENT_TYPE']=$headers['content_type'];
		syslog(LOG_INFO, "REQUEST ".json_encode($request));
		
		if (!$token = $this->oauth_server->srv->verifyResourceRequest($request, $response))
		{
			$response->send();
			return json_encode(array("lb_valido"=>"false", "message"=>"El Token Expiro!!!"));
		}
		
		$method = $_SERVER['REQUEST_METHOD'];
		$permissions=$this->oauth_server->storage->checkClientPermissions($_REQUEST["client_id"], $_REQUEST["username"], strtolower($method));
		syslog(LOG_INFO, "Permissions ".($permissions!=true));
		if($permissions!=true)
		{
			syslog(LOG_INFO, "Debe retornar el usuario no tiene permisos");
			print json_encode(array("lb_valido"=>"false", "message"=>"El usuario no tiene permisos para la operacion ".$method));
			return false;
		}		
		$data   = $_GET;
		syslog(LOG_INFO, "Method ".$_SERVER["REQUEST_METHOD"]);
		syslog(LOG_INFO, "REQUEST ".json_encode($_REQUEST));
		syslog(LOG_INFO, "QUERY_STRING ".json_encode($_SERVER["QUERY_STRING"]));
		$class  = $data["class"];
		$funct  = $data["funct"];
		if($data["data"]!='all')
			$param = json_decode($data["data"], true);
		else
			$param = $data["data"];
		if($class=='')
		{
			die();
		}
		if($funct=='')
			die();
		if($param=='')
			die();
		if($method!='GET')
		{
			$file_in = file_get_contents("php://input");
			if($file_in===false)
			{
				$file_in = "";
				$param   = array();
			}
			else
			{
				$json_data = json_decode($file_in, true);
				syslog(LOG_INFO, "No es GET. Param Type ".gettype($json_data));
				syslog(LOG_INFO, "No es GET. Param  ".$json_data);
				syslog(LOG_INFO, "No es GET. Param  data ".$json_data["data"]);
				syslog(LOG_INFO, "No es GET. Param Type ".gettype(json_decode($json_data)));
				syslog(LOG_INFO, "No es GET. Param ".json_decode($json_data, true));
				syslog(LOG_INFO, "No es GET. Param ".json_encode(json_decode($json_data, true)));
				$param     = json_decode($json_data["data"], true);
			}
		}
		switch($method)
		{
			case 'POST':
				syslog(LOG_INFO, json_encode($_POST));
				syslog(LOG_INFO, "Arreglo Data POST ".json_encode($data));
				syslog(LOG_INFO, "Post Data ".$json_data);
				syslog(LOG_INFO, "Param ".json_encode($param));
				print $this->perform($class, $funct, $param);
				break;
			case 'PUT':
				syslog(LOG_INFO, json_encode($_POST));
				syslog(LOG_INFO, "Arreglo Data PUT ".json_encode($data));
				syslog(LOG_INFO, "Post Data ".$json_data);
				print $this->perform($class, $funct, $param);
				break;
			case 'DELETE':
				syslog(LOG_INFO, "Arreglo Data DELETE ".json_encode($data));
				
				syslog(LOG_INFO, "Post Data ".$json_data);
				print $this->perform($class, $funct, $param);
				break;
			case 'GET':
				syslog(LOG_INFO, "Arreglo Data VIEW ".json_encode($data));
				print $this->perform($class, $funct, $param);
				break;
			default:
				header('HTTP/1.1 405 Method not allowed');
				header('Allow: GET, POST, PUT, DELETE');
			break;
		}
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	protected function parseGetParameter($get,$name,$characters) 
	{
		$value = isset($get[$name])?$get[$name]:false;
		return $characters?preg_replace("/[^$characters]/",'',$value):$value;
	}

	protected function parseGetParameterArray($get,$name,$characters) 
	{
		$values = isset($get[$name])?$get[$name]:false;
		if (!is_array($values)) $values = array($values);
		if ($characters) {
			foreach ($values as &$value) {
				$value = preg_replace("/[^$characters]/",'',$value);
			}
		}
		return $values;
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	function __autoload($class)
	{

		$fname = '../models/' . $class . '.php';
		syslog(LOG_INFO, $fname);
		if (file_exists($fname))
		{
			syslog(LOG_INFO, "Existe el archivo de la clase");
			require_once $fname;
			return new $class();
		}
		else
		{
			return false;
		}
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	function perform($class, $funct, $param)
	{
		$lb_valid   = false;
		$res        = "";
		$clInstance = $this->__autoload($class);
		if ($clInstance)
		{
			if(is_array($param))
				foreach ($param as $key => $value)
				{
					syslog(LOG_INFO, "Key ".$key." Value ".$value);
					$clInstance->__set($key, $value);
				}
			/*check if function exists*/
			if(method_exists($clInstance, $funct))
			{
				syslog(LOG_INFO, "class ".$class." funct ".$funct." Param ". json_encode($param));
				$result   = $clInstance->$funct();
				$lb_valid = $result["lb_valid"];
				$res      = $result["data"];	
			}
			else
				$res="Error el Metodo ".$funct.". No existe en la clase ".$class.".";
		}
		else
			$res="Error al Instanciar la clase ".$class.".";

		$_rawData=array("lb_valid"=>$lb_valid, "data"=>$res);

		$response=$this->encodeOutput($_rawData);
		return $response;
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	public function encodeOutput($rawData)
	{
		$requestContentType = $_SERVER['CONTENT_TYPE'];
		//$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false)
		{
			$response = $this->encodeJson($rawData);
			
		}
		else if(strpos($requestContentType,'text/html') !== false)
		{
			$response = $this->encodeHtml($rawData);
			
		} 
			else if(strpos($requestContentType,'application/xml') !== false)
			{
				$response = $this->encodeXml($rawData);
			
			}
		return $response;
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	public function encodeHtml($responseData) {
	
		$htmlResponse = "<table border='1'>";
		foreach($responseData as $key=>$value)
		{
			if(is_array($value))
				$value=encodeHtml($value);
			$htmlResponse .= "<tr><td>". $key. "</td><td>". $value. "</td></tr>";
		}
		$htmlResponse .= "</table>";
		return $htmlResponse;
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/	
	public function encodeJson($responseData)
	{
		$jsonResponse = json_encode($responseData);
		return $jsonResponse;
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	public function encodeXml($responseData)
	{
		// creating object of SimpleXMLElement
		$xml = new SimpleXMLElement('<?xml version="1.0"?><responseData></responseData>');
		foreach($responseData as $key=>$value) 
		{
			if(is_array($value))
				$value=encodeXml($value);
			$xml->addChild($key, $value);
		}
		return $xml->asXML();
	}

}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	$api= new ws_rest_api();
	$api->API();

?>
