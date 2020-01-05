<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller; 

class Login extends REST_Controller {

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

    public function index_post()
    {
        $data = $this->post();

        if (!isset($data['correo']) OR !isset($data['contrasena'])) {
           $respuesta = array('error' => TRUE,
                            'mensaje'=>'La informacion enviada no es valida'
                        );
            $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        //tenemos contraseña y correo en un post

        $condiciones = array('correo' => $data['correo'],
                            'contrasena'=>$data['contrasena']);
        $query=$this->db->get_where('login',$condiciones);
        $usuario = $query->row();

        if (!isset($usuario)) {
            $respuesta = array('error' => TRUE,
                                'mensaje'=>'Usuario y/o contrasena no valido' );

            $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        //aqui tenemmos un usuario y contraseña validos

        //Token

        $token=bin2hex(openssl_random_pseudo_bytes(20));
        $token=hash('ripemd160',$data['correo']);

        $this->db->reset_query();
        //actualizamos un token por cada usuario
        $actualizar_token=array('token'=>$token);
         //seleccionamos el usuario  
        $this->db ->where('id',$usuario->id);
         //realizamos el update
        $hecho = $this->db->update('login',$actualizar_token);
         //devolvemos el token nuevo del usuario
        $respuesta = array(
                     'error'=> FALSE,
                     'token'=>$token,
                     'id_usuario'=>$usuario->id
                     );

        //$this->response( $data["correo"] );
        $this->response($respuesta);

        
    }

    
    


}