<?php

namespace App\Http\Middleware;

use App\Models\BuildingList;
use App\Service\BuildingService;
use Closure;
use Illuminate\Support\Facades\Auth;

class buildingCheck
{
    protected $buildingService = null;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            $response = $next($request);
            if (Auth::check()) $this->check();
        } else {
            $this->check();
            $response = $next($request);
        }

        return $response;
    }

    protected function check()
    {
        $this->buildingService = new BuildingService();
        $this->buildingService->init();

        foreach ($this->buildingService->schedules as $key => $item) {
            if ($item->endTime <= time()) {
                switch ($item->action) {
                    case BuildingList::ACTION_BUILD:
                        $result = $this->buildingService->build($key);
                        if ($result[0] !== 'succeed') return response($result[1], 500);
                        break;
                    case BuildingList::ACTION_DESTROY:
                        $result = $this->buildingService->destroy($key);
                        if ($result[0] !== 'succeed') return response($result[1], 500);
                        break;
                    default:
                }
            }
        }
    }
}
