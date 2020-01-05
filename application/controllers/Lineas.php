<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller; 

class Lineas extends REST_Controller {

    //este constructor para heredar la inicializacion de la base de datos en todas las peticiones
    public function __construct()
    {
        //esto es para permitir de cualquier origen que solo se realicen esas peticiones get put post delete options y otras propiedades
        header("Access-Control-Allow-Methods:  GET");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");

       parent::__construct();
       $this ->load -> database ();

    }

    public function index_get() 
    {
        $query=$this->db->query('SELECT * FROM `lineas`');

        $respuesta = Array(
            'error'=> FALSE,
            'lineas'=>$query->result_array()
             
        );
       $this->response($respuesta);
    }





}


