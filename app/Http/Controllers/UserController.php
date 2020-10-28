<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  public function register(Request $request)
    {

      //Recoger datos por Post
      $json = $request ->input('json', null);
      $params = json_decode($json);

      $email    = (!is_null ($json) && isset($params->email)) ? $params->email : null;
      $name     = (!is_null ($json) && isset($params->name)) ? $params->name : null;
      $surname  = (!is_null ($json) && isset($params->surname)) ? $params->surname : null;
      $password = (!is_null ($json) && isset($params->password)) ? $params->password : null;
      $telefono = (!is_null ($json) && isset($params->telefono)) ? $params->telefono : null;
      $roles_id  = (!is_null ($json) && isset($params->roles_id)) ? $params->roles_id: null;

      if (!is_null($email) && !is_null($password) && !is_null($name))
      {
          //Crear usuarios 
          $user = new User();
          $user->email    =$email;
          $user->name     =$name;
          $user->surname  =$surname;
          $user->telefono =$telefono;
          $pwd = hash('sha256', $password);
          $user->password =$pwd;
          $user->roles_id   =$roles_id;
      
          //Comprobar usuario duplicado
          $isset_user = User:: where('email', '=', $email)->first();
            if(count((array)$isset_user) == 0)
            {
                //Guardar Usuario
                $user->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Usuario registrado correctamente'
                );
            }else
            {
                //No guardar porque ya existe
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Usuario duplicado'
                );
            }
      }else{
          $data = array(
              'status' => 'error',
              'code' => 400,
              'message' => 'Usuario no creado'
          );
        }
      return response()->json($data, 200);
  }//Fin metodo register

  public function login(Request $request){
      $jwtAuth = new JwtAuth();
      //Recibir POST
      $json = $request->input('json', null);
      $params = json_decode($json);
      $email    =(!is_null($json) && isset($params->email)) ? $params->email : null;
      $password =(!is_null($json) && isset($params->password)) ? $params->password : null;
      $getToken =(!is_null($json) && isset($params->getToken)) ? $params->getToken : null;

      //Cifrar contraseña

      $pwd = hash('sha256', $password);
      if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false') )
      {
        $signup = $jwtAuth->signup($email, $pwd);
      }elseif($getToken != null)
      {
       //var_dump($getToken); die();
        $signup = $jwtAuth->signup($email, $pwd, $getToken);
      }else{
        $signup = array(
            'status' => 'error', 
            'message' => 'Envia tus datos por post'
        );
      }
      return response()->json($signup,200);
  }//Fin metodo login

  public function index(Request $request ){
      /* $user = DB::table('users')->where('roles_id','2')->get(); */
     $user = User::all();
     return response()->json(array(
       'users'=> $user,
       'status'=>'success'
     ),200);
  }//fin metodo index 
  public function show($id){
      $user = User::find($id);
      return response()->json(array(
        'users'=> $user,
        'status' => 'success'
      ),200);
  }// fin metodo show
  public function update($id, Request $request){
      $hash = $request->header('Authorization',null);
      $jwtAuth = new JwtAuth();
      $checkToken = $jwtAuth->checkToken($hash);
  
        if($checkToken){
        //Recoger parametros POST
          $json = $request->input('json', null);
          $params = json_decode($json);
          $params_array = json_decode($json, true);
  
          //Validar los datos
          $validate = Validator::make($params_array,[
            'name' =>'required',
            'surname' =>'required',
            'telefono' =>'required'
          ]);
          if($validate->fails())
          {
            return response()->json($validate->errors(),400);
          }
          //Actualizar datos
          unset($params_array['id']);
          unset($params_array['email']);
          unset($params_array['password']);
          unset($params_array['created_at']);
          unset($params_array['updated_at']);
          
  
          $users = User::where('id',$id)->update($params_array);
            $data = array(
              'users' => $params,
              'status' => 'success',
              'code' => 200
            );
        }else{
          //Error 
          $data = array(
            'message' => 'Login incorrecto',
            'status' => 'error',
            'code' => 300
          );
        }
        return response()->json($data, 200);
  }// fin metodo update
  
  public function destroy($id, Request $request){
      $hash = $request->header('Authorization', null);
      $jwtAuth = new JwtAuth();
      $checkToken = $jwtAuth->checkToken($hash);
  
      if($checkToken)
      {
        //comprobar el registro existente
        $user = User::find($id);
  
        //Borrar Registro
        $user->delete();
  
        //Devolver registro
        $data = array(
          'users' => $user,
          'status' => 'success',
          'code' => 200
        ); 
  
      }else{
        //Error 
        $data = array(
          'message' => 'Login incorrecto',
          'status' => 'error',
          'code' => 400
          
        );
      }
      return response()->json($data, 200);
    }// fin metodo destroy

  public function getClientes(){
    $user = DB::table('users')->where('roles_id','2')->get();
    return response()->json(array(
      'users'=> $user,
      'status'=>'success'
    ),200);
   }
  
   public function getOperador(){
    $user = DB::table('users')->where('roles_id','3')->get();
    return response()->json(array(
      'users'=> $user,
      'status'=>'success'
    ),200);
   }
}