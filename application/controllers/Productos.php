<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller; 

class Productos extends REST_Controller {

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

    public function todos_get($pagina=0)
    {
        $pagina = $pagina *10;
        $query=$this->db->query('SELECT * FROM `productos` limit '.$pagina.',10 ');

        $respuesta = Array(
            'error'=> FALSE,
            'productos'=>$query->result_array()
             
        );
       $this->response($respuesta);
    }
    public function por_tipo_get($tipo=0,$pagina=0)
    {

        if ($tipo == 0) {    
            $respuesta = array(
                'error'=> TRUE,
                'mensaje'=>'FALTA EL PARAMETRO DE TIPO'
            );
            $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $pagina = $pagina *10;
        $query=$this->db->query('SELECT * FROM `productos` where linea_id='.$tipo.' limit '.$pagina.',10 ');

        $respuesta = Array(
            'error'=> FALSE,
            'productos'=>$query->result_array()
             
        );
       $this->response($respuesta);
        
    }
    
    public function buscar_get($termino = "NO ESPECIFICO")
    {
        $query=$this->db->query("SELECT * FROM `productos` WHERE producto LIKE '%".$termino."%'");

        $respuesta = Array(
            'error'=> FALSE,
            'termino'=>$termino,
            'productos'=>$query->result_array()
             
        );
       $this->response($respuesta);
        
    }
        
    

}
