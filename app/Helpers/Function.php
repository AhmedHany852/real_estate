<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
if (!function_exists('upload')) {
function upload($avatar, $directory)
{
        $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
        $avatar->move($directory, $avatarName);
        return $avatarName;

}

// function isServiceInUserSubscription( $serviceId)
// {
//     $user = Auth::guard('app_users')->user();

//     if (!$user) {
//         return false;
//     }
//     $subscriptions = $user->subscription;
//     $subscriptionData = $user->subscription()->first(['expire_date', 'visit_count']);

//     if (!$subscriptions) {
//         return false;
//     }

//     if (!$subscriptions ||  $subscriptionData->expire_date < now()) {
//         return false;
//     }
//     foreach($subscriptions as $subscription){
//         if ( $subscriptionData->visit_count >= $subscription->visits) {
//             return response()->json(['error' => 'Visit count limit exceeded'], 422);
//         }
//         $subscriptionServices = $subscription->services;

//         foreach ($subscriptionServices as $service) {
//             // dd($serviceId);
//             if ($service->id == $serviceId) {
//                 return true;
//             }
//         }
//     }
//     return false;
// }
function isServiceInUserSubscription($serviceId)
{
    $user = Auth::guard('app_users')->user();

    if (!$user) {
        return false;
    }

    $subscriptions = $user->subscription()->where('expire_date', '>', now())->get();

    if ($subscriptions->isEmpty()) {
        return false;
    }

    foreach ($subscriptions as $subscription) {
        $pivotData = $subscription->pivot;

        if ($pivotData->visit_count > $subscription->visits) {
            return false;
            return response()->json(['error' => 'Visit count limit exceeded'], 422);
        }

        $subscriptionServices = $subscription->services;

        foreach ($subscriptionServices as $service) {
            if ($service->id == $serviceId) {
                return true;
            }
        }
    }

    return false;
}

}
