<?php
	return [
		'settings' => [
			'db' => [
					'driver'    => 'POSTGRES8',
					'host'      => 'localhost',
					'port'		=> '5432',
					'database'  => 'db_factura_prueba',
					'username'  => 'userAPI',
					'password'  => 'EnlaceBD',
					'charset'   => '',
					'collation' => '',
					'prefix'    => '',
				],
			'db_oauth'=> [
					'driver'	=> 'pgsql',
					'host'		=> 'localhost',
					'port'		=> '5432',
					'database'	=> 'oauth2',
					'username'  => 'userAPI',
					'password'  => 'EnlaceBD',
					'charset'   => '',
					'collation' => '',
					'prefix'    => '',	
				]
			],
		];
?>