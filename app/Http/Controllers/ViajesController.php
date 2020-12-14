<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Models\viaje;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ViajesController extends Controller
{
  public function index(Request $request )
    {
     $viaje = viaje::all();
     return response()->json(array(
       'viaje'=> $viaje,
       'status'=>'success'
     ),200);
    }

    public function show($id)
    {
      $viaje = viaje::find($id);
      return response()->json(array(
        'viaje'=> $viaje,
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

      //Conseguir usuario identificado
      $user = $jwtAuth->checkToken($hash, true);

      //Validación de datos 
        $validate = Validator::make($params_array,[
          /* 'cliente' =>'required', */
          'operador' =>'required',
          'fecha' =>'required',
          'hora' =>'required',
          'lugar_recibido' =>'required',
          'lugar_destino' =>'required',
          'contenido' =>'required'
        ]);

        if($validate->fails())
        {
          return response()->json($validate->errors(),400);
        }
        
       //Guardar Viaje 
        $viaje = new viaje();
        $viaje->cliente        = $params->cliente;
        $viaje->operador       = $params->operador;
        $viaje->fecha          = $params->fecha;
        $viaje->hora           = $params->hora;
        $viaje->lugar_recibido = $params->lugar_recibido;
        $viaje->lugar_destino  = $params->lugar_destino;
        $viaje->contenido      = $params->contenido;
        $viaje->comentarios    = $params->comentarios;
        $viaje->user_id        = $user->sub;
        $viaje->save();

        $data = array(
          'viaje' => $viaje,
          'status' => 'success',
          'code' => 200
        );
      }else{
        //Error 
        $data = array(
          'message'=> 'Viaje incorrecto',
          'status' => 'error',
          'code'   => 400
          
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
          'cliente' =>'required',
          'operador' =>'required',
          'fecha' =>'required',
          'hora' =>'required',
          'lugar_recibido' =>'required',
          'lugar_destino' =>'required',
          'contenido' =>'required'
        ]);
        if($validate->fails())
        {
          return response()->json($validate->errors(),400);
        }
        //Actualizar el viaje
        unset($params_array['id']);
        unset($params_array['created_at']);
        unset($params_array['updated_at']);
        unset($params_array['user_id']);
        
          $viaje = viaje::where('id',$id)->update($params_array);

          $data = array(
            'viaje' => $params,
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
  }

public function destroy($id, Request $request)
  {
    $hash = $request->header('Authorization', null);
    $jwtAuth = new JwtAuth();
    $checkToken = $jwtAuth->checkToken($hash);

    if($checkToken)
    {
      //comprobar el registro existente
      $viaje = viaje::find($id);

      //Borrar Registro
      $viaje->delete();

      //Devolver registro
      $data = array(
        'viaje' => $viaje,
        'status' => 'success',
        'code' => 200
      ); 

    }else{
      //Error 
      $data = array(
        'message' => 'Viaje incorrecto',
        'status' => 'error',
        'code' => 400
        
      );
    }
    return response()->json($data, 200);
  }

  public function buscar($id)
    {
      
      $viaje =DB::table('viaje')->where('id','like',"%$id%")->get();
      return response()->json(array(
        'viaje'=> $viaje,
        'status' => 'success'
      ),200);
    }
  
}// Fin clase

