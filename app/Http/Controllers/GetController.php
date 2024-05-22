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
use App\Models\videos;
use Carbon\Carbon;
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
    // return  $stories->randomx($stories, 8, 'Trending');
    $arr = array();
     $storiesx =  $stories->where(['status'=>1])->orderBy('created_at', 'desc')->get();

    foreach($storiesx as $story){
$storiesSection = json_decode($story->stories_section, true);

      if( $story->stories_section != null &&  $story->stories_section != "" && is_string($storiesSection)){
        $zoe = json_decode($story->stories_section, true);
        $cow = json_decode($zoe, true);
        if(in_array("Trending", $cow)){
       array_push($arr, $story);
        }
      }

    if(is_array($storiesSection)){
       $checksection = json_decode($story->stories_section, true);
    foreach($checksection as $storysec){
       if($storysec['value']  == 'Trending'){
        array_push($arr, $story);
       }
    }
    }




    }
    $slicedArray = array_slice($arr, 0, 8);
     $uniquecategory = uniquecategory::collection($slicedArray)->resolve();

     return response()->json(["success"=>$uniquecategory],200);
    }

    public function editor(Stories $stories){

        $arr = array();
      // $storiesx =  $stories->where('status', 1)->orderBy('created_at', 'desc')->get();
      $storiesx = $stories->whereIn('status', [1, '1'])->orderBy('created_at', 'desc')->get();
      foreach($storiesx as $story){
        $storiesSection = json_decode($story->stories_section, true);

        if( $story->stories_section != null &&  $story->stories_section != "" && is_string($storiesSection)){
          $zoe = json_decode($story->stories_section, true);
          $cow = json_decode($zoe, true);
          if(in_array("Editor", $cow)){
         array_push($arr, $story);
          }
        }

        if(is_array($storiesSection)){
            $checksection = json_decode($story->stories_section, true);
         foreach($checksection as $storysec){
            if($storysec['value']  == 'Editor'){
             array_push($arr, $story);
            }
         }
         }

      }
      $slicedArray = array_slice($arr, 0, 3);
      $uniquecategory = uniquecategory::collection($slicedArray)->resolve();
      return response()->json(["success"=>$uniquecategory],200);
    }

    public function popular(Stories $stories, Request $request){
    $arr = array();
    $storiesx =  $stories->whereIn('status', [1, '1'])->orderBy('created_at', 'desc')->get();
    foreach($storiesx as $story){
    $storiesSection = json_decode($story->stories_section, true);

    if( $story->stories_section != null &&  $story->stories_section != "" && is_string($storiesSection)){
      $zoe = json_decode($story->stories_section, true);
      $cow = json_decode($zoe, true);
      if(in_array("Popular", $cow)){
     array_push($arr, $story);
      }
    }

    if(is_array($storiesSection)){
        $checksection = json_decode($story->stories_section, true);
     foreach($checksection as $storysec){
        if($storysec['value']  == 'Popular'){
         array_push($arr, $story);
        }
     }
     }
    }
    $uniquecategory = uniquecategory::collection($arr)->resolve();
    $ans = intval($request->get('number'));
    $pagdata =  $this->paginate($uniquecategory, 6, $ans);
    return response()->json(["success"=>$pagdata],200);
    }

    public function randomcategory(Stories $stories){
        // $uni = Stories::orderBy('created_at', 'desc')->pluck('category_id');
        // whereIn('category_id', $uni)
        $stories = Stories::whereIn('status', [1, '1'])->orderBy('created_at', 'desc')->limit(4)->get();
         $uniquecategory = uniquecategory::collection($stories)->resolve();
        return response()->json(['success' => $uniquecategory]);

    }

    public function randomcategorystrories(category $category, Request $request){
        $uniqueRandomData = $category::distinct()->inRandomOrder()->limit(4)->get();
        $arr = [];
        $uniqueRandomData->each(function($item) use (&$arr) {
            $arr[] = $item->id;
        });
        $story =  Stories::whereIn("category_id", $arr)->orderBy('created_at', 'desc')->inRandomOrder()->limit(12)->get();
         $uniquecategory = uniquecategory::collection($story)->resolve();
    $ans = intval($request->get('number'));
    $pagdata =  $this->paginate($uniquecategory, 6, $ans);
       return response()->json(['success' => $pagdata]);
    }

    public function randomstories(Stories $stories, Request $request){
        $id = $request->get('id');
     $storyx =   $stories->where([ ['id', '!=', $id] ])->orderBy('created_at', 'desc')->inRandomOrder()->limit(2)->get();
     $uniquecategory = uniquecategory::collection($storyx)->resolve();
     return response()->json(['success'=>$uniquecategory],200);
    }

    public function category(Stories $stories, Request $request){
        // $uni = $stories->distinct()->pluck('category_id')->first();
      // $category = category::where('id', $uni)->first();
    //   $categoryanx = $request->get('category_id')?$request->get('category_id'):$category->id;
    $categoryanx = $request->get('category_id');
     $data = $stories->where(['category_id'=>$categoryanx, 'status'=>1])->get()->toArray();
     $ans = intval($request->get('number'));
     $pagdata =  $this->paginate($data, 8, $ans);
     return response()->json(['success'=>$pagdata],200);
    }

    public function updatestories(){




       $stories =   Stories::orderBy('created_at', 'desc')->get();
       $data = array();
       foreach($stories as $story){

        if($story->status == 1 || $story->status == '1'){
          array_push($data, $story);
        }else{
            $todaytime =  Carbon::now('America/Los_Angeles');
            $schedule = CarbonImmutable::parse($story->schedule_story_time);
            if($schedule->diffInDays($todaytime) == 0 &&  $schedule->diffInHours($todaytime) == 0 && $schedule->diffInMinutes($todaytime) == 0 && $schedule->diffInSeconds($todaytime) == 0){
                $updatestory = Stories::find($story->id);
                $updatestory->status = 1;
                $updatestory->save();
                array_push($data, $updatestory);
            }elseif($todaytime->greaterThanOrEqualTo($schedule)){
                $updatestory = Stories::find($story->id);
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
            $story = Stories::find(intval($request->get('id')));
            $todaytime =  Carbon::now('America/Los_Angeles');
            $schedule = CarbonImmutable::parse($story->schedule_story_time);
            // $clientIp = $request->header('x-real-ip') ?: $request->ip();
            // $ip = ipadress::where('ip', $clientIp)->first();
            if($story->status == 1 || $story->status == '1'){
              $ans = $story->no_time_viewed + 1;
              $story->no_time_viewed =  $ans;
               $story->save();
               $data = uniquecategory::make($story);
                 return response()->json(['success'=>200, 'message'=>$data], 200);
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


    public function psttime(){
        $todaytime =  Carbon::now('America/Los_Angeles');
        $formattedPSTTime = $todaytime->format('Y-m-d H:i:s');
        return response()->json(['success'=>$formattedPSTTime]);
    }

    public function categoryfilter(Request $request){
    $category =optional(category::where('name', $request->get('category'))->first())->id??"";
    $story = Stories::where(['category_id'=>$category])->orderBy('created_at', 'desc')->get();
    $uniquecategory =  uniquecategory::collection($story)->resolve();
    $ans = intval($request->get('number'));
    $pagdata =  $this->paginate($uniquecategory, 8, $ans);
    return response()->json(['success'=>$pagdata]);
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

        public function adminvideos(Request $request){
           $videos =  videos::all()->toArray();
           $ans = intval($request->get('number'));
           $pagdata =  $this->paginate($videos, 8, $ans);
           return response()->json(['success'=>200, 'message'=>$pagdata]);
        }


        public function uservideos(Request $request){
            $videos =  videos::all()->toArray();
            $ans = intval($request->get('number'));
            $pagdata =  $this->paginate($videos, 5, $ans);
            return response()->json(['success'=>200, 'message'=>$pagdata]);
         }
}
