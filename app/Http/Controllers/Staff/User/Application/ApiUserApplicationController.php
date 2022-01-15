<?php

namespace App\Http\Controllers\Staff\User\Application;

use App\Action\Group\Group;
use App\Action\PlayerHistory\PlayerHistory;
use App\Action\UserData\UserData;
use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\User;
use App\Models\UserGroup;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Str;
use function route;

class ApiUserApplicationController extends Controller
{
    public function list(Request $request, Group $group): JsonResponse
    {
        try {
            $this->jsonValidator($request, [
                'step' => 'int',
                'limit' => 'int'
            ]);

            $step = $request->get('step', 0);
            $limit = $request->get('limit', 20);

            if ($limit < 1 || $limit > 100) $limit = 20;

            $surveyForms = Survey::where('name', 'like', 'join-application-%')->get(['id'])->pluck('id')->toArray();
            $countApplicant = $group->countSpecificGroupUsers($group::ARMA_APPLY);
            $step = $this->getPaginationStep($step, $limit, $countApplicant);
            $applicants = $group->getSpecificGroupUsers($group::ARMA_APPLY, $step * $limit, $limit, true);

            $keys = [];
            $items = [];

            foreach ($applicants as $i){
                $user = $i->user()->first();

                if (is_null($user)) {
                    continue;
                }

                $keys[] = $user->id;

                $survey = $user->surveys()->whereIn('survey_id', $surveyForms)->latest()->first();
                $answers = $survey->answers()->latest()->get();

                $values = [
                    "<a class='link-indigo' href='https://steamcommunity.com/profiles/{$user->socials()->where('social_provider', 'steam')->latest()->first()->social_id}' target='_blank'>{$user->nickname}</a>",
                    '', '', '', '',
                    '<a class="link-indigo" href="'. route('staff.user.application.read', $user->id) .'">확인하기</a>'
                ];

                foreach ($answers as $it) {
                    $question = $it->question()->first();
                    $v = $it->value;

                    if (is_null($question)) continue;

                    switch ($question->title) {
                        case '네이버 아이디':
                            $values[1] = "<a class='link-indigo' href='https://cafe.naver.com/ca-fe/cafes/17091584/members?memberId={$v}' target='_blank'>{$v}</a>";
                            break;

                        case '본인의 생년월일': $values[2] = $v; break;

                        case '아르마 커뮤니티(클랜) 활동 여부': $values[3] = $v; break;
                    }
                }

                $values[4] = $survey->created_at->toDateString();

                $items[] = $values;
            }

            return $this->jsonResponse(200, 'OK', [
                'fields' => ['스팀 닉네임', '네이버 아이디', '생년월일', '타 클랜 활동', '신청일', '상세 정보'],
                'keys' => $keys,
                'items' => $items,
                'count' => [
                    'step' => $step,
                    'limit' => $limit,
                    'total' => $countApplicant
                ]
            ]);

        } catch (Exception $e) {
            return $this->jsonResponse($e->getCode(), Str::upper($e->getMessage()), []);
        }
    }

    public function process(Request $request, Group $group, PlayerHistory $history): JsonResponse
    {
        try {
            $this->jsonValidator($request, [
                'type' => 'string|required',
                'user_id' => 'array|required', // 없애면 안된다! 가입 신청자 리스트에서 처리를 해야하기 때문
                'reason' => ''
            ]);

            if (count($request->get('user_id')) <= 0) {
                throw new Exception('USER NOT SELECTED', 422);
            }

            $applicantQuery = UserGroup::where('group_id', $group::ARMA_APPLY);
            $applicants = $applicantQuery->whereIn('user_id', $request->get('user_id'))->get();

            $toGroup = match ($request->get('type')) {
                'accept' => $group::ARMA_MEMBER,
                'reject' => $group::ARMA_REJECT,
                'defer' => $group::ARMA_DEFER,
                default => null,
            };

            if (is_null($toGroup)) {
                throw new Exception('TYPE NOT FOUND', 422);
            }

            $reason = strip_tags($request->get('reason'));
            $executor = $request->user();

            foreach ($applicants as $i) {
                $user = $i->user()->first();
                $group->delete($group::ARMA_APPLY, $user, $reason);
                $group->add($toGroup, $user, $reason);

                if ($toGroup === $group::ARMA_MEMBER) {
                    foreach ([$group::ARMA_REJECT, $group::ARMA_DEFER] as $checkGroup) {
                        if ($group->has($checkGroup, $user)) {
                            $group->delete($checkGroup, $user, '가입 승인 됨.');
                        }
                    }

                    $history->add($history->getIdentifierFromUser($user), $history::TYPE_USER_JOIN, $reason, $executor);

                } else {
                    $historyType = match($request->get('type')) {
                        'reject' => $history::TYPE_APPLICATION_REJECTED,
                        'defer' => $history::TYPE_APPLICATION_DEFERRED
                    };

                    $history->add($history->getIdentifierFromUser($user), $historyType, $reason, $executor);
                }
            }

            return $this->jsonResponse(200, 'OK', []);

        } catch (Exception $e) {
            return $this->jsonResponse($e->getCode(), Str::upper($e->getMessage()), []);
        }
    }

    public function read_info(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (is_null($user)) {
                throw new Exception('NOT FOUND USER', 422);
            }

            $summaries = $user->data()->where('name', UserData::STEAM_USER_SUMMARIES)->latest()->first();
            $group = $user->data()->where('name', UserData::STEAM_GROUP_SUMMARIES)->latest()->first();
            $arma = $user->data()->where('name', UserData::STEAM_GAME_INFO_ARMA3)->latest()->first();
            $ban = $user->data()->where('name', UserData::STEAM_USER_BANS)->latest()->first();

            if (is_null($summaries) || is_null($arma) || is_null($ban) || is_null($group)) {
                throw new Exception('DATA IS NOT READY', 500);
            }

            $arma->playtimeForeverReadable = date('H시간 i분', mktime(0, $arma->playtimeForever));

            $surveyForms = Survey::where('name', 'like', 'join-application-%')->get(['id'])->pluck('id')->toArray();
            $answers = $user->surveys()->whereIn('survey_id', $surveyForms)->latest()->first()->answers()->latest()->get();

            foreach ($answers as $it) {
                $question = $it->question()->first();
                $v = $it->value;

                $naver = match ($question->title) {
                    '네이버 아이디' => $v,
                    default => null,
                };

                if (!is_null($naver)) {
                    break;
                }
            }

            return $this->jsonResponse(200, 'OK', [
                'summaries' => json_decode($summaries->data),
                'group' => json_decode($group->data),
                'arma' => json_decode($arma->data),
                'ban' => json_decode($ban->data),
                'created_at' => $summaries->updated_at->toDateString(),
                'naver_id' => $naver
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse($e->getCode(), Str::upper($e->getMessage()), []);
        }
    }
}
