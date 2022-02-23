@push('js')
    <script defer src="//cdn.embedly.com/widgets/platform.js"></script>
@endpush
<x-theme.galaxyhub.sub-content :title="$type" :description="$mission->title" :breadcrumbs="Diglactic\Breadcrumbs\Breadcrumbs::render('app.mission', $type)">
    <div class="md:flex md:space-x-4" x-data="mission_read">
        <x-panel.galaxyhub.basics class="self-start md:basis-3/5 lg:basis-2/3 flex flex-col space-y-8">
            <div class="flex flex-col space-y-2">
                <h2 class="text-xl lg:text-2xl font-bold">{{ $type }} 소개</h2>

                @if($isAdmin && !$isMaker)
                    <x-alert.galaxyhub.danger title="중요">
                        <ul>
                            <li>관리자는 다른 미션 메이커가 생성한 미션을 수정 및 처리할 수 있습니다.</li>
                            <li>{{ $type }} 수정 및 처리 전 반드시 날짜 및 시간 확인바랍니다.</li>
                        </ul>
                    </x-alert.galaxyhub.danger>
                @endif
                @if($isMaker)
                    <x-alert.galaxyhub.info title="미션 메이커님께">
                        <ul>
                            <li>만약 {{ $type }} 시작이 늦을 경우 참가자 분들께 즉시 알려주세요.</li>
                            <li>출석 코드 발급은 {{ $type }} 종료 처리를 해주시면 됩니다.</li>
                            <li>{{ $type }} 시작 처리 이후에는 {{ $type }} 내용과 시간을 수정할 수 없습니다!</li>
                        </ul>
                    </x-alert.galaxyhub.info>
                @else
                    <x-alert.galaxyhub.info title="지켜주세요!">
                        <ul>
                            <li>아르마 밤 참가 신청 후 불참 또는 지각시 <a href="https://cafe.naver.com/gamemmakers" class="underline hover:no-underline" target="_blank">커뮤니티</a>에 사유를 남겨주세요.</li>
                            <li>DDOS 방지를 위하여 게임 접속 전 스팀 프로필 상태를 오프라인으로 변경해주세요.</li>
                            <li>게임 접속 전 팀스피크 디스크랩션을 읽어주셔야 서버 접속이 가능합니다.</li>
                        </ul>
                    </x-alert.galaxyhub.info>
                @endif
                <template x-if="data.load.data.is_edit">
                    <x-alert.galaxyhub.warning title="새로고침 필요">
                        <ul>
                            <li>{{ $type }} 소개가 변경되었습니다. - <span class="font-bold text-yellow-700 underline hover:no-underline cursor-pointer" @click="load(true)">새로고침</span></li>
                        </ul>
                    </x-alert.galaxyhub.warning>
                </template>

                <div class="h-fit w-full rounded-md bg-gray-50 dark:border dark:bg-gray-900 dark:border-gray-800 p-4">
                    <div class="prose dark:prose-invert" x-html="data.load.data.body"></div>
                </div>
            </div>

            <div class="flex flex-col space-y-2">
                <h2 class="text-xl lg:text-2xl font-bold">{{ $type }} 참가자</h2>
                <div class="">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <template x-for="i in data.participants.data.participants">
                            <div class="relative rounded-lg border border-gray-300 dark:border-gray-800 bg-white dark:bg-gray-900 px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-40">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" :src="i.avatar" alt="">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="i.name"></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 truncate tabular-nums" x-text="i.attend + '회 참가'"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </x-panel.galaxyhub.basics>

        <div class="self-start p-4 lg:p-8 md:basis-2/5 lg:basis-1/3 flex flex-col space-y-8">
            <div class="flex flex-col space-y-2">
                <h2 class="text-xl lg:text-2xl font-bold" x-text="(data.load.data.phase === 2) ? '{{ $type }} 출석 마감' : '{{ $type }} 시간'"></h2>
                <div class="mb-3 tabular-nums">
                    <div class="p-4 bg-gray-50 dark:border dark:bg-gray-900 dark:border-gray-800 rounded-lg overflow-hidden">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-300 truncate" x-text="data.load.data.timestamp.display_date"></dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100" x-text="data.load.data.timestamp.display_time"></dd>
                    </div>
                </div>
            </div>

            @if ($isMaker || $isAdmin)
                <div class="flex flex-col space-y-2">
                    <h2 class="text-xl lg:text-2xl font-bold">{{ $type }} 출석 코드</h2>
                    <div class="mb-3" >
                        <div class="p-4 bg-gray-50 dark:border dark:bg-gray-900 dark:border-gray-800 rounded-lg overflow-hidden tabular-nums">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-300 truncate" x-text="(data.load.data.phase < 2) ? '{{ $type }} 종료 후 발급됩니다.' : '4자리 숫자'">&nbsp;&nbsp;</dt>
                            <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100 select-all" :class="{ 'blur': data.load.data.phase < 2 }" x-text="(data.load.data.phase < 2) ? 'XXXX' : data.load.data.code">&nbsp;&nbsp;</dd>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex flex-col space-y-2">
                <div class="flex flex-col space-y-2">
                    @if($isMaker || $isAdmin)
                        <template x-if="data.load.data.phase === 0">
                            <x-button.filled.md-white class="w-full" type="button" onclick="location.href='{{ route('mission.read.edit', $mission->id) }}'" x-cloak>
                                {{ $type }} 수정
                            </x-button.filled.md-white>
                        </template>
                        <template x-if="data.load.data.phase === 0">
                            <x-button.filled.md-white class="w-full" type="button" @click="process('START')" x-cloak>
                                {{ $type }} 시작
                            </x-button.filled.md-white>
                        </template>
                        <template x-if="data.load.data.phase === 1">
                            <x-button.filled.md-white class="w-full" type="button" @click="process('END')" x-cloak>
                                {{ $type }} 종료
                            </x-button.filled.md-white>
                        </template>
                    @endif
                </div>
            </div>

            <div class="flex flex-col space-y-2">
                @if(!$isMaker)
                    <template x-if="data.load.data.is_participant && data.load.data.phase === 2" x-cloak>
                        <x-button.filled.md-blue class="w-full" type="button">
                            출석 체크
                        </x-button.filled.md-blue>
                    </template>
                    <template x-if="!data.load.data.is_participant && ((data.load.data.can_tardy && data.load.data.phase === 1) || data.load.data.phase === 0)">
                        <x-button.filled.md-blue class="w-full" type="button" @click="process('JOIN')" x-cloak>
                            참가 신청
                        </x-button.filled.md-blue>
                    </template>
                    <template x-if="data.load.data.is_participant && data.load.data.phase <= 0">
                        <x-button.filled.md-blue class="w-full" type="button" @click="process('LEAVE')" x-cloak>
                            참가 취소
                        </x-button.filled.md-blue>
                    </template>
                @endif

                <x-button.filled.md-white class="w-full" onclick="location.href='{{ route('mission.list') }}'" type="button" >
                    목록
                </x-button.filled.md-white>
            </div>

            <div class="flex flex-col space-y-2">
                <h2 class="text-xl lg:text-2xl font-bold">{{ $type }} 정보</h2>
                <ul class="divide-y divide-gray-200 dark:divide-gray-800">
                    <li class="py-4">
                        <div class="flex justify-between">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                메이커
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $maker->name }}
                            </p>
                        </div>
                    </li>
                    <li class="py-4" x-cloak="" x-show="data.load.data.status.length > 0">
                        <div class="flex justify-between">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                미션 상태
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 tabular-nums" x-text="data.load.data.status"></p>
                        </div>
                    </li>
                    <li class="py-4" x-cloak="" x-show="data.load.data.timestamp.created_at.length > 0">
                        <div class="flex justify-between">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                미션 생성
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 tabular-nums" x-text="data.load.data.timestamp.created_at"></p>
                        </div>
                    </li>
                    <li class="py-4" x-cloak="" x-show="data.load.data.timestamp.started_at.length > 0">
                        <div class="flex justify-between">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                미션 시작
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 tabular-nums" x-text="data.load.data.timestamp.started_at"></p>
                        </div>
                    </li>
                    <li class="py-4" x-cloak="" x-show="data.load.data.timestamp.ended_at.length > 0">
                        <div class="flex justify-between">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                미션 종료
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 tabular-nums" x-text="data.load.data.timestamp.ended_at"></p>
                        </div>
                    </li>
                    <li class="py-4" x-cloak="" x-show="data.load.data.timestamp.closed_at.length > 0">
                        <div class="flex justify-between">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                출석 마감
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 tabular-nums" x-cloak="" x-show="data.load.data.timestamp.closed_at.length > 0" x-text="data.load.data.timestamp.closed_at"></p>
                        </div>
                    </li>
                </ul>
            </div>

            @if($isMaker || $isAdmin)
                <div class="flex flex-col space-y-2">
                    <template x-if="data.load.data.phase === 0">
                        <x-button.filled.md-red class="w-full" type="button" @click="remove()">
                            {{ $type }} 삭제
                        </x-button.filled.md-red>
                    </template>
                    <template x-if="data.load.data.phase === 1">
                        <x-button.filled.md-red class="w-full" type="button" @click="process('CANCEL')">
                            {{ $type }} 취소
                        </x-button.filled.md-red>
                    </template>
                    <template x-if="data.load.data.phase === 3">
                        <x-button.filled.md-blue class="w-full" type="button" onclick="location.href='{{ route('mission.read.report', $mission->id) }}'">
                            설문 결과
                        </x-button.filled.md-blue>
                    </template>
                </div>
            @endif
        </div>
    </div>

    <script type="text/javascript">
        window.document.addEventListener('alpine:init', () => {
            window.alpine.data('mission_read', () => ({
                interval: {
                    load: -1,
                    participants: -1,
                },
                data: {
                    load: {
                        url: '{{route('mission.read.refresh', $mission->id)}}',
                        body: {},
                        data: {
                            phase: '{{ $mission->phase }}',
                            status: '{{ $phase }}',
                            timestamp: {
                                display_date: '{{ $timestamp->format('Y년 m월 d일') }}',
                                display_time: '{{ $timestamp->format('H시 i분') }}',
                                created_at: '{{ $mission->created_at->format('Y-m-d H:i') }}',
                                started_at: '@if(!is_null($mission->started_at)){{ $mission->started_at->format('Y-m-d H:i') }}@endif',
                                ended_at: '@if(!is_null($mission->ended_at)){{ $mission->ended_at->format('Y-m-d H:i') }}@endif',
                                closed_at: '@if(!is_null($mission->closed_at)){{ $mission->closed_at->format('Y-m-d H:i') }}@endif',
                            },
                            code: '{{ $code }}',
                            body: '{!! $mission->body !!}',
                            can_tardy: {{ var_export($mission->can_tardy) }},
                            is_participant: {{ var_export($isParticipant) }},
                            is_edit: false
                        },
                    },
                    participants: {
                        url: '{{ route('mission.read.participants', $mission->id) }}',
                        body: {},
                        data: {
                            participants: []
                        },
                    },
                    remove: {
                        url: '{{ route('mission.delete') }}',
                        body: {
                            mission_id: {{ $mission->id }}
                        },
                        lock: false
                    },
                    process: {
                        url: '{{ route('mission.read.process', $mission->id )}}',
                        body: {
                            type: ''
                        },
                        lock: false
                    }
                },
                remove() {
                    window.modal.confirm('미션 삭제', '정말 삭제하시겠습니까?', (r) => {
                        if (r.isConfirmed) {
                            let success = (r) => {
                                if (r.data.data !== null) {
                                    if (!(typeof r.data.data === 'undefined' || r.data.data.length <= 0)) {
                                        location.href = '{{route('mission.list')}}';
                                    }
                                }
                            }
                            let error = (e) => {
                                if (typeof e.response !== 'undefined') {
                                    if (e.response.status === 415) {
                                        //CSRF 토큰 오류 발생
                                        window.modal.alert('처리 실패', '로그인 정보를 확인할 수 없습니다.', (c) => {
                                            Location.reload();
                                        }, 'error');
                                        return;
                                    }

                                    if (e.response.status === 422) {
                                        let msg = '';
                                        switch (e.response.data.description) {
                                            case "MISSION STATUS DOES'T MATCH THE CONDITIONS":
                                                msg = '현재 미션 상태에서 실행할 수 없는 요청입니다.';
                                                break;
                                            default:
                                                msg = e.response.data.description;
                                                break;
                                        }

                                        window.modal.alert('처리 실패', msg, (c) => {}, 'error');
                                        return;
                                    }
                                }

                                window.modal.alert('처리 실패', '데이터 처리 중 문제가 발생하였습니다.', (c) => {}, 'error');
                                console.log(e);
                            }
                            let complete = () => {
                                this.data.remove.lock = false;
                            }

                            if (!this.data.remove.lock) {
                                this.data.remove.lock = true;
                                this.post(this.data.remove.url, this.data.remove.body, success, error, complete);
                            }
                        }
                    });
                },
                process(type) {
                    this.data.process.body.type = type;

                    let success = (r) => {
                        this.load();
                        this.participants();
                        window.modal.alert('처리 완료', '성공적으로 처리하였습니다.', (c) => {});
                    };

                    let error = (e) => {
                        if (typeof e.response !== 'undefined') {
                            if (e.response.status === 415) {
                                //CSRF 토큰 오류 발생
                                window.modal.alert('처리 실패', '로그인 정보를 확인할 수 없습니다.', (c) => {
                                    Location.reload();
                                }, 'error');
                                return;
                            }

                            if (e.response.status === 422) {
                                let msg = '';

                                switch (e.response.data.description) {
                                    case "MISSION STATUS DOES'T MATCH THE CONDITIONS":
                                        msg = '현재 미션 상태에서 실행할 수 없는 요청입니다.';
                                        break;
                                    default:
                                        msg = e.response.data.description;
                                        break;
                                }

                                window.modal.alert('처리 실패', msg, (c) => {}, 'error');
                                return;
                            }
                        }

                        window.modal.alert('처리 실패', '데이터 처리 중 문제가 발생하였습니다.', (c) => {}, 'error');
                        console.log(e);
                    };

                    let complete = () => {
                        this.data.process.lock = false;
                    };

                    if (!this.data.process.lock) {
                        this.data.process.lock = true;
                        this.post(this.data.process.url, this.data.process.body, success, error, complete);
                    }
                },
                load(update = false) {
                    let success = (r) => {
                        if (r.data.data !== null) {
                            if (!(typeof r.data.data === 'undefined' || r.data.data.length <= 0)) {
                                if (!update) {
                                    if (this.data.load.data.body !== r.data.data.body) {
                                        this.data.load.data.is_edit = true;
                                        delete r.data.data.body;
                                    }

                                } else {
                                    this.data.load.data.is_edit = false;
                                }

                                this.data.load.data = window._.merge(this.data.load.data, r.data.data);

                                if (update) {
                                    window.alpine.nextTick(() => {
                                        window.global.embedly();
                                    });
                                }
                            }
                        }
                    };

                    let error = (e) => {
                        console.log(e);
                    };

                    let complete = () => {};

                    this.post(this.data.load.url, this.data.load.body, success, error, complete);

                    if (this.interval.load === -1) {
                        this.interval.load = setInterval(() => {this.post(this.data.load.url, this.data.load.body, success, error, complete)}, 30000);
                    }
                },
                participants() {
                    let success = (r) => {
                        if (r.data.data !== null) {
                            if (!(typeof r.data.data === 'undefined' || r.data.data.length <= 0)) {
                                this.data.participants.data = r.data.data;
                            }
                        }
                    };

                    let error = (e) => {
                        console.log(e);
                    };

                    let complete = () => {};

                    this.post(this.data.participants.url, this.data.participants.body, success, error, complete);

                    if (this.interval.participants === -1) {
                        this.interval.participants = setInterval(() => {this.post(this.data.participants.url, this.data.participants.body, success, error, complete)}, 30000);
                    }
                },
                init() {
                    this.load();
                    this.participants();
                },
                post(url, body, success, error, complete) {
                    window.axios.post(url, body).then(success).catch(error).then(complete);
                }
            }));
        });

        window.addEventListener('load', function(){
            window.global = {
                embedly() {
                    document.querySelectorAll('oembed[url]').forEach(element => {
                        const anchor = document.createElement('a');

                        anchor.setAttribute('href', element.getAttribute('url'));
                        anchor.className = 'embedly-card';

                        element.appendChild(anchor);
                    });
                }
            }

            window.global.embedly();

            embedly("defaults", {
                cards: {
                    align: 'center',
                }
            });
        });
    </script>
</x-theme.galaxyhub.sub-content>
