<?php

namespace App\Http\Controllers\App\Mission;

use App\Enums\MissionAddonType;
use App\Enums\MissionMapType;
use App\Enums\MissionPhaseType;
use App\Enums\MissionType;
use App\Enums\PermissionType;
use App\Enums\RoleType;
use App\Http\Controllers\Controller;
use App\Repositories\Mission\Interfaces\MissionRepositoryInterface;
use App\Repositories\User\Interfaces\UserMissionRepositoryInterface;
use App\Services\Mission\Contracts\MissionServiceContract;
use Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EditorController extends Controller
{
    private MissionRepositoryInterface $missionRepository;
    private UserMissionRepositoryInterface $userMissionRepository;
    private MissionServiceContract $missionService;

    public function __construct
    (
        MissionRepositoryInterface $missionRepository, UserMissionRepositoryInterface $userMissionRepository,
        MissionServiceContract $missionService
    )
    {
        $this->missionRepository = $missionRepository;
        $this->userMissionRepository = $userMissionRepository;
        $this->missionService = $missionService;
    }

    public function new(Request $request): View
    {
        $user = Auth::user();

        if (!$user->hasAnyPermission([PermissionType::MAKER1->name, PermissionType::MAKER2->name]))
        {
            abort(404);
        }

        return view('app.mission.editor', [
            'user' => $user,
            'title' => '미션 생성',
            'edit' => false,
            'types' => MissionType::getByRole($user->roles()->latest()->first()->name),
            'maps' => MissionMapType::getKoreanNames(),
            'addons' => MissionAddonType::getKoreanNames(),
            'contents' => [
                'type' => '',
                'date' => '',
                'time' => '',
                'map' => '',
                'addons' => [],
                'body' => '',
                'tardy' => false,
            ]
        ]);
    }

    public function edit(Request $request, int $missionId): View
    {
        $user = Auth::user();
        $mission = $this->missionRepository->findById($missionId);

        if (is_null($mission) || ($mission->user_id !== $user->id) && !$user->hasRole(RoleType::ADMIN->name))
        {
            abort(404);
        }

        return view('app.mission.editor', [
            'user' => $user,
            'title' => '미션 수정',
            'edit' => true,
            'types' => [
                $mission->type => MissionMapType::getKoreanNames()[$mission->type] // 미션 수정은 미션 타입을 바꿀 수 없다.
            ],
            'maps' => MissionMapType::getKoreanNames(),
            'addons' => MissionAddonType::getKoreanNames(),
            'contents' => [
                'id' => $mission->id,
                'type' => $mission->type,
                'date' => $mission->expected_at->format('Y-m-d'),
                'time' => $mission->expected_at->format('H:i'),
                'map' => $mission->data['map'],
                'addons' => $mission->data['addons'],
                'body' => $mission->body,
                'tardy' => !$mission->can_tardy, //체크 박스의 기본값 => 중도 참여 비허용
            ]
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $this->jsonValidator($request, [
                'id' => 'int',
                'type' => 'int|required',
                'date' => 'string|required',
                'time' => 'string|required',
                'map' => 'string|required',
                'addons' => 'array|required',
                'body' => 'string',
                'tardy' => 'boolean|required'
            ]);

            $user = Auth::user();
            $types = array_keys(MissionType::getByRole($user->roles()->latest()->first()->name));
            $type = (int) $request->get('type');

            if (!in_array($type, $types))
            {
                throw new \Exception('PERMISSION ERROR', 422);
            }

            $typeKorean = MissionType::getKoreanNames()[$type];
            $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i', "{$request->get('date')} {$request->get('time')}");

            if ($date->isPast())
            {
                throw new \Exception('DATE OLD', 422);
            }

            if ($this->missionRepository->new()->newQuery()->whereBetween('expected_at', [now()->subHours(2), now()->addHours(2)])->count() > 0)
            {
                throw new \Exception('DATE UNAVAILABLE', 422);
            }


            $mission = $this->missionRepository->create([
                'user_id' => $user->id,
                'type' => $type,
                'code' => mt_rand(1000, 9999),
                'title' => "{$date->format('m월 d일 H시 i분')} {$typeKorean}",
                'body' => strip_tags($request->get('body'), '<h2><h3><h4><p><a><i><br><u><strong><sub><sup><ol><ul><li><blockquote><span><figure><table><tbody><tr><td><oembed><img>'),
                'can_tardy' => !boolval($request->get('tardy')),
                'expected_at' => $date,
                'data' => [
                    'addons' => $request->get('addons'),
                    'map' => $request->get('map')
                ]
            ]);

            /*
            if ($isSurvey) {
                $survey = $form->getMissionSurvey($mission);

                $mission->survey_id = $survey->id;
                $mission->save();
            }
            */

            $this->missionService->addParticipant($mission->id, $user->id, true);

            return $this->jsonResponse(200, 'OK', [
                'url' => route('mission.read', $mission->id)
            ]);

        } catch (\Exception $e) {
            return $this->jsonResponse($e->getCode(), \Str::upper($e->getMessage()), config('app.debug')? $e->getTrace() : []);
        }
    }

    public function update(Request $request): JsonResponse
    {

    }
}
