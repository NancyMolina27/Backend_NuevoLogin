<?php

namespace App\Http\Controllers;

use App\Models\Imagenes;
use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\Validator;


class ImagenesController extends Controller
{
    public function index(Request $request )
    {
     $imagen = Imagenes::all();
     return response()->json(array(
       'imagen'=> $imagen,
       'status'=>'success'
     ),200);
    }

    public function show($id)
    {
      $imagen = Imagenes::find($id);
      return response()->json(array(
        'imagen'=> $imagen,
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
          'file_name' =>'required|unique:imagenes',
          
        ]);

        if($validate->fails())
        {
          return response()->json($validate->errors(),400);
        }
        
       //Guardar Viaje 
        $imagen = new Imagenes();
        $imagen->file_name        = $params->file_name;
        $imagen->save();

        $data = array(
          'imagen' => $imagen,
          'status' => 'success',
          'code' => 200
        );
      }else{
        //Error 
        $data = array(
          'message' => 'Imagen incorrecta',
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
          'file_name' =>'required',
        ]);
        if($validate->fails())
        {
          return response()->json($validate->errors(),400);
        }
        //Actualizar imagen
        unset($params_array['id']);
        unset($params_array['created_at']);
        unset($params_array['updated_at']);

          $imagen = Imagenes::where('id',$id)->update($params_array);

          $data = array(
            'imagen' => $params,
            'status' => 'success',
            'code' => 200
          );
      }else{
        //Error 
        $data = array(
          'message' => 'Imagen incorrecta',
          'status' => 'error',
          'code' => 400
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
      $imagen = Imagenes::find($id);

      //Borrar Registro
      $imagen->delete();

      //Devolver registro
      $data = array(
        'imagen' => $imagen,
        'status' => 'success',
        'code' => 200
      ); 

    }else{
      //Error 
      $data = array(
        'message' => 'imagen incorrecta',
        'status' => 'error',
        'code' => 400
        
      );
    }
    return response()->json($data, 200);
  }
}
