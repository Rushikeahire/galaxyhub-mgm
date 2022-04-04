<?php

namespace App\Http\Controllers\App\Admin\Statistic;

use App\Enums\MissionPhaseType;
use App\Http\Controllers\Controller;
use App\Repositories\Mission\MissionRepository;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AddonController extends Controller
{
    private array $statisticAddon;

    public function addon(Request $request): View
    {
        return view('app.admin.statistic.addon');
    }

    public function data(Request $request, MissionRepository $missionRepository): JsonResponse
    {
        try
        {
            $this->validate($request, [
                'query' => 'array'
            ]);

            $this->statisticAddon =[];

            $q = $request->get('query', []);
            $query = $missionRepository->new()->newQuery()->whereIn('phase', [MissionPhaseType::END->value]);

            $start = empty($q['start']) ? \Carbon\Carbon::createFromFormat('Y-m-d', "2020-01-01") : \Carbon\Carbon::createFromFormat('Y-m-d', "{$q['start']}");
            $end = empty($q['end']) ? today() : \Carbon\Carbon::createFromFormat('Y-m-d', "{$q['end']}");

            $query = $query->whereBetween('started_at', [$start, $end]);
            $query->chunk(100, function ($missions)
            {
                foreach ($missions as $mission)
                {
                    $addons = $mission->data['addons'];

                    foreach ($addons as $addon)
                    {
                        $this->statisticAddon[$addon] = array_key_exists($addon, $this->statisticAddon) ? $this->statisticAddon[$addon] + 1 : 1;
                    }
                }
            });

            return $this->jsonResponse('200', 'success', [
                'statistic' => [
                    'keys' => array_keys($this->statisticAddon),
                    'values' => array_values($this->statisticAddon)
                ]
            ]);

        }
        catch (Exception $e)
        {
            return $this->jsonResponse($e->getCode(), Str::upper($e->getMessage()), config('app.debug') ? $e->getTrace() : []);
        }
    }
}
