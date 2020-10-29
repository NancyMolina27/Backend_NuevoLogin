<?php
namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    public function index(Request $request )
    {
     $roles = Roles::all();
     return response()->json(array(
       'roles'=> $roles,
       'status'=>'success'
     ),200);
    }

    public function show($id)
    {
      $roles = Roles::find($id);
      return response()->json(array(
        'roles'=> $roles,
        'status' => 'success'
      ),200);
    }


  public function store(Request $request)
  {
    $hash = $request->header('Authorization',null);
    $jwtAuth = new JwtAuth();
    $checkToken = $jwtAuth->checkToken($hash);

      if($checkToken){
       //recoger datos por post
       $json = $request->input('json',null);
       $params = json_decode($json);
       $params_array = json_decode($json,true);

      //Validación de datos 
        $validate = Validator::make($params_array,[
          'nombre' =>'required|unique:roles',
          'description' =>'required',
        ]);

        if($validate->fails())
        {
          return response()->json($validate->errors(),400);
        }
        
       //Guardar Viaje 
        $roles = new Roles();
        $roles->nombre        = $params->nombre;
        $roles->description   = $params->description;
        $roles->save();

        $data = array(
          'roles' => $roles,
          'status' => 'success',
          'code' => 200
        );
      }else{
        //Error 
        $data = array(
          'message' => 'Rol incorrecto',
          'status' => 'error',
          'code' => 400
        );
      }
      return response()->json($data,200);
  }//Fin metodo store

  public function update($id, Request $request)
  {
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
          'nombre' =>'required',
          'description' =>'required',
        ]);
        if($validate->fails())
        {
          return response()->json($validate->errors(),400);
        }
        //Actualizar roles
        unset($params_array['id']);
        unset($params_array['created_at']);
        unset($params_array['updated_at']);

          $roles = Roles::where('id',$id)->update($params_array);

          $data = array(
            'roles' => $params,
            'status' => 'success',
            'code' => 200
          );
      }else{
        //Error 
        $data = array(
          'message' => 'Rol incorrecto',
          'status' => 'error',
          'code' => 300
        );
      }
      return response()->json($data, 200);
  }

public function destroy($id, Request $request)
  {
    $hash = $request->header('Authorization', null);
    $jwtAuth = new JwtAuth();
    $checkToken = $jwtAuth->checkToken($hash);

    if($checkToken)
    {
      //comprobar el registro existente
      $roles = Roles::find($id);

      //Borrar Registro
      $roles->delete();

      //Devolver registro
      $data = array(
        'viaje' => $roles,
        'status' => 'success',
        'code' => 200
      ); 

    }else{
      //Error 
      $data = array(
        'message' => 'Roles incorrecto',
        'status' => 'error',
        'code' => 400
        
      );
    }
    return response()->json($data, 200);
  }
}
