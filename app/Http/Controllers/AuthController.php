<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\sociallink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{

    public function register(Request $request){
       User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>$request->password,
        'role'=>'user',
       ]);
       return response()->json(['success'=>'you have registered']);
    }

    public function login(Request $request){

      $user = User::where(['email'=>$request->email])->first();
      if($user && Hash::check($request->password, $user->password)){
        $token =  $user->createToken('my-app-token')->plainTextToken;
        $user->api_token = $token;
        $user->save();
        $data =['token'=>$token, 'name'=>$user->name, 'email'=>$user->email, 'role'=>$user->role, 'id'=>$user->id ];
        return response()->json(['success'=>200, 'message'=>'you logged in successfully', 'data'=>$data]);
      }else{
        return response()->json(['error'=>'please enter correct details']);
      }
    }


    public function editor_register(Request $request){
     $user =  User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>$request->password,
        'role'=>'editor',
       ]);

       sociallink::create([
        'twitter'=>$request->twitter,
        'instagram'=>$request->instagram,
        'linkedin'=>$request->linkedin,
        'user_id'=>$user->id
       ]);

       return response()->json(['status'=>200, 'success'=>'you have successfully registered']);
    }

    public function editor_login(Request $request){
        $user = User::where(['email'=>$request->email])->first();
        if($user->role == 'editor' && Hash::check($request->password, $user->password)){
          $token =  $user->createToken('my-app-token')->plainTextToken;
          $user->api_token = $token;
          $user->save();
          $data =['token'=>$token, 'name'=>$user->name, 'email'=>$user->email, 'role'=>$user->role, 'id'=>$user->id ];
          return response()->json(['success'=>200, 'message'=>'you logged in successfully', 'data'=>$data]);
        }else{
            return response()->json(['error'=>'please enter correct details']);
        }

    }


    public function admin_register(Request $request){

        $user =  User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
            'role'=>'admin',
           ]);

           sociallink::create([
            'twitter'=>$request->twitter,
            'instagram'=>$request->instagram,
            'linkedin'=>$request->linkedin,
            'user_id'=>$user->id
           ]);

           return response()->json(['status'=>200, 'success'=>'you have successfully registered']);
    }



    public function admin_login(Request $request){
        $user = User::where(['email'=>$request->email])->first();
        if($user->role == 'admin' && Hash::check($request->password, $user->password)){
          $token =  $user->createToken('my-app-token')->plainTextToken;
          $user->api_token = $token;
          $user->save();
          $data =['token'=>$token, 'name'=>$user->name, 'email'=>$user->email, 'role'=>$user->role, 'id'=>$user->id ];
          return response()->json(['success'=>200, 'message'=>'you logged in successfully', 'data'=>$data]);
        }else{
            return response()->json(['error'=>'please enter correct details']);
        }

    }

    public function postcreate(){
        try {
            if(Gate::allows("check-user", auth()->user())){
                return response()->json('hello');
             }else{
                return response()->json('you cant');
               }
        } catch (\Throwable $th) {
            return response()->json('you cant');
        }

    }

}
