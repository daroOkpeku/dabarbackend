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
    // return  $stories->randomx($stories, 8, 'Trending');
    $arr = array();
    $storiesx =  $stories->where(['status'=>1])->orderBy('created_at', 'asc')->get();
    foreach($storiesx as $story){
      $storiesSection = json_decode($story->stories_section, true);
      if (is_string($story->stories_section) && $story->stories_section == 'Trending' ) {
          array_push($arr, $story);
      } elseif (is_array($storiesSection) && in_array("Trending", json_decode($story->stories_section, true)) ) {
          array_push($arr, $story);
      }
    }
    $slicedArray = array_slice($arr, 0, 8);
    $uniquecategory = uniquecategory::collection($slicedArray)->resolve();
    return response()->json(["success"=>$uniquecategory],200);
    }

    public function editor(Stories $stories){
        $arr = array();
      $storiesx =  $stories->where(['status'=>1])->orderBy('created_at', 'asc')->get();
      foreach($storiesx as $story){
        $storiesSection = json_decode($story->stories_section, true);
        if (is_string($story->stories_section) && $story->stories_section == 'Editor' ) {
            array_push($arr, $story);
        } elseif (is_array($storiesSection) && in_array("Editor", json_decode($story->stories_section, true)) ) {
            array_push($arr, $story);
        }
      }
      $slicedArray = array_slice($arr, 0, 3);
      $uniquecategory = uniquecategory::collection($slicedArray)->resolve();
      return response()->json(["success"=>$uniquecategory],200);
    //   return  $stories->randomx($stories, 3, "Editor");
    }

    public function popular(Stories $stories, Request $request){
    $arr = array();
    $storiesx =  $stories->where(['status'=>1])->orderBy('created_at', 'asc')->get();
    foreach($storiesx as $story){
      $storiesSection = json_decode($story->stories_section, true);
      if (is_string($story->stories_section) && $story->stories_section == 'Popular' ) {
          array_push($arr, $story);
      } elseif (is_array($storiesSection) && in_array("Popular", json_decode($story->stories_section, true)) ) {
          array_push($arr, $story);
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
        $stories = Stories::where('status', 1)->orderBy('created_at', 'desc')->limit(4)->get();
         $uniquecategory = uniquecategory::collection($stories)->resolve();
        return response()->json(['success' => $uniquecategory]);

    }

    public function randomstories(Stories $stories){
     $storyx =   $stories->inRandomOrder()->limit(6)->get();
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
        if($story->status == 1){
        array_push($data, $story);
        }else{
            $todaytime =  CarbonImmutable::now('America/Los_Angeles');
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
            $story = Stories::find(intval($request->get('id')));
            $todaytime =  CarbonImmutable::now('America/Los_Angeles');
            $schedule = CarbonImmutable::parse($story->schedule_story_time);
            // $clientIp = $request->header('x-real-ip') ?: $request->ip();
            // $ip = ipadress::where('ip', $clientIp)->first();
            if($story->status == 1){
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


    public function categoryfilter(Request $request){
    $category =optional(category::where('name', $request->get('category'))->first())->id??"";
    $story = Stories::where(['category_id'=>$category])->get();
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

}
