<?php

	require_once('../lib/class_model.php');

	class client extends _model
	{
	
		var $codemp             ="";
		var $codcliente         ="";
		var $rifcliente         ="";
		var $tipocontribuyente  ="";
		var $porc_retencion     =0;
		var $nomcliente         ="";
		var $nomseniat          ="";
		var $dircliente         ="";
		var $telfcliente        ="";
		var $emailcliente       ="";
		var $sc_cuenta_cxc      ="";
		var $sc_cuenta_anticipo ="";
		var $precio_cliente     ="";
		var $estatus            ="f";
		var $codpai             =""; 
		var $codest             ="";
		var $codmun             ="";
		var $codpar             =""; 
		var $codciu             ="";

		var $data               ="";

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
		public function create()
		{
			return $this->client_save();
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
			$ls_sql_aux="";
			if ($this->codcliente!="")
				$ls_sql_aux="'".$this->codcliente."' ";

			$this->get_conn();
			$ls_sql="SELECT sp_vw_clientes(".$ls_sql_aux.");";
			syslog(LOG_INFO,"View ". $ls_sql);
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
				$this->data=array("message"=>$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
			else
			{
				$lb_valido=true;
				while($row=$this->io_sql->fetch_row($rs_data))
					$this->data[]=array( "codemp"=>utf8_encode($row["codemp"]),	"codcliente"=>utf8_encode($row["codcliente"]),	"rifcliente"=>utf8_encode($row["rifcliente"]),	"tipocontribuyente"=>utf8_encode($row["tipocontribuyente"]),	"porc_retencion"=>utf8_encode($row["porc_retencion"]), "nomcliente"=>utf8_encode($row["nomcliente"]), "nomseniat"=>utf8_encode($row["nomseniat"]),	"dircliente"=>utf8_encode($row["dircliente"]),	"telfcliente"=>utf8_encode($row["telfcliente"]),	"emailcliente"=>utf8_encode($row["email"]),	"sc_cuenta_cxc"=>utf8_encode($row["sc_cuenta_cxc"]),	"sc_cuenta_anticipo"=>utf8_encode($row["sc_cuenta_anticipo"]),	"precio_cliente"=>utf8_encode($row["precio_cliente"]),	"codpai"=>utf8_encode($row["codpai"]),	"codest"=>utf8_encode($row["codest"]),	"codmun"=>utf8_encode($row["codmun"]),	"codpar"=>utf8_encode($row["codpar"]),	"codciu"=>utf8_encode($row["codciu"]),"message"=>"");
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
			return $this->client_save();
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
			$ls_sql=" SELECT sp_del_cliente('". $this->codemp."', '".$this->codcliente."');";
			
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
		public function client_save()
		{
			$lb_valido=false;
			$this->get_conn();
			syslog(LOG_INFO, " CREATE codemp ".$this->codemp." codcliente ".$this->codcliente.	" rifcliente ".$this->rifcliente.	" tipocontribuyente ".$this->tipocontribuyente." porc_retencion ".$this->porc_retencion." nomcliente ".$this->nomcliente.	" nomseniat ".$this->nomseniat." dircliente ".$this->dircliente.	" telfcliente ".$this->telfcliente." emailcliente ".$this->emailcliente.	" sc_cuenta_cxc ".$this->sc_cuenta_cxc." sc_cuenta_anticipo ".$this->sc_cuenta_anticipo.	" precio_cliente ".$this->precio_cliente." estatus ".$this->estatus.	" codpai ".$this->codpai." codest ".$this->codest." codmun ".$this->codmun." codpar ".$this->codpar." codciu ".$this->codciu);

			$lb_valido=false;
			$lb_existe=$this->uf_existe_cliente();
			$lb_existe_rif=$this->uf_existe_cliente_rif();
			if(!$lb_existe && !$lb_existe_rif)
			{
				$this->codcliente=$this->uf_get_max_codigo();
				$ls_sql="SELECT sp_new_cliente('".$this->codemp."','".$this->codcliente."','".$this->rifcliente."','".$this->tipocontribuyente."',".$this->porc_retencion.",'".$this->nomcliente."','".$this->nomseniat."','".$this->dircliente."','".$this->telfcliente."','".$this->emailcliente."','".$this->sc_cuenta_cxc."','".$this->sc_cuenta_anticipo."','".$this->precio_cliente ."','". $this->estatus. "','". $this->codpai."','". $this->codest."','". $this->codmun."','". $this->codpar."','". $this->codciu."');";
			}
			else
			{
				$ls_sql="SELECT sp_up_cliente('".$this->codemp."','".$this->codcliente."','".$this->rifcliente."','".$this->tipocontribuyente."',".$this->porc_retencion.",'".$this->nomcliente."','".$this->nomseniat."','".$this->dircliente."','".$this->telfcliente."','".$this->emailcliente."','".$this->sc_cuenta_cxc."','".$this->sc_cuenta_anticipo."','".$this->precio_cliente ."','". $this->estatus. "','". $this->codpai."','". $this->codest."','". $this->codmun."','". $this->codpar."','". $this->codciu."');";
			}
			syslog(LOG_INFO, "Cliente save ".$ls_sql);
			$rs_data=$this->io_sql->execute($ls_sql);
			if($rs_data===false)
			{
				syslog(LOG_INFO, "Cliente save rs_data===false ".$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->data=array("message"=>$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
			}
			else 
			{
				$lb_valido=true;
				$this->data=array("message"=>"Se Guardo satisfactoriamente");
			}
			return array("lb_valid"=>$lb_valido, "data"=>$this->data);
		}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
		function uf_existe_cliente_rif()
		{
			$lb_valido=false;
			$this->get_conn();
			$ls_sql="SELECT  sp_exist_rif_cliente('".$this->codemp."', '".$this->rifcliente."');";
			syslog(LOG_INFO,"Existe Cliente rif ".$ls_sql);
			$rs_data=$this->io_sql->select($ls_sql);
			syslog(LOG_INFO,"Existe Cliente rif despues del select");
			if($rs_data===false)
			{
				syslog(LOG_INFO, "Existe Cliente rif rs_data===false ".$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->data=array("message"=>$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
			}
			else
			{
				$row=$this->io_sql->fetch_row($rs_data);
				if(!is_array($row))	
					$lb_valido=false;
				else
					$lb_valido=true;
			}
			syslog(LOG_INFO,"Existe Cliente rif ".$lb_valido);
			return $lb_valido;
		}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
		function uf_existe_cliente()
		{
			$lb_valido=false;
			$this->get_conn();
			$ls_sql=" SELECT sp_exist_cliente('".$this->codemp."' AND codcliente='".$this->codcliente."');";
			syslog(LOG_INFO,"Existe Cliente ".$ls_sql);
			$rs_data=$this->io_sql->select($ls_sql);
			syslog(LOG_INFO,"Existe Cliente despues del select ");
			if($rs_data===false)
			{
				syslog(LOG_INFO, "Existe Cliente rs_data===false ".$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->data=array("message"=>$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
			}
			else
			{
				if($rs_data->RecordCount()==0)
					$lb_valido=false;
				else
					$lb_valido=true;
			}
			syslog(LOG_INFO,"Existe Cliente ".$lb_valido);
			return $lb_valido;
		}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
		function uf_get_max_codigo()
		{
			$this->get_conn();
			
			$ls_sql=" SELECT sp_get_max_codigo('".$this->codemp."');";
			
			syslog(LOG_INFO,"Max Codigo ".$ls_sql);
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				syslog(LOG_INFO, "get max codigo Cliente ".$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->data=array("message"=>$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
					$codigo=$row["codigo"];
			}
			return $codigo;
		}
	}
?>
