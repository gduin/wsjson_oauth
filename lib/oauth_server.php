<?php

	include __DIR__."/../vendor/autoload.php";
	
	//use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
	use OAuth2\Server as OAuth2Server;
	use OAuth2\Storage\Pdo;
	use OAuth2\Storage\Memory;
	use OAuth2\OpenID\GrantType\AuthorizationCode;
	use OAuth2\GrantType\UserCredentials;
	use OAuth2\GrantType\RefreshToken;

class oauth_server
{
	var $svr     = "";
	var $storage = "";

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	function __construct()
	{
		$this->srv      = $this->setup();
		//$this->response = new BridgeResponse();
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	function setup()
	{
		$settings          = require '../cfg/gsettings.php';

		$driver   = $settings["settings"]["db_oauth"]["driver"];
		$host     = $settings["settings"]["db_oauth"]["host"];
		$port     = $settings["settings"]["db_oauth"]["port"];
		$dbname   = $settings["settings"]["db_oauth"]["database"];
		$username = $settings["settings"]["db_oauth"]["username"];
		$password = $settings["settings"]["db_oauth"]["password"];

		$dsn      = $driver.":dbname=".$dbname.";host=".$host.";port=".$port;
		
		// error reporting (this is a demo, after all!)
		ini_set('display_errors',1);error_reporting(E_ALL);

		$this->storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

		// create array of supported grant types
		$grantTypes = array(
			'authorization_code' => new AuthorizationCode($this->storage),
			'user_credentials'   => new UserCredentials($this->storage),
			'refresh_token'      => new RefreshToken($this->storage, array('always_issue_new_refresh_token' => true,
			)),
		);

		// Pass a storage object or array of storage objects to the OAuth2 server class
		$server = new OAuth2Server($this->storage, array(
												'enforce_state' => true,
												'allow_implicit' => true,
												'use_openid_connect' => true,
												'issuer' => $_SERVER['HTTP_HOST'],
												), $grantTypes);

		$server->addStorage($this->getKeyStorage(), 'public_key');

		return $server;
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	function validateuser()
	{
		$request = OAuth2\Request::createFromGlobals();
		$response = new OAuth2\Response();
		syslog(LOG_INFO, "Me llamaron validateuser");
		syslog(LOG_INFO, "Me llamaron validateuser response ". json_encode($response));
		syslog(LOG_INFO, "Me llamaron validateuser request _REQUEST ". json_encode($_REQUEST));
		syslog(LOG_INFO, "Me llamaron validateuser request app[request] ". json_encode($request));
		// let the oauth2-server-php library do all the work!
		$this->srv->handleTokenRequest($request, $response);

		//return json_encode($response->getParameters(), JSON_FORCE_OBJECT);
		$response->send();
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	function refresh_token()
	{
		$request = OAuth2\Request::createFromGlobals();
		$response = new OAuth2\Response();
		syslog(LOG_INFO, "Me llamaron refresh_token");
		syslog(LOG_INFO, "Me llamaron refresh_token response ". json_encode($response));
		syslog(LOG_INFO, "Me llamaron refresh_token request _REQUEST ". json_encode($_REQUEST));
		syslog(LOG_INFO, "Me llamaron refresh_token request app[request] ". json_encode($request));
		// let the oauth2-server-php library do all the work!
		$this->srv->handleTokenRequest($request, $response);

		//return json_encode($response->getParameters(), JSON_FORCE_OBJECT);
		$response->send();
	}


/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	private function getKeyStorage()
	{
		$publicKey  = file_get_contents($this->getProjectRoot().'/data/pubkey.pem');
		$privateKey = file_get_contents($this->getProjectRoot().'/data/privkey.pem');

		// create storage
		$keyStorage = new Memory(array('keys' => array(
	    					'public_key'  => $publicKey,
	    					'private_key' => $privateKey,
			)));

		return $keyStorage;
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
	private function getProjectRoot()
	{
		syslog(LOG_INFO, "1 Arriba ".dirname(__DIR__, 1));
		syslog(LOG_INFO, "2 Arriba ".dirname(__DIR__, 2));
		return dirname(__DIR__, 1);
	}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
}
?>
