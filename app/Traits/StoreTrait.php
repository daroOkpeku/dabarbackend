<?php
namespace App\Traits;

class StoreTrait{

   public function hello($model, $stories_id, $type){

    try {
        $tending = $model->where(["stories_id"=>$stories_id])->first();
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
}
