<?php

namespace App\Http\Controllers;

use App\Http\Requests\createstoryreq;
use App\Http\Requests\storyidreq;
use App\Http\Requests\subscribereq;
use App\Models\featured;
use App\Models\popular;
use App\Models\Stories;
use App\Models\Subscribe;
use App\Models\tending;
use App\Models\topstories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
class PostController extends Controller
{

      public function createstory(createstoryreq $request){
        if(Gate::allows("check-writer", auth()->user())){
            Stories::create([
                'heading'=>$request->heading,
                'presummary'=>$request->presummary,
                'category_id'=>$request->category_id,
                'writer_id'=>$request->writer_id,
                'read_time'=>$request->read_time,
                'main_image'=>$request->main_image,
                'keypoint'=>$request->keypoint,
                'thumbnail'=>$request->thumbnail,
                'summary'=>$request->summary,
                'body'=>$request->body,
                'sub_categories_id'=>$request->sub_categories_id,
                'no_time_viewed'=>$request->no_time_viewed
            ]);
            return response()->json(['success'=>'you have created a story']);
        }else{
           return response()->json(['error'=>'you are not a writer']);
        }
      }


      public function topstories(storyidreq $request, topstories $top){
           if(Gate::allows("check-admin", auth()->user())){
            $type = "topstory";
            $top->hello($top, $request->stories_id, $type);
           }else{
            return response()->json(['error'=>'you are not an admin']);
           }
      }


      public function featuredstories(storyidreq $request, featured $featured){
        if(Gate::allows("check-admin", auth()->user())){
            $type = "featured";
            $featured->hello($featured, $request->stories_id, $type);
                }else{
                return response()->json(['error'=>'you are not an admin']);
                }
             }



      public function tendingstories(storyidreq $request, tending $tend){
        if(Gate::allows("check-admin", auth()->user())){
            $type = "tending";
            $tend->hello($tend, $request->stories_id, $type);
              }else{
                return response()->json(['error'=>'you are not an admin']);
            }
          }


      public function populastories(storyidreq $request, popular $popular){
        if(Gate::allows("check-admin", auth()->user())){
            $type = "popular";
            $popular->hello($popular, $request->stories_id, $type);
                }else{
                return response()->json(['error'=>'you are not an admin']);
                }
      }

      public function cryptoapi(){
        $client = new CoinGeckoClient();
        $data = $client->coins()->getMarkets('usd');
        return response()->json($data);
      }

      public function subscribe(subscribereq $request){
        Subscribe::create([
          "email"=>$request->email
        ]);
        // subscribes
        return response()->json(["success"=>"you have subscribed"],200);
        }
}
