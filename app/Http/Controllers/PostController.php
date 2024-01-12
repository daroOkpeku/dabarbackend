<?php

namespace App\Http\Controllers;

use App\Http\Requests\createstoryreq;
use App\Http\Requests\deletestoryreq;
use App\Http\Requests\mediarequest;
use App\Http\Requests\profilecreatereq;
use App\Http\Requests\profileupdatereq;
use App\Http\Requests\storyidreq;
use App\Http\Requests\subscribereq;
use App\Http\Resources\dashbordresource;
use App\Models\category;
use App\Models\featured;
use App\Models\Media;
use App\Models\popular;
use App\Models\Stories;
use App\Models\Subscribe;
use App\Models\tending;
use App\Models\topstories;
use App\Models\User;
use App\Models\userprofile;
use App\Models\writer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{

      public function createstory(createstoryreq $request){
        // if(Gate::allows("check-editor", auth()->user())){
           $date_time = Carbon::parse($request->date_time);
           $formattedDate = $date_time->format('Y-m-d H:i:s');
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
                'no_time_viewed'=>$request->no_time_viewed,
                'schedule_story_time'=>$formattedDate,
                'status'=>$request->status
            ]);
            return response()->json(['success'=>'you have created a story']);
        // }else{
        //    return response()->json(['error'=>'you are not a writer']);
        // }
      }




      public function editstory(createstoryreq $request){
        // if(Gate::allows("check-editor", auth()->user())){
           try {
            $date_time = $request->date_time?Carbon::parse($request->date_time):Carbon::now();
            $formattedDate = $date_time->format('Y-m-d H:i:s');
           $story = Stories::find($request->id);
           $story->update([
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
                'no_time_viewed'=>$request->no_time_viewed,
                'schedule_story_time'=>$formattedDate,
                'status'=>$request->status
           ]);
           return response()->json(['success'=>200, 'message'=>"you have edited the article"]);
           } catch (\Throwable $th) {
            return response()->json(['error'=>500, 'message'=>'please select the correct story']);
           }
        // }else{
        //     return response()->json(['error'=>403, 'message'=>'you do not have access to this email']);
        // }
      }

      public function deletestory(deletestoryreq $request){
        if(Gate::allows("check-editor", auth()->user())){
            try {
                $story = Stories::find($request->id)->delete();
                return response()->json(['success'=>200, 'message'=>'this story has been deleted']);
            } catch (\Throwable $th) {
                return response()->json(['error'=>500, 'message'=>'please select the correct story']);
            }
        }else{
            return response()->json(['error'=>500, 'message'=>'you dont have access to this endpoint']);

        }

      }


      public function dashboardata(){
         $data = array(
            [
                "title"=>"Articles Available",
                "theme"=>"primary",
                "list"=>count(Stories::all()),
                "name"=>"Articles"
            ],
            [
                "title"=>"Users Available",
                "theme"=>"warning",
                'list'=>count(User::all()),
                "name"=>"User"
            ],
            [
                "title"=>"Categories Available",
                "theme"=>"info",
                "list"=>count(Stories::distinct()->get(['category_id'])),
                "name"=>"Category"
            ],
            [
                "title"=>"Draft Availiable",
                "theme"=>"danger",
                "list"=>count(Stories::where(['status'=>0])->get()),
                "name"=>"Category"
            ]

         );
         return response()->json(['success'=>200, "message"=>$data]);
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

        public function mediainsert(mediarequest $request){
        if(Gate::allows("check-admin", auth()->user())){
            $media = new Media();
            $media->name = $request->name;
            $media->alter_text = $request->alter_text;
            $media->file = $request->file;
            $media->save();
           return response()->json(['success'=>200, 'message'=>'your file has been saved']);
            }
        }


        public function mediadata($page){
            $media =  Media::all();
            $ans = intval($page);
            $pagdata =  $this->paginate($media, 8, $ans);
            return response()->json(['success'=>$pagdata]);
        }

        public function recentstories(){
         $story =   Stories::latest()->limit(4)->get();
         return response()->json(['success'=>200, 'message'=>$story]);
        }

        public function userprofile(User $user, Request $request){
            // W.I.P there suppose to be resoure here

            $idx =  intval($request->get('id'));
           $answer = $user->where(['id'=>$idx])->first();
           return response()->json(['success'=>200, 'message'=>$answer]);
        }

        public function userprofilex(userprofile $userprofile, Request $request){
            $user =  $userprofile->where(['user_id'=>$request->get('id')])->first();
            if($user){
              return response()->json(['success'=>200, 'message'=>$user]);
            }
        }


         public function profilecreate(userprofile $userprofile, profilecreatereq $request){
             $user = User::where(['id'=>$request->user_id])->first();
            $userx = $userprofile->where(['user_id'=>$request->user_id])->first();
              if($user && !$userx){
                DB::transaction(function ()  use($request, $userprofile, $user) {
                    $user->name = $request->name;
                    $user->save();

                   $userprofile->create([
                       'username'=>$request->username,
                       'phone'=>$request->phone,
                       'user_id'=>$request->user_id
                   ]);
                   });
                   return response()->json(['success'=>200,  'message'=>'you have created your profile'],200);
              }

             }


         public function profileupdate(userprofile $userprofile, profileupdatereq $request){
            $user = User::where(['id'=>$request->user_id])->first();
            $userx = $userprofile->where(['user_id'=>$request->user_id])->first();
              if($user && $userx){
                DB::transaction(function ()  use($request, $userx, $user) {
                $user->name = $request->name;
                $user->save();
                $userx->username = $request->username;
                $userx->phone = $request->phone;
                $userx->save();
                });
                return response()->json(['success'=>200, 'message'=>'you have updated your profile'],200);
              }
         }


         public function storydatalist(){
            $data = [
                "category"=>category::all(),
                "writer"=>writer::all()
            ];
            return response()->json(['message'=>$data],200);
         }


}
