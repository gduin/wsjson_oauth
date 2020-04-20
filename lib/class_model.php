<?php
	
	
	abstract class _model
	{
		var $db;
		var $io_sql;

		public function get_conn()
		{
			require_once('class_connection_db.php');
			require_once('class_sql.php');

			$settings          = require '../cfg/gsettings.php';
			
					
			$conn              = new connection_db();
			$conn->as_hostname = $settings["settings"]["db"]["host"];
			$conn->as_login    = $settings["settings"]["db"]["username"];
			$conn->as_password = $settings["settings"]["db"]["password"];
			$conn->as_database = $settings["settings"]["db"]["database"];
			$conn->as_gestor   = $settings["settings"]["db"]["driver"];
			$this->db          = $conn->uf_connect_db();
			
			$this->io_sql      = new class_sql($this->db);
		}


		function __set($name, $value)
		{
			$this->$name= $value;
		}


		abstract public function create();
		abstract public function view();
		abstract public function update();
		abstract public function delete();

	}
?>
