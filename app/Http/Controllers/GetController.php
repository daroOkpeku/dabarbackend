<?php

namespace App\Http\Controllers;

use App\Models\featured;
use App\Models\ipadress;
use App\Models\popular;
use App\Models\Stories;
use App\Models\Subscribe;
use App\Models\tending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetController extends Controller
{
    public function searchstories(Request $request){
        $search = $request->get("search");
        $serchstories =  Stories::search($search)->take(5)->get();
       return response()->json(['success'=>$serchstories]);
    }

    public function tending(tending $tending){
       $tending->randomx($tending);
    }

    public function popular(popular $popular){
        $popular->randomx($popular);
    }

    public function featured(featured $featured){
        $featured->randomx($featured);
    }

    public function singlestory(Request $request){
        $story = Stories::where(["id", $request->id])->first();
        $clientIp = $request->header('x-real-ip') ?: $request->ip();
        $ip = ipadress::where('ip', $clientIp)->first();
        if($story && $ip){
           $ans = $story->no_time_viewed + 1;
           $story->no_time_viewed =  $ans;
           $story->save();
           return response()->json(['success'=>$story]);
        }
        // else{
        //     $ans = $story->no_time_viewed + 1;
        //     DB::transaction(function() use($clientIp, $story, $ans){
        //         ipadress::create([
        //             'ip'=>$clientIp,
        //              'is_status'=>0,
        //      ]);

        //      $story->no_time_viewed =  $ans;
        //      $story->save();

        //     });

        // }
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


}
