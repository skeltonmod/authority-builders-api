<?php

namespace Deyji\Manage\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $action;
    private $caller;
    public function handle($request, Closure $next, $action)
    {   

        $this->action = $action;

        if(Auth::user() != null){
            $this->caller = Auth::user();
        }
        Storage::disk('local')->append('logs.txt', $this->caller->name.' @ '. $this->action. ' in '. $request->route()->getActionName().' at exactly '. Carbon::now());
        Storage::disk('local')->append('logs.txt', "Data Change". json_encode($request->input()));
        return $next($request);
    }
}
