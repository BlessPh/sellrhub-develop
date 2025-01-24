<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use App\Models\Plan;
use App\Models\Subscription;
use Auth;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function subscribe(SubscriptionRequest $request)
    {
        $user = Auth::user();
        $plan = Plan::findOrFail($request->validated(['plan_id']));

        $activeSubscription = $user->subscriptions()->where('ends_at', '>=', now())
            ->first();

        if ($activeSubscription) {
            return response()->json(['message' => 'Vous avez déjà un abonnement actif.'], 400);
        }

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $request->validated(['plan_id']),
            'payment_method_id' => $request->validated(['payment_method_id']),
            'start_date' => now(),
            'end_date' => now()->addDays($plan->duration_in_days),
        ]);

        return response()->json([
            'message' => 'Subscription successful',
            'subscription' => $subscription,
        ]);
    }

    public function userSubscription()
    {
        $userId = Auth::id();

        $subscriptions = Subscription::with('plan')->where('user_id', $userId)->get();

        return response()->json([
            'subscriptions' => $subscriptions,
        ]);
    }

    public function unsubscribe($id)
    {
        $user = Auth::id();

        $subscription = Subscription::findOrFail($id);

        if ($user !== $subscription->user_id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $subscription->update([
            'status' => 'cancelled',
            'end_date' => now(),
        ]);

        return response()->json([
            'message' => 'Subscription cancelled',
            'subscription' => $subscription,
        ]);
    }

}
