<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class isStoreOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $storeId = $request->route('store');

        if(!$user = auth()->user()) { 
            throw new NotFoundHttpException('User not found');
        }

        if(!$user->hasRole('store-owner')) { 
            throw new AccessDeniedHttpException('User does not have the right role for access');
        }

        $exists = $user->stores()->where(function($query) use ($storeId, $user) { 
            $query->where('owner_id', $user->id);
            if($storeId) { 
                $query->where('id', $storeId);
            }
        });

        if(!$exists) { 
            throw new AccessDeniedHttpException('That\'s not your store or you don\t have any stores. Do you want to create one?');
        }


        return $next($request);
    }
}
