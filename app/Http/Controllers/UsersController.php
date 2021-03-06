<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Follower;
use App\Models\Favorite;
use App\Models\User;
use App\Models\Tweet;
use App\Models\City;
use App\Sex;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $all_users = $user->getAllUsers(auth()->user()->id);

        return view('users.index', [
            'all_users'  => $all_users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, Tweet $tweet, Follower $follower)
    {
        $login_user = auth()->user();
        $is_following = $login_user->isFollowing($user->id);
        $is_followed = $login_user->isFollowed($user->id);
        $timelines = $tweet->getUserTimeLine($user->id);
        $tweet_count = $tweet->getTweetCount($user->id);
        $follow_count = $follower->getFollowCount($user->id);
        $follower_count = $follower->getFollowerCount($user->id);
        $favorite_count = Favorite::where('user_id', $user->id)->pluck('tweet_id')->count();

        return view('users.show', [
            'user'           => $user,
            'is_following'   => $is_following,
            'is_followed'    => $is_followed,
            'timelines'      => $timelines,
            'tweet_count'    => $tweet_count,
            'follow_count'   => $follow_count,
            'follower_count' => $follower_count,
            'favorite_count' => $favorite_count,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'screen_name'    => ['required', 'string', 'min:6', 'max:50', Rule::unique('users')->ignore($user->id)],
            'name'           => ['required', 'string', 'min:1', 'max:15'],
            'single_comment' => ['required', 'string', 'min:1', 'max:15'],
            'profile_image'  => ['file', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'email'          => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)]
        ]);
        $validator->validate();
        $user->updateProfile($data);

        return redirect('users/'.$user->id);
    }
    
    public function favorite(Tweet $tweet, Follower $follower, $id)
    {
        $user = User::find($id);
        $favorite_count = Favorite::where('user_id', $user->id)->pluck('tweet_id')->count();
        $pick_user = Favorite::where('user_id', $user->id)->pluck('tweet_id')->toArray();
        $timelines = Tweet::with('city')->whereIn('id', $pick_user)->orderBy('id','desc')->paginate(50);

        $login_user = auth()->user();
        $is_following = $login_user->isFollowing($user->id);
        $is_followed = $login_user->isFollowed($user->id);
        $tweet_count = $tweet->getTweetCount($user->id);
        $follow_count = $follower->getFollowCount($user->id);
        $follower_count = $follower->getFollowerCount($user->id);
        $favorite_count = Favorite::where('user_id', $user->id)->pluck('tweet_id')->count();

        return view('users.favorite', [
            'user'           => $user,
            'is_following'   => $is_following,
            'is_followed'    => $is_followed,
            'timelines'      => $timelines,
            'tweet_count'    => $tweet_count,
            'follow_count'   => $follow_count,
            'follower_count' => $follower_count,
            'favorite_count' => $favorite_count,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    
    public function delete_confirm($id)
    {
        $user = User::find($id);

        return view('users.delete_confirm', [
            'user' => $user
        ]);
    }
    
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect('/');
    }

    public function follow(User $user)
    {
        $follower = auth()->user();
        $is_following = $follower->isFollowing($user->id);

        if(!$is_following) {
            $follower->follow($user->id);

            return back();
        }
    }

    public function unfollow(User $user)
    {
        $follower = auth()->user();
        $is_following = $follower->isFollowing($user->id);

        if($is_following) {
            $follower->unfollow($user->id);

            return back();
        }
    }
}
