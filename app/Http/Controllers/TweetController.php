<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TweetController extends Controller
{
    //

    public function index()
    {
        $tweets = Tweet::with(['user' => function($q){ 
            $q->withCount([
                'followers as isFollowing' => function($q) { 
                    $q->where('follower_id', auth()->user()->id);
                }
            ])->withCasts(['isFollowing' => 'boolean']);
        }])->OrderBy('id', 'DESC')->get();
        return Inertia::render('Tweets/Index', [
            'tweets' => $tweets
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['exists:users,id'],
            'content' => ['required', 'max:280']
        ]);

        Tweet::create([
            'content' => $request->content,
            'user_id' => auth()->user()->id
        ]);

        return Redirect::route('tweets.index');   
    }

    public function profile(User $user)
    {
        $user->loadCount([
            'followers as isFollowing' => function($q){
                $q->where('follower_id', '=', auth()->user()->id)->withCasts(['isFollowing' => 'boolean']);
            },
            'followings as is_following_you' => function($q) { 
                $q->where('following_id', auth()->user()->id);
            }
        ]);

        $tweets = $user->tweets;

        return Inertia::render('Tweets/Profile', [
            'profileUser' => $user,
            'tweets' => $tweets
        ]);
    }

    public function followings()
    {
        $followings = Tweet::with('user')
        ->whereIn('user_id', auth()->user()->followings->pluck('id')->toArray())
        ->orderBy('created_at', 'DESC')
        ->get();
        return Inertia::render('Tweets/Followings', [
            'followings' => $followings
        ]);
    }

    public function follows(User $user)
    {
        auth()->user()->followings()->attach($user->id);
        return redirect()->back();
    }

    public function unfollows(User $user)
    {
        auth()->user()->followings()->detach($user->id);
        return redirect()->back();
    }
}
