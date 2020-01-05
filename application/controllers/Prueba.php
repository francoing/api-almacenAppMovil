<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller; 

class Prueba extends REST_Controller {

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

    public function index()
    {
        echo "Hola culiao";
    }

    public function obtener_arreglo_get($index =0)
    {
        if ($index > 2) {
            $respuesta = array('error'=> TRUE, 'mensaje'=>'No existe elemento con la posicion:'.$index);       
            $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST );//NOS INDICA EL ERROR
        } else {
            $arreglo = array("Naranja","Manzana" ,"uva");
            //decimos que la condicion es falsa y mostramos la fruta que le corresponde al indice
            $respuesta=array('error'=>FALSE,'fruta'=>$arreglo[$index]);
            $this->response($respuesta);
        }
        
        

        //echo json_encode($arreglo[$index]);

        
    }

    public function obtener_producto_get($codigo)
    {
        //$this ->load -> database ();

        $query = $this->db->query("SELECT * FROM `productos` WHERE codigo = '".$codigo."'");

     
        echo json_encode($query->result()) ;
    }




}