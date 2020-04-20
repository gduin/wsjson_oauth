<?php

	require_once('../lib/class_model.php');

	class product extends _model
	{

		var $codemp               ="";
		var $codprodserv          ="";
		var $tipprodserv          ="";
		var $descprodserv         ="";
		var $spi_cuenta_prodserv  ="";
		var $sc_cuenta            ="";
		/*
		var $sc_cuenta_costo      ="";
		var $sc_cuenta_inventario ="";
		*/
		var $denominacion         ="";
		var $codunimed            ="";
		var $denunimed            ="";
		var $precio_a             =0;
		var $precio_b             =0;
		var $precio_c             =0;
		var $precio_d             =0;
		var $porcentaje_descuento =0;
		
		var $min_precio           =0;
		var $max_precio           =0;
		
		var $data               ="";

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
		public function create()
		{
			
		}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
		public function view()
		{
			syslog(LOG_INFO,"Function View ");
			$lb_valido=false;
			if ($this->codprodserv!="")
				$ls_sql_aux=" AND codprodserv=".$this->codprodserv;

			$this->get_conn();
			$ls_sql=" SELECT codemp, codprodserv, tipprodserv, descprodserv, spi_cuenta_prodserv,
					(SELECT denominacion FROM spi_cuentas WHERE spi_cuenta=sfc_productos_servicios.spi_cuenta_prodserv) as denominacion,
					(SELECT sc_cuenta FROM spi_cuentas WHERE spi_cuenta=sfc_productos_servicios.spi_cuenta_prodserv) as sc_cuenta,
		 			codunimed,
		 			(SELECT max(a.denunimed) FROM siv_unidadmedida as a WHERE a.codunimed=sfc_productos_servicios.codunimed) as unidad_medida,
		 			 precio_a, precio_b, precio_c, precio_d, porcentaje_descuento
				    FROM sfc_productos_servicios
				   WHERE codemp='0001' ".$ls_sql_aux;
			syslog(LOG_INFO,"View ". $ls_sql);
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
				$this->data=array("message"=>$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
			else
			{
				$lb_valido=true;
				while($row=$this->io_sql->fetch_row($rs_data))
					$this->data[]=array( "codemp"=>utf8_encode($row["codemp"]), "codprodserv"=>utf8_encode($row["codprodserv"]), "tipprodserv"=>utf8_encode($row["tipprodserv"]), "descprodserv"=>utf8_encode($row["descprodserv"]), "spi_cuenta_prodserv"=>utf8_encode($row["spi_cuenta_prodserv"]), "denominacion"=>utf8_encode($row["denominacion"]), "sc_cuenta"=>utf8_encode($row["sc_cuenta"]), "codunimed"=>utf8_encode($row["codunimed"]), "denunimed"=>utf8_encode($row["unidad_medida"]), "precio_a"=>utf8_encode($row["precio_a"]), "precio_b"=>utf8_encode($row["precio_b"]), "precio_c"=>utf8_encode($row["precio_c"]), "precio_d"=>utf8_encode($row["precio_d"]), "porcentaje_descuento"=>utf8_encode($row["porcentaje_descuento"]),"message"=>"");
			}
			return array("lb_valid"=>$lb_valido, "data"=>$this->data);
		}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
		public function update()
		{
			$this->get_conn();
		}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
		public function delete()
		{
			$lb_valido=false;
			$this->get_conn();
			$ls_sql="DELETE FROM sfc_productos_servicios
				     WHERE codemp='".$this->codemp."' AND codprodserv='".$this->codprodserv."'";

			$rs_data=$this->io_sql->execute($ls_sql);
			if($rs_data===false)
			{
				$this->data=array("message"=>$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
			}
			else
				$lb_valido=true;
			return array("lb_valid"=>$lb_valido, "data"=>$this->data);
		}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
		function uf_existe_producto()
		{
			$lb_valido=false;
			$this->get_conn();
			$ls_sql=" SELECT 1
				    FROM sfc_productos_servicios
				   WHERE codemp='".$this->codemp."' AND codprodserv='".$this->codprodserv."' ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->data=array("message"=>$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
			}
			else
			{
				if($rs_data->RecordCount()==0)
					$lb_valido=false;
				else
					$lb_valido=true;
			}
			return array("lb_valid"=>$lb_valido, "data"=>$this->data);
		}
	}
?>