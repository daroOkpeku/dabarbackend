<?php
namespace App\Traits;

trait StoreTrait{

   public function hello($model, $stories_id, $type){

    try {
        $tending = $model->where(["stories_id"=>$stories_id, 'status'=>1])->first();
        if(!$tending){
            $model->create([
             "stories_id" => $stories_id
            ]);
            return response()->json(['success'=>'you have added this story to "'.$type.'" story'], 200);
        }
        return response()->json(['error'=>'this story already exist'], 404);
      } catch (\Throwable $th) {
        return response()->json(['error'=>'this story already exist'], 404);
      }

   }


   public function randomx($model){
    $tending = $model->inRandomOrder()->limit(5)->get();
    return response()->json(["success"=>$tending]);
   }

   public function editfun($userinfo, $firstname, $lastname, $email,  $role){
    if($userinfo){
        $userinfo->firstname = $firstname;
        $userinfo->lastname = $lastname;
        $userinfo->email = $email;
        $userinfo->role = $role;
        $userinfo->save();
        return response()->json(['success'=>200, 'message'=>'you have inserted your social media links'], 200);
      }else{
         return response()->json(['error'=>500, 'message'=>'This id does not exist']);
      }

   }


   public function find_single($user, $id){
    $userinfo = $user->where(['role'=>'editor', 'id'=>$id])->first();
    if($userinfo){
     return response()->json(['status'=>200, 'success'=>$userinfo]);
    }else{
     return response()->json(['error'=>500, 'error'=>'you selected the wrong user']);
    }
   }
}
