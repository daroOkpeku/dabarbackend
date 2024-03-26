<?php

namespace App\Http\Controllers;

use App\Events\publishstroyevent;
use App\Http\Requests\change_category;
use App\Http\Requests\createstoryreq;
use App\Http\Requests\delete_user_req;
use App\Http\Requests\deletemediareq;
use App\Http\Requests\sectionreq;
use App\Http\Requests\deletestoryreq;
use App\Http\Requests\edituser_req;
use App\Http\Requests\mediarequest;
use App\Http\Requests\profilecreatereq;
use App\Http\Requests\profileupdatereq;
use App\Http\Requests\storyidreq;
use App\Http\Requests\subscribereq;
use App\Http\Requests\writer_req;
use App\Http\Resources\dashbordresource;
use App\Http\Resources\storyresource;
use App\Http\Resources\uniquecategory;
use App\Http\Resources\usersresource;
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
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\HttpCache\Store;
use ImageKit\ImageKit;
use App\Models\stories_sections;
class PostController extends Controller
{

      public function createstory(createstoryreq $request){
            // createstoryreq
        //     $dateWithoutTimezone = preg_replace('/\s\(.*\)/', '', $request->schedule_story_time);
        //   $date_time = Carbon::parse($dateWithoutTimezone);
        //   $formattedDate = $date_time->format('Y-m-d H:i:s');
           $date_time = Carbon::parse($request->schedule_story_time);
        $formattedDate = $date_time->format('Y-m-d H:i:s');
           $story = Stories::create([
                'heading'=>$request->heading,
                'presummary'=>$request->presummary,
                'category_id'=>$request->category_id,
                'writer_id'=>$request->writer_id,
                'read_time'=>$request->read_time,
                'main_image'=>$request->main_image,
                // 'keypoint'=>$request->keypoint,
                // 'thumbnail'=>$request->thumbnail,
                'summary'=>$request->summary,
                'body'=>$request->body,
                "stories_section"=>$request->stories_section,
                //'sub_categories_id'=>$request->sub_categories_id,
                //'no_time_viewed'=>$request->no_time_viewed,
                'schedule_story_time'=>$formattedDate,
                'status'=>$request->status
            ]);
             if($story->status == 1){
                event(new publishstroyevent($story->id, $story->heading, ) );
             }
            return response()->json(['success'=>'you have created a story', 'date'=>$story->schedule_story_time]);

      }




      public function editstory(createstoryreq $request){
        // if(Gate::allows("check-editor", auth()->user())){
            // createstoryreq

           try {
        //     $dateWithoutTimezone = preg_replace('/\s\(.*\)/', '', $request->schedule_story_time);
        //   $date_time = Carbon::parse($dateWithoutTimezone);
        //   $formattedDate = $date_time->format('Y-m-d H:i:s');
        $date_time = Carbon::parse($request->schedule_story_time);
        $formattedDate = $date_time->format('Y-m-d H:i:s');
           $story = Stories::find($request->id);
           $story->fill([
            'heading' => $request->heading,
            'presummary' => $request->presummary,
            'category_id' => $request->category_id,
            'writer_id' => $request->writer_id,
            'read_time' => $request->read_time,
            'main_image' => $request->main_image,
            'summary' => $request->summary,
            'body' => $request->body,
            'stories_section' => $request->stories_section,
            'schedule_story_time' => $formattedDate,
            'status' => $request->status,
        ]);
          $story->save();
           return response()->json(['success'=>200, 'message'=>"you have edited the article", 'date'=>$story->schedule_story_time]);
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
             $data = $media->all();
           return response()->json(['success'=>200, 'message'=>'your file has been saved', 'data'=>$data]);
            }
        }


        public function mediadata(){
            $media =  Media::all();
            // $ans = intval($page);
            // $pagdata =  $this->paginate($media, 8, $ans);
            return response()->json(['success'=>$media]);
        }

        public function deletemediadata(Media $media, deletemediareq $request){
          $media->find($request->id)->delete();
          $all = $media->all();
          return response()->json(['success'=>$all]);
        }

        public function recentstories(){
         $story =   Stories::where('status', 1)->orderBy('created_at', 'desc')->limit(4)->get();
         $data = storyresource::collection($story)->resolve();
         return response()->json(['success'=>200, 'message'=>$data]);
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
                    $user->firstname = $request->firstname;
                    $user->lastname = $request->lastname;
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
                $user->firstname = $request->firstname;
                $user->lastname = $request->lastname;
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

         public function storyedit(Stories $id){
            // $data = $story->find(intval($id));
          return response()->json(['message'=>$id],200);
         }

         public function deletesinglestory(deletestoryreq $request, Stories $story){
            $story->where(['id'=>$request->id])->delete();
            return response()->json(['message'=>'This story has been deleted'],200);
         }

         public function logout(){
            auth()->user()->tokens()->delete();
            return response()->json(['success'=>'logged out']);
         }

         public function uploadauth(){
            $imageKit = new ImageKit(
              "public_Wl9bE4KPg58H6+uWmt7exPKW+Wc=",
              "private_qsav7UfRQU1bRCF+6hyJyUkRnf8=",
              "https://ik.imagekit.io/jtcvnfckl"
              );
              $authenticationParameters = $imageKit->getAuthenticationParameters();
              return  response()->json(['success'=>$authenticationParameters]);
           }

           public function publishedstories(Request $request){
            $story =   Stories::where(['status'=>1])->orderBy('created_at', 'desc')->get();
            $data = storyresource::collection($story)->resolve();
            $ans = intval($request->get('number'));
            $pagdata =  $this->paginate($data , 8, $ans);
            return response()->json(['success'=>200, 'message'=>$pagdata]);
           }

           public function searchpublishedstories(Request $request){
            $search = $request->get("search");
            $ans = intval($request->get('number'));
            $serchstories =  Stories::where(['status'=>1])->search($search)->take(5)->get();
            $data = storyresource::collection($serchstories)->resolve();
            $pagdata =  $this->paginate($data , 8, $ans);

           return response()->json(['success'=>$serchstories]);
        }


        public function unpublishedstories(Request $request){
            $story =   Stories::where(['status'=>0])->orderBy('created_at', 'desc')->get();
            $data = storyresource::collection($story)->resolve();
            $ans = intval($request->get('number'));
            $pagdata =  $this->paginate($data , 8, $ans);
            return response()->json(['success'=>200, 'message'=>$pagdata]);
           }

           public function searchunpublishedstories(Request $request){
            $search = $request->get("search");
            $ans = intval($request->get('number'));
            $serchstories =  Stories::where(['status'=>0])->search($search)->take(5)->get();
            $data = storyresource::collection($serchstories)->resolve();
            $pagdata =  $this->paginate($data , 8, $ans);

           return response()->json(['success'=>$pagdata]);
        }


        public function alluser (Request $request){
            if(Gate::allows("check-admin", auth()->user())){
                $user = User::all();
                $ans = intval($request->get('number'));
                $data = usersresource::collection($user)->resolve();
                $pagdata =  $this->paginate($data , 8, $ans);
                return response()->json(['success'=>$pagdata]);
            }else{
                return response()->json(['error'=>'you do not have access to this api']);
            }
       }

      public function edituser(edituser_req $request){
        if(Gate::allows("check-admin", auth()->user())){
          try {
            $user = User::findOrFail($request->id);
            $userprofile = userprofile::where('user_id', $request->id)->first();
            if($user && $userprofile){
                DB::transaction(function() use($user, $userprofile, $request){
                    $user->update([
                        'firstname'=>$request->firstname,
                        'lastname'=>$request->lastname,
                        'role'=>$request->role,
                    ]);

                    $userprofile->update([
                        "username"=>$request->username,
                        "phone"=>$request->phone
                    ]);
                    return response()->json(['message'=>'The user profile has been updated'], 200);

                });
            }else if($user && !$userprofile){
                $user->update([
                    'firstname'=>$request->firstname,
                    'lastname'=>$request->lastname,
                    'role'=>$request->role,
                ]);
                return response()->json(['message'=>'The user profile has been updated'], 200);
            }

          } catch (\Throwable $th) {
            return response()->json(['error'=>'something went wrong'], 500);
          }
        }else{
            return response()->json(['error'=>'you do not have access to this api'],500);
        }
      }

      public function deleteuser(delete_user_req $request){
        if(Gate::allows("check-admin", auth()->user())){
            User::findOrFail($request->id)->delete();
            return response()->json(['success'=>'you have deleted this user'],200);
        }else{
         return response()->json(['error'=>'you do have access to this api endpoint'],500);
        }
      }

    public function searchuser(Request $request){
        if(Gate::allows("check-admin", auth()->user())){
            $user =  User::search($request->get('search'))->get();
            $data = usersresource::collection($user)->resolve();
            $ans = intval($request->get('number'));
            $pagdata =  $this->paginate($data , 8, $ans);
            return response()->json(['success'=>$pagdata],200);
        }else{
        return response()->json(['error'=>'you do have access to this api endpoint'],500);
        }
    }


    public function allusercsv()
    {
        //  $users = User::all();
        // $all = User::with('userprofiledetails')->get();
        $all = User::with('userprofiledetails')->get();
        // $all = usersresource::collection($users);

        $fileName = 'userdoc.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $columns = ['firstname', 'lastname', 'email', 'role', 'status', 'username', 'phone'];


        $callback = function () use ($all, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);


                foreach ($all as $al) {
                    $row = [
                        'firstname' => $al->firstname,
                        'lastname'  => $al->lastname,
                        'email'     => $al->email,
                        'role'      => $al->role,
                        'status'    => $al->status == 1 ? 'Active' : 'Inactive',
                        'username'  => $al->userprofiledetails->username,
                        'phone'     =>strval($al->userprofiledetails->phone),
                    ];

                  fputcsv($file, $row);
                }
           fclose($file); // Make sure to close the file handle
        };
        // return response()->stream($callback, 200, $headers);

        return response()->streamDownload($callback, $fileName, $headers);
    }


    public function stories_sections(){
         if(Gate::allows("check-admin", auth()->user())){
      $stories_sections = stories_sections::all();
      return response()->json(['success'=>$stories_sections]);
         }else{
            return response()->json(['error'=>'you do not have access to this api'],500);
        }
    }


    public function create_category(sectionreq $request){
        if(Gate::allows("check-admin", auth()->user())){
             category::create([
                  "name"=>$request->name
                 ]);
                 return response()->json(['success'=>'category created successfully'],200);
        }else{
             return response()->json(['error'=>'you do not have access to this api'],500);
        }
    }

    public function stories_unique_date(){
        $groupedData = [];

        $uniqueDates =  Stories::latest('created_at')->take(10)->pluck('created_at');
        foreach ($uniqueDates as $date) {
             $datex =  Carbon::parse($date);
            $dataForDate = Stories::whereDate('created_at', $datex)->get();

            $groupedData[] = [
                'date' => $date,
                'data' => $dataForDate,
            ];
        }

        return response()->json(['success'=>$groupedData],200);
    }

    public function stories_category(Request $request){
    //    $all = Stories::all();
    //    $stories =  storyresource::collection($all)->resolve();
    //    $ans = intval($request->get('number'));
    //    $pagdata =  $this->paginate($stories , 8, $ans);
    //    return response()->json(['success'=>$pagdata],200);

    $all = category::all()->toArray();
    $ans = intval($request->get('number'));
   $pagdata =  $this->paginate($all , 8, $ans);
    return response()->json(['success'=>$pagdata]);
    }


    public function all_writer(Request $request){
        $all = writer::all()->toArray();
        $ans = intval($request->get('number'));
        $pagdata =  $this->paginate($all , 8, $ans);
         return response()->json(['success'=>$pagdata]);
    }

    public function create_writer(writer_req $request){
        if(Gate::allows("check-admin", auth()->user())){
       writer::create([
        "name"=>$request->name
       ]);
       return response()->json(['success'=>'you have created a writer']);
    }else{
       return response()->json(['error'=>'you do not have access to this api'],500);
       }
    }

    public function delete_writer(edituser_req $request){
        if(Gate::allows("check-admin", auth()->user())){
            $writer = writer::find($request->id)->delete();
            return response()->json(["success"=>"the writer has been deleted"]);
        }else{
            return response()->json(['error'=>'you do not have access to this api'],500);
        }
    }


    public function stories_from_client(Request $request){
    $story = Stories::where(['heading'=>$request->heading])->first();
    if($story){
        // $date =  Carbon::parse($request->date);

         $story->body = $request->body;
        $story->main_image = $request->main_image;
       // $story->created_at = $date;
        //$story->status = 1;
        $story->save();
        // return response()->json(['success'=>'successful']);
    }else{
        $date =  Carbon::parse($request->date);
            $story = new  Stories();
            $story->heading = $request->heading;
            $story->presummary = $request->presummary;
            // 'category_id'=>$request->category_id,
            // 'writer_id'=>$request->writer_id,
            $story->read_time = $request->read_time;
            $story->main_image = $request->main_image;
            // 'keypoint'=>$request->keypoint,
            // 'thumbnail'=>$request->thumbnail,
            $story->summary = $request->summary;
           $story->body = $request->body;
           $story->writer = $request->writer;
           $story->category = $request->category;
            //"stories_section"=>$request->stories_section,
            //'sub_categories_id'=>$request->sub_categories_id,
            //'no_time_viewed'=>$request->no_time_viewed,
            //'schedule_story_time'=>$formattedDate,
            $story->status = 1;
            $story->created_at = $date;
             $story->save();
        return response()->json(['success'=>'successful']);

    }

    }

    public function story_empty_id(){

        $stories =   Stories::all();
        $count = count($stories);
        for ($i = 0; $i < $count; $i++) {
            $story = $stories[$i];
         if(empty($story->category_id) && empty($story->writer_id)){
                $category = category::where("name", $story->category)->first();
                $writer = writer::where("name", $story->writer)->first();
                if($category && $writer){
                    $story->category_id = $category->id;
                    $story->writer_id = $writer->id;
                    $story->save();

                }
        }
     }
     return response()->json(["success"=>"successful"]);


    }

    public function story_admin (Stories $story, Request $request){
    if(Gate::allows("check-admin", auth()->user())){
     $stories =  $story->orderBy('created_at', 'desc')->get();
     $uniquecategory = uniquecategory::collection($stories)->resolve();
     $ans = intval($request->get('number'));
     $pagdata =  $this->paginate($uniquecategory, 8, $ans);
     return response()->json(['success'=>$pagdata],200);
     }else{
     return response()->json(['error'=>'you do not have access to this api'],500);

     }
    }

    public function story_delete(Request $request){
    if(Gate::allows("check-admin", auth()->user())){
    Stories::find($request->get('story_id'))->delete();
    return response()->json(['success'=>'successful'],200);
    }else{
    return response()->json(['error'=>'you do not have access to this api'],500);
    }
    }

    public function change_category(change_category $request){
    if(Gate::allows("check-admin", auth()->user())){
    $categoryid = optional(category::where(['name'=>$request->category])->first())->id??"";
    Stories::find($request->id)->update([
        "category_id"=>$categoryid
    ]);
     return response()->json(['success'=>'successful'],200);
    }else{
    return response()->json(['error'=>'you do not have access to this api'],500);
     }
    }

    public function allcategory(){
    if(Gate::allows("check-admin", auth()->user())){
     $category = category::all();
     return response()->json(['success'=>$category],200);
    }else{
        return response()->json(['error'=>'you do not have access to this api'],500);
    }
   }

}
