<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller; 

class Pedidos extends REST_Controller {

    //este constructor para heredar la inicializacion de la base de datos en todas las peticiones
    public function __construct()
    {
        //esto es para permitir de cualquier origen que solo se realicen esas peticiones get put post delete options y otras propiedades
        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");

       parent::__construct();
       $this ->load -> database ();

    }

    public function realizar_orden_post($token="0",$id_usuario="0")
    {
        $data= $this->post();

        // verificar token e id del usuario

        if ($token=="0" || $id_usuario=="0") {
            $respuesta = array('error' => TRUE,
                               'mensaje'=>'token invalido y/o usuario invalido' );

        $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);

        }
        // verificar que el pedido tenga items


        if (!isset($data["items"]) || strlen($data['items'])==0) {
            $respuesta = array('error' => TRUE,
                               'mensaje'=>'Faltan los items en el post' );

            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);

        }
        //aqui validamos y decimos que el id tiene que ser igual a lo que me traiga asi como el token

        $condiciones = array('id'=> $id_usuario,'token'=>$token);
        $this->db->where($condiciones);
        $query=$this->db->get('login');
        $existe=$query->row();

        if (!$existe) {
            $respuesta = array('error' => TRUE,
                                'mensaje'=>'Ususario y token incorrectos' );

            $this->response($respuesta);

        }
        //usuario y token son correctos

        //reseto el query que estabamos usando

        $this->db->reset_query();

        //insertaremos un id de pedido con el id de usuario en nuestro pedido
        $insertar = array('usuario_id'=>$id_usuario);
        $this->db->insert('ordenes',$insertar);
        $orden_id = $this->db->insert_id();

        //crear el detalle de la orden
        $this->db->reset_query();
        //esta instruccion me sirve para traer mis items y separarlos por comas
        $items= explode(',',$data['items']);

        //insertamos el detalle de orden con este foreach

        foreach ($items as $producto_id)   {
            $data_insertar=array('producto_id'=>$producto_id,'orden_id'=>$orden_id);
            $this->db->insert('ordenes_detalle',$data_insertar);
        }
        $respuesta = array(

            'mensaje'=>FALSE,
            'orden_id'=> $orden_id
        );

        $this->response($respuesta);
        




    }


    public function obtener_pedidos_get($token="0",$id_usuario="0") 
    {
         // verificar token e id del usuario

         if ($token=="0" || $id_usuario=="0") {
            $respuesta = array('error' => TRUE,
                               'mensaje'=>'token invalido y/o usuario invalido' );

        $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);

        }

        $condiciones = array('id'=> $id_usuario,'token'=>$token);
        $this->db->where($condiciones);
        $query=$this->db->get('login');
        $existe=$query->row();

        if (!$existe) {
            $respuesta = array('error' => TRUE,
                                'mensaje'=>'Ususario y token incorrectos' );

            $this->response($respuesta);
            return;

        }

        //restornar todas las ordenes del usuario

        $query = $this->db->query('SELECT * FROM `ordenes` where usuario_id=' .$id_usuario);

        $ordenes = array();

        foreach ($query->result() as $row) {
            //con esta sentencia sql nos muestra los detalles de producto y relaciona la tabla orden con productos para obetener el detalle del  codigo
            $query_detalle = $this->db->query('SELECT a.orden_id, b.* FROM `ordenes_detalle`a INNER JOIN productos b on a.producto_id = b.codigo WHERE orden_id= '.$row->id);
          
            //mostramos la orden que obtenemos
            $orden= array(

                'id'=>$row->id,
                'creado_en'=>$row->creado_en,
                'detalle'=>$query_detalle->result()

            );

            //insertamos la orden
            array_push($ordenes ,$orden);
        }

        $respuesta = array('error'=>FALSE,
                            'ordenes'=>$ordenes
                        );
        //Mostramos la respuesta en ajax
        $this->response($respuesta);

    }

    public function borrar_pedido_delete($token="0",$id_usuario="0",$orden_id="0")
    {
        // verificar token e id del usuario

        if ($token=="0" || $id_usuario=="0" || $orden_id== 0) {
            $respuesta = array('error' => TRUE,
                               'mensaje'=>'token invalido,usuario invalido y/o orden invalida.' );

        $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);

        }

        $condiciones = array('id'=> $id_usuario,'token'=>$token);
        $this->db->where($condiciones);
        $query=$this->db->get('login');
        $existe=$query->row();

        if (!$existe) {
            $respuesta = array('error' => TRUE,
                                'mensaje'=>'Ususario y token incorrectos' );

            $this->response($respuesta);

        }
        //Verificar si la orden es de ese usuario
        $this->db->reset_query();
        $condiciones  = array('id'=> $orden_id,
                            'usuario_id'=>$id_usuario
                        );
                        $this->db->where($condiciones);
                        $query = $this->db->get('ordenes');

                        $existe = $query->row();

                        if (!$existe) {
                            $respuesta = array(
                                'error'=>TRUE,
                                'mensaje'=>'Esa orden no puede ser borrada'
                            );
                        }
                        $this->response($respuesta);

                        //todo esta bien 

                        $condiciones = array('id'=>$orden_id);
                        $this->db->delete('ordenes',$condiciones);

                        $condiciones = array('orden_id'=>$orden_id);
                        $this->db->delete('ordenes_detalle',$condiciones);

                        $respuesta = array('error'=>FALSE,
                                            'mensaje'=>'orden eliminada');

                        $this->response($respuesta);

        
    }

}