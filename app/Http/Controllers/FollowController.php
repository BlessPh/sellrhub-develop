<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowRequest;
use App\Models\Follow;
use App\Models\Shop;
use Auth;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow($shopId)
    {
        $user = Auth::user();

        $alreadyFollowing = Follow::where('user_id', $user->id)
            ->where('shop_id', $shopId)
            ->exists();

        if ($alreadyFollowing) {
            return response()->json([
                'message' => 'You already follow this shop'
            ], 400);
        }

        Follow::create([
            'user_id' => $user->id,
            'shop_id' => $shopId
        ]);

        return response()->json([
            'message' => 'Following shop successfully'
        ]);
    }


    public function unfollow($shopId)
    {
        $user = Auth::user();

        $follow = Follow::where('user_id', $user->id)->where('shop_id', $shopId)->first();

        if (!$follow) {
            return response()->json([
                'message' => 'You are not following this shop'
            ]);
        }

        $follow->delete();

        return response()->json([
            'message' => 'Unfollowing shop successfully'
        ]);
    }

    public function getFollowingShops()
    {
        $user = Auth::user();

        $shops = $user->followingShops()->get();

        return response()->json([
            'message' => 'Get all following shops successfully',
            'user' => $user,
            'shops' => $shops
        ]);
    }

    public function getFollowerShops($shopId)
    {
        $shop = Shop::findOrFail($shopId);

        $followers = $shop->followers()->get();

        return response()->json([
            'message' => 'Followers fetched successfully',
            'shop' => $shop,
            'followers' => $followers
        ]);
    }
}
