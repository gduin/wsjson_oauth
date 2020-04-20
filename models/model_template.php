<?php

	require_once('../lib/class_model.php');

	class model_template extends _model
	{

		var $data               ="";

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
/*Implementa el metodo abstracto de la clase _model que almacenara la data  */
/*en algun repositorio. (Base de datos, Cola, Archivo, entre otros)         */
		public function create()
		{
			$this->get_conn();/*Obtiene la instancia de manejador del repositorio, actualmente DB*/
			/*codificacion del metodo de almacenamiento en el repositorio*/
		}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
/*Implementa el metodo abstracto de la clase _model que busca y devuelve la */
/*data encontrada en algun repositorio, actualmente BD                      */
		public function view()
		{
			syslog(LOG_INFO,"Function View ");
			$lb_valido=false;
			
			$this->get_conn();
			$ls_sql="SELECT sp_vw_object('".$this->field1_filter."','".$this->field2_filter."');";
			syslog(LOG_INFO,"View ". $ls_sql);
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
				$this->data=array("message"=>$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
			else
			{
				$lb_valido=true;
				while($row=$this->io_sql->fetch_row($rs_data))
					$this->data[]=array( "field1"=>utf8_encode($row["field1"]),	"field2"=>utf8_encode($row["field2"]),	"field3"=>utf8_encode($row["field3"]),	"field4"=>utf8_encode($row["field4"]),"message"=>"");
			}
			return array("lb_valid"=>$lb_valido, "data"=>$this->data);
		}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
/*Implementa el metodo abstracto de la clase _model que actualiza la data   */
/*en algun repositorio, actualmente BD                                      */
		public function update()
		{
			$this->get_conn();/*Obtiene la instancia de manejador del repositorio, actualmente DB*/
			/*codificacion del metodo de almacenamiento en el repositorio*/
		}

/****************************************************************************/
/****************************************************************************/
/*																			*/
/****************************************************************************/
/****************************************************************************/
/*Implementa el metodo abstracto de la clase _model que elimina la data     */
/*especifica en el repositorio                                              */
		public function delete()
		{
			$lb_valido=false;
			$this->get_conn();
			$ls_sql="SELECT sp_del_data('".$this->field1_filter."');";

			$rs_data=$this->io_sql->execute($ls_sql);
			if($rs_data===false)
			{
				$this->data=array("message"=>$this->io_sql->io_funciones->uf_convertirmsg($this->io_sql->message));
			}
			else
				$lb_valido=true;
			return array("lb_valid"=>$lb_valido, "data"=>$this->data);
		}
	}
?>