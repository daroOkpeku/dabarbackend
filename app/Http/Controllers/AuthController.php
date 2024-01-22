<?php

namespace App\Http\Controllers;

use App\Events\emailevent;
use App\Events\roleevent;
use App\Http\Requests\contactreq;
use App\Http\Requests\editor_register_req;
use App\Http\Requests\editusersreq;
use App\Http\Requests\loginreq;
use App\Http\Requests\registerreq;
use App\Http\Requests\role_confirm_req;
use App\Http\Requests\socialreq;
use App\Http\Requests\writereditorreq;
use App\Http\Resources\usersresource;
use App\Models\Contact;
use App\Models\Post;
use App\Models\sociallink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{

    public function register(writereditorreq $request){
    if(Gate::allows("check-admin", auth()->user())){
        // $code = sha1(time());
      $user = User::create([
        'firstname'=>$request->firstname,
        'lastname'=>$request->lastname,
        'email'=>$request->email,
        "verification_code"=>sha1(time()),
        'status'=>0,
      //  'password'=>$request->password,
        'role'=>'writer',
       ]);
      event(new roleevent($request->firstname, $request->lastname, $request->email, $user->verification_code, $request->role));
       return response()->json(['success'=>'you have registered']);
    }else{
        return response()->json(['status'=>403, 'error'=>'you do not have access to this api']);
    }
    }

    public function role_confirm (role_confirm_req $request){
          $user = User::where(['email'=>$request->email, "verification_code"=>$request->verification_code, 'role'=>$request->role])->first();
          if($user){
            $user->update([
                'status'=>1,
                'password'=>Hash::make($request->password)
            ]);
            return response()->json(["success"=>200, "message"=>"your account has been verifield"]);
          }else{
            return response()->json(['status'=>500, 'error'=>'are you sure you are passing the correct values']);
          }
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


    public function editor_register(editor_register_req $request){
    if(Gate::allows("check-admin", auth()->user())){
        // $code = sha1(time());
     $user =  User::create([
        'firstname'=>$request->firstname,
        'lastname'=>$request->lastname,
        'email'=>$request->email,
        "verification_code"=>sha1(time()),
        'status'=>0,
        // 'password'=>$request->password,
        'role'=>$request->role,
       ]);
       event(new roleevent($request->firstname, $request->lastname, $request->email, $user->verification_code, $request->role));

    //    event( new emailevent($user->firstname, $user->lastname, $user->email, $user->verification_code));
       return response()->json(['status'=>200, 'success'=>'you have successfully registered']);
    }else{
        return response()->json(['status'=>403, 'error'=>'you do not have access to this api']);

    }
    }

    public function editor_login(loginreq $request){
        $user = User::where(['email'=>$request->email])->first();
        if($user && $user->role == 'editor' && Hash::check($request->password, $user->password)){
          $token =  $user->createToken('my-app-token')->plainTextToken;
          $user->api_token = $token;
          $user->save();
          $data =['token'=>$token, 'name'=>$user->name, 'email'=>$user->email, 'role'=>$user->role, 'id'=>$user->id ];
          return response()->json(['success'=>200, 'message'=>'you logged in successfully', 'data'=>$data]);
        }else{
            return response()->json(['error'=>'please enter correct details']);
        }

    }
    // MAIL_MAILER=smtp
    // MAIL_HOST=mail.thedabar.com
    // MAIL_PORT=465
    // MAIL_USERNAME=support@thedabar.com
    // MAIL_PASSWORD=I,1cJ94[H(@K
    // MAIL_ENCRYPTION=tls
    // MAIL_FROM_ADDRESS=support@thedabar.com
    // MAIL_FROM_NAME="${APP_NAME}"

    public function admin_register(registerreq $request){
        $user =  User::create([
            'firstname'=>$request->firstname,
            'lastname'=>$request->lastname,
            'email'=>$request->email,
            "verification_code"=>sha1(time()),
            'status'=>1,
            'password'=>Hash::make($request->password),
            'role'=>$request->role,
           ]);
        //    event( new emailevent($user->firstname, $user->lastname, $user->email, $user->verification_code));
           return response()->json(['status'=>200, 'success'=>'you have successfully registered']);
    }



    public function admin_login(loginreq $request){
        $user = User::where(['email'=>$request->email])->first();
        if($user && $user->role == 'admin' && Hash::check($request->password, $user->password)){

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
     if (!$user){
         return response()->json(['error' => 404, 'message' => 'User not found'], 404);
          }
      try {
        DB::transaction(function() use($user, $request){
            $user->status = 1;
            $user->save();

                sociallink::create([
               'twitter'=>$request->twitter,
               'instagram'=>$request->instagram,
               'linkedin'=>$request->linkedin,
               'user_id'=>$user->id
              ]);
            return response()->json(['success'=>200, 'message'=>'you have inserted your social media links'], 200);
          });
      } catch (\Throwable $th) {
        return response()->json(['error' => true, 'message' => 'An error occurred during the transaction'], 500);
          }
    }

    public function postcreate(){
        try {
            if(Gate::allows("check-user", auth()->user())){
                return response()->json('hello');
             }else{
                return response()->json('you can\'t');
               }
        } catch (\Throwable $th) {
            return response()->json('you cant');
        }

    }


    public function contact(contactreq $request){
        Contact::create([
            "name"=>$request->name,
            "email"=>$request->email,
            "messages"=>$request->messages
        ]);
        return response()->json(["status"=>200, 'message'=>"we have seen your message and we get back to you soon"],200);
    }

    public function single_editor($id, User $user){
        if(Gate::allows("check-admin", auth()->user())){
          $user->find_single($user, $id);
        }else{
            return response()->json(['status'=>403, 'error'=>'you do not have access to this api']);
        }
    }



    public function edit_editor(editusersreq $request, User $user){
        if(Gate::allows("check-admin", auth()->user())){
     $userinfo = $user->where(['role'=>'editor', 'id'=>$request->id])->first();
     $user->editfun($userinfo, $request->firstname, $request->lastname, $request->email, $request->role);
    }else{
        return response()->json(['status'=>403, 'error'=>'you do not have access to this api']);
    }
    }

    public function edit_writer(editusersreq $request, User $user){
        if(Gate::allows("check-admin", auth()->user())){
            $userinfo = $user->where(['role'=>'writer', 'id'=>$request->id])->first();
            $user->editfun($userinfo, $request->firstname, $request->lastname, $request->email, $request->role);
           }else{
               return response()->json(['status'=>403, 'error'=>'you do not have access to this api']);
           }
         }

         public function single_writer($id, User $user){
            if(Gate::allows("check-admin", auth()->user())){
                $user->find_single($user, $id);
            }else{
                return response()->json(['status'=>403, 'error'=>'you do not have access to this api']);
            }
        }

        public function delete_writer($id, User $user){
            if(Gate::allows("check-admin", auth()->user())){
            $userinfo = $user->where(['role'=>'writer', 'id'=>$id])->first();
            return response()->json(['status'=>200, "success"=>$userinfo]);
            }else{
                return response()->json(['status'=>403, 'error'=>'you do not have access to this api']);
            }
        }

        public function delete_editor($id, User $user){
            if(Gate::allows("check-admin", auth()->user())){
                $userinfo = $user->where(['role'=>'editor', 'id'=>$id])->first();
                return response()->json(['status'=>200, "success"=>$userinfo]);
                }else{
                    return response()->json(['status'=>403, 'error'=>'you do not have access to this api']);
                }
        }


}
