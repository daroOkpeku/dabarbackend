<?php

namespace App\Http\Controllers;

use App\Http\Resources\uniquecategory;
use App\Models\category;
use App\Models\featured;
use App\Models\ipadress;
use App\Models\popular;
use App\Models\Stories;
use App\Models\Subscribe;
use App\Models\tending;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetController extends Controller
{
    public function searchstories(Request $request){
        $search = $request->get("search");
        $serchstories =  Stories::search($search)->take(5)->get();
       return response()->json(['success'=>$serchstories]);
    }

    // public function tending(tending $tending){
    //    $tending->randomx($tending);
    // }

    // public function popular(popular $popular){
    //     $popular->randomx($popular);
    // }

    // public function featured(featured $featured){
    //     $featured->randomx($featured);
    // }

    public function  tending(Stories $stories){
    return  $stories->randomx($stories, 1);
    }

    public function editor(Stories $stories){
      return  $stories->randomx($stories, 2);
    }

    public function popular(Stories $stories){
    return $stories->randomx($stories, 3);
    }

    public function randomcategory(Stories $stories){
        $uni = $stories->distinct()->pluck('category_id')->limit(4);
     $stories = Stories::whereIn('category_id',  $uni)->get();
     return response()->json(['success'=>$stories]);
    }

    public function category(Stories $stories, Request $request){
        // $uni = $stories->distinct()->pluck('category_id')->first();
      // $category = category::where('id', $uni)->first();
    //   $categoryanx = $request->get('category_id')?$request->get('category_id'):$category->id;
    $categoryanx = $request->get('category_id');
     $data = $stories->where(['category_id'=>$categoryanx])->get()->toArray();
     $ans = intval($request->get('number'));
     $pagdata =  $this->paginate($data, 8, $ans);
     return response()->json(['success'=>$pagdata]);
    }

    public function updatestories(){
       $stories =   Stories::orderBy('created_at', 'desc')->get();
       $data = array();
       foreach($stories as $story){
        if($story->status == 1){
        array_push($data, $story);
        }else{
            $todaytime =  CarbonImmutable::now();
            $schedule = CarbonImmutable::parse($story->schedule_story_time);

            if($schedule->diffInDays($todaytime) == 0 &&  $schedule->diffInHours($todaytime) == 0 && $schedule->diffInMinutes($todaytime) && $schedule->diffInSeconds($todaytime)){
                $updatestory = Stories::find($story);
                $updatestory->status = 1;
                $updatestory->save();
                array_push($data, $updatestory);
            }
        }
       }

      $datax =   array_slice($data, 0, 5);
      return response()->json(['success'=>$datax], 200);
    }

    public function singlestory(Request $request){
        try {
            $story = Stories::where(["id", $request->id])->first();
            $todaytime =  CarbonImmutable::now();
            $schedule = CarbonImmutable::parse($story->schedule_story_time);
            // $clientIp = $request->header('x-real-ip') ?: $request->ip();
            // $ip = ipadress::where('ip', $clientIp)->first();
            if($story->status == 1){
              $ans = $story->no_time_viewed + 1;
              $story->no_time_viewed =  $ans;
               $story->save();
                 return response()->json(['success'=>200, 'message'=>$story]);
            }else{
           if($schedule->diffInDays($todaytime) == 0 &&  $schedule->diffInHours($todaytime) == 0 && $schedule->diffInMinutes($todaytime) && $schedule->diffInSeconds($todaytime)){
            $ans = $story->no_time_viewed + 1;
            $story->no_time_viewed =  $ans;
            $story->status = 1;
             $story->save();
             return response()->json(['success'=>200, 'message'=>$story]);
            }else{
                return response()->json(['success'=>200, 'message'=>'you cant view this stort now']);
            }
            }

        } catch (\Throwable $th) {
            return response()->json(['success'=>403, 'message'=>'this id does nt exist']);

        }

    }



        public function downloadsubscribe(Subscribe $subscribe){
        $all = $subscribe->all();
        $fileName = 'subscribeemail.csv';

        $headers = array(
          "Content-type"        => "text/csv",
          "Content-Disposition" => "attachment; filename=$fileName",
          "Pragma"              => "no-cache",
          "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
          "Expires"             => "0"
      );

      $columns = array('Email');


      $callback = function() use($all, $columns) {
          $file = fopen('php://output', 'w');
          fputcsv($file, $columns);

          foreach ($all as $task) {
              $row['Email']  = $task->email;

              fputcsv($file, array($row['Email'],));
          }

          fclose($file);
      };
      return response()->stream($callback, 200, $headers);

        }

        public function allsubscribe(Request $request){
          $subscribe =  Subscribe::all()->toArray();
          $ans = intval($request->get('number'));
          $pagdata =  $this->paginate($subscribe, 8, $ans);
          return response()->json(['success'=>200, 'message'=>$pagdata]);
        }

        public function searchsubsribe(Request $request){
            $search = $request->get("search");
            $serchstories =  Stories::where(['status'=>1])->search($search)->take(5)->get();
            return response()->json(['success'=>$serchstories]);
        }

}
