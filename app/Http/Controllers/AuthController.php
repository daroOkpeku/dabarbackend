<?php

namespace App\Http\Controllers;

use App\Events\emailevent;
use App\Http\Requests\loginreq;
use App\Http\Requests\registerreq;
use App\Http\Requests\socialreq;
use App\Models\Post;
use App\Models\sociallink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{

    public function register(registerreq $request){
      $user = User::create([
        'firstname'=>$request->firstname,
        'lastname'=>$request->lastname,
        'email'=>$request->email,
        "verification_code"=>sha1(time()),
        'status'=>0,
        'password'=>$request->password,
        'role'=>'writer',
       ]);
       event( new emailevent($user->firstname, $user->lastname, $user->email, $user->verification_code));
       return response()->json(['success'=>'you have registered']);
    }

    public function login(loginreq $request){

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


    public function editor_register(registerreq $request){
     $user =  User::create([
        'firstname'=>$request->firstname,
        'lastname'=>$request->lastname,
        'email'=>$request->email,
        "verification_code"=>sha1(time()),
        'status'=>0,
        'password'=>$request->password,
        'role'=>'editor',
       ]);
       event( new emailevent($user->firstname, $user->lastname, $user->email, $user->verification_code));

    //    sociallink::create([
    //     'twitter'=>$request->twitter,
    //     'instagram'=>$request->instagram,
    //     'linkedin'=>$request->linkedin,
    //     'user_id'=>$user->id
    //    ]);

       return response()->json(['status'=>200, 'success'=>'you have successfully registered']);
    }

    public function editor_login(loginreq $request){
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


    public function admin_register(registerreq $request){

        $user =  User::create([
            'firstname'=>$request->firstname,
            'lastname'=>$request->lastname,
            'email'=>$request->email,
            "verification_code"=>sha1(time()),
            'status'=>0,
            'password'=>$request->password,
            'role'=>'admin',
           ]);
           event( new emailevent($user->firstname, $user->lastname, $user->email, $user->verification_code));
           //    sociallink::create([
        //     'twitter'=>$request->twitter,
        //     'instagram'=>$request->instagram,
        //     'linkedin'=>$request->linkedin,
        //     'user_id'=>$user->id
        //    ]);

           return response()->json(['status'=>200, 'success'=>'you have successfully registered']);
    }



    public function admin_login(loginreq $request){
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


    public function social_media(socialreq $request){

       $user = User::where(['email'=>$request->email, 'verification_code'=>$request->code])->first();
        if(!$user){
           $user->status = 1;
           $user->save();

             sociallink::create([
            'twitter'=>$request->twitter,
            'instagram'=>$request->instagram,
            'linkedin'=>$request->linkedin,
            'user_id'=>$user->id
           ]);

           return response()->json(['success'=>200, 'message'=>'you have inserted your social media links'], 200);

        }else{
            return response()->json(['error'=>403, 'message'=>'please verifield your email']);
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
