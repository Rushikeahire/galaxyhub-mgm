<?php

namespace App\Http\Controllers\App\Application;

use App\Enums\RoleType;
use App\Enums\UserRecordType;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessSteamUserAccount;
use App\Repositories\Survey\SurveyEntryRepository;
use App\Repositories\User\Interfaces\UserRecordRepositoryInterface;
use App\Repositories\User\UserAccountRepository;
use App\Services\Steam\Contracts\SteamServiceContract;
use App\Services\Survey\Contracts\SurveyServiceContract;
use App\Services\User\Contracts\UserServiceContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use function now;
use function redirect;
use function view;

class FormController extends Controller
{
    public SurveyServiceContract $surveyService;

    public function __construct(SurveyServiceContract $surveyService)
    {
        $this->surveyService = $surveyService;
    }

    public function index(Request $request): View|Application|RedirectResponse|Redirector
    {
        $user = Auth::user();

        if ($user->isBanned())
        {
            return redirect()->route('account.suspended');
        }

        if ($user->hasRole(RoleType::APPLY->name))
        {
            return redirect()->route('application.index');
        }

        if ($request->isMethod('get') && (is_null($request->session()->get('_old_input')) || count($request->session()->get('_old_input')) <= 0 ))
        {
            return redirect()->route('application.agreements');
        }

        $form = $this->surveyService->createApplicationForm();

        return view('app.application.form', [
            'survey' => $form,
            'action' => route('application.store')
        ]);
    }

    public function store(
        Request $request, SurveyEntryRepository $surveyEntryRepository, UserAccountRepository $accountRepository,
        UserServiceContract $userService, SteamServiceContract $steamService): View|Application|RedirectResponse|Redirector
    {
        try
        {
            $user = Auth::user();

            if ($user->isBanned())
            {
                return redirect()->route('account.suspended');
            }

            if ($user->hasRole(RoleType::APPLY->name))
            {
                return redirect()->route('application.index');
            }

            $form = $this->surveyService->createApplicationForm();
            $answers = $this->validate($request, $form->validateRules());

            $steamAccount = $accountRepository->findSteamAccountByUserId($user->id);

            if (is_null($steamAccount))
            {
                throw new \Exception('스팀 계정이 등록되지 않았습니다.');
            }

            $steamPlayerSummaries = $steamService->getPlayerSummaries($steamAccount->account_id);

            if ($steamPlayerSummaries['response']['players'][0]['communityvisibilitystate'] != 3)
            {
                throw new \Exception('스팀 프로필이 친구 공개 또는 비공개 상태입니다. 프로필을 공개로 변경해 주십시오.');
            }

            ProcessSteamUserAccount::dispatch($user);

            $surveyEntryRepository->new()->for($form)->by($user)->fromArray($answers)->push();

            $now = now();
            $user->update([
                'created_at' => $now,
                'agreed_at' => $now
            ]);

            $user->assignRole(RoleType::APPLY->name);
            $data = [
                'role' => RoleType::APPLY->name,
                'reason' => ''
            ];

            $userService->createRecord($user->id, UserRecordType::ROLE_DATA->name, $data);

            return redirect()->route('application.applied')->withErrors(['success' => '가입 신청이 접수되었습니다.']);
        }
        catch (\Exception $e)
        {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
