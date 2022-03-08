<?php

namespace App\View\Components\Theme\Galaxyhub;

use App\Enums\PermissionType;
use App\Models\User;
use App\Repositories\User\Interfaces\UserBadgeRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SubBasicsAccount extends Component
{
    public string $title;
    public string $description;
    public string $class;
    public User $user;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $title = '', string $description = '', string $class = '', User $user = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->class = $class;
        $this->user = $user;
    }

    public function render(): View
    {
        $badges = $this->user->badges()->with('badge')->get();

        $isMember = $this->user->hasAnyPermission([
            PermissionType::MEMBER->name,
            PermissionType::MAKER1->name,
            PermissionType::MAKER2->name,
            PermissionType::ADMIN->name
        ]);

        $menu = array();

        if ($isMember)
        {
            $menu = array_merge($menu, [
                '개인 정보' => [
                    'url' => route('account.me'),
                    'icon' => '
                        <svg class="text-center text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300 flex-shrink-0 -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M272 304h-96C78.8 304 0 382.8 0 480c0 17.67 14.33 32 32 32h384c17.67 0 32-14.33 32-32C448 382.8 369.2 304 272 304zM48.99 464C56.89 400.9 110.8 352 176 352h96c65.16 0 119.1 48.95 127 112H48.99zM224 256c70.69 0 128-57.31 128-128c0-70.69-57.31-128-128-128S96 57.31 96 128C96 198.7 153.3 256 224 256zM224 48c44.11 0 80 35.89 80 80c0 44.11-35.89 80-80 80S144 172.1 144 128C144 83.89 179.9 48 224 48z"/>
                        </svg>
                    ',
                ],
                '장기 미접속' => [
                    'url' => route('account.pause'),
                    'icon' => '
                        <svg class="text-center text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300 flex-shrink-0 -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor">
                            <path d="M224 288c13.25 0 24-14.37 24-31.1S237.3 224 224 224S200 238.4 200 256S210.8 288 224 288zM288 0C285.5 0 282.9 .382 280.3 1.007l-192 49.75C73.1 54.51 64 67.76 64 82.88V464H24C10.75 464 0 474.7 0 488C0 501.3 10.75 512 24 512H288c17.67 0 32-14.33 32-32V33.13C320 14.38 305.3 0 288 0zM272 464h-160V94.13l160-41.38V464zM552 464H512V128c0-35.35-28.65-64-64-64l-96 .0061V112h96c8.836 0 16 7.162 16 16v352c0 17.67 14.33 32 32 32h56c13.25 0 24-10.75 24-24C576 474.7 565.3 464 552 464z"/>
                        </svg>
                    ',
                ],
                '신청한 미션' => [
                    'url' => route('account.missions'),
                    'icon' => '
                        <svg class="text-center text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300 flex-shrink-0 -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor">
                            <path d="M400 272c-17.62 0-32 14.38-32 32s14.38 32 32 32s32-14.38 32-32S417.6 272 400 272zM247.1 232h-32v-32c0-13.2-10.78-24-23.98-24c-13.2 0-24.02 10.8-24.02 24v32L136 231.1C122.8 231.1 111.1 242.8 111.1 256c0 13.2 10.85 23.99 24.05 23.99L167.1 280v32c0 13.2 10.82 24 24.02 24c13.2 0 23.98-10.8 23.98-24v-32h32c13.2 0 24.02-10.8 24.02-24C271.1 242.8 261.2 232 247.1 232zM464 176c-17.62 0-32 14.38-32 32s14.38 32 32 32s32-14.38 32-32S481.6 176 464 176zM448 64H192C85.96 64 0 149.1 0 256s85.96 192 192 192h256c106 0 192-85.96 192-192S554 64 448 64zM448 400H192c-79.4 0-144-64.6-144-144S112.6 112 192 112h256c79.4 0 144 64.6 144 144S527.4 400 448 400z"/>
                        </svg>
                    ',
                ],
            ]);
        }

        $menu = array_merge($menu, [
            '데이터 삭제' => [
                'url' => route('account.leave'),
                'icon' => '
                    <svg class="text-center text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300 flex-shrink-0 -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                       <path d="M424 80C437.3 80 448 90.75 448 104C448 117.3 437.3 128 424 128H412.4L388.4 452.7C385.9 486.1 358.1 512 324.6 512H123.4C89.92 512 62.09 486.1 59.61 452.7L35.56 128H24C10.75 128 0 117.3 0 104C0 90.75 10.75 80 24 80H93.82L130.5 24.94C140.9 9.357 158.4 0 177.1 0H270.9C289.6 0 307.1 9.358 317.5 24.94L354.2 80H424zM177.1 48C174.5 48 171.1 49.34 170.5 51.56L151.5 80H296.5L277.5 51.56C276 49.34 273.5 48 270.9 48H177.1zM364.3 128H83.69L107.5 449.2C108.1 457.5 115.1 464 123.4 464H324.6C332.9 464 339.9 457.5 340.5 449.2L364.3 128z"/>
                    </svg>
                ',
            ],
            '버전 정보' => [
                'url' => route('account.versions'),
                'icon' => '
                    <svg class="text-center text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300 flex-shrink-0 -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor">
                        <path d="M119.1 176C119.1 153.9 137.9 136 159.1 136C182.1 136 199.1 153.9 199.1 176C199.1 198.1 182.1 216 159.1 216C137.9 216 119.1 198.1 119.1 176zM207.3 23.36L213.9 51.16C223.4 55.3 232.4 60.5 240.6 66.61L267.7 58.52C273.2 56.85 279.3 58.3 283.3 62.62C299.5 80.44 311.9 101.8 319.3 125.4C321.1 130.9 319.3 136.9 315.1 140.9L294.1 160C295.7 165.5 295.1 171.1 295.1 176.8C295.1 181.1 295.7 187.1 295.2 192.1L315.1 211.1C319.3 215.1 321.1 221.1 319.3 226.6C311.9 250.2 299.5 271.6 283.3 289.4C279.3 293.7 273.2 295.2 267.7 293.5L242.1 285.8C233.3 292.5 223.7 298.1 213.5 302.6L207.3 328.6C205.1 334.3 201.7 338.8 196 340.1C184.4 342.6 172.4 344 159.1 344C147.6 344 135.6 342.6 123.1 340.1C118.3 338.8 114 334.3 112.7 328.6L106.5 302.6C96.26 298.1 86.67 292.5 77.91 285.8L52.34 293.5C46.75 295.2 40.65 293.7 36.73 289.4C20.5 271.6 8.055 250.2 .6513 226.6C-1.078 221.1 .6929 215.1 4.879 211.1L24.85 192.1C24.29 187.1 24 181.1 24 176.8C24 171.1 24.34 165.5 25.01 160L4.879 140.9C.6936 136.9-1.077 130.9 .652 125.4C8.056 101.8 20.51 80.44 36.73 62.62C40.65 58.3 46.75 56.85 52.34 58.52L79.38 66.61C87.62 60.5 96.57 55.3 106.1 51.17L112.7 23.36C114 17.71 118.3 13.17 123.1 11.91C135.6 9.35 147.6 8 159.1 8C172.4 8 184.4 9.35 196 11.91C201.7 13.16 205.1 17.71 207.3 23.36L207.3 23.36zM63.1 176.8C63.1 180.5 64.21 184.1 64.6 187.7L66.79 207.4L44.25 228.9C47.68 236.5 51.84 243.7 56.63 250.4L85.96 241.7L102.2 254C108.4 258.7 115.1 262.7 122.3 265.8L140.8 273.8L147.8 303.4C151.8 303.8 155.9 304 159.1 304C164.1 304 168.2 303.8 172.2 303.4L179.2 273.8L197.7 265.8C204.9 262.7 211.6 258.7 217.8 254L234 241.7L263.4 250.4C268.2 243.7 272.3 236.5 275.7 228.9L253.2 207.4L255.4 187.7C255.8 184.1 255.1 180.5 255.1 176.8C255.1 172.7 255.8 168.7 255.3 164.8L252.9 144.9L275.7 123.1C272.3 115.5 268.2 108.3 263.4 101.6L232.9 110.7L216.8 98.74C210.1 94.42 204.7 90.76 197.1 87.85L179.6 79.87L172.2 48.58C168.2 48.2 164.1 48 159.1 48C155.9 48 151.8 48.2 147.8 48.58L140.4 79.87L122 87.85C115.3 90.76 109 94.42 103.2 98.74L87.1 110.7L56.63 101.6C51.84 108.3 47.68 115.5 44.25 123.1L67.14 144.9L64.72 164.8C64.25 168.7 63.1 172.7 63.1 176.8L63.1 176.8zM464 312C486.1 312 504 329.9 504 352C504 374.1 486.1 392 464 392C441.9 392 424 374.1 424 352C424 329.9 441.9 312 464 312zM581.5 244.3L573.4 271.4C579.5 279.6 584.7 288.6 588.8 298.1L616.6 304.7C622.3 306 626.8 310.3 628.1 315.1C630.6 327.6 632 339.6 632 352C632 364.4 630.6 376.4 628.1 388C626.8 393.7 622.3 397.1 616.6 399.3L588.8 405.9C584.7 415.4 579.5 424.4 573.4 432.6L581.5 459.7C583.2 465.3 581.7 471.4 577.4 475.3C559.6 491.5 538.2 503.9 514.6 511.4C509.1 513.1 503.1 511.3 499.1 507.1L479.1 486.1C474.5 487.7 468.9 488 463.2 488C458 488 452.9 487.7 447.9 487.2L428.9 507.1C424.9 511.3 418.9 513.1 413.4 511.4C389.8 503.9 368.4 491.5 350.6 475.3C346.3 471.4 344.8 465.3 346.5 459.7L354.2 434.1C347.5 425.3 341.9 415.7 337.4 405.5L311.4 399.3C305.7 397.1 301.2 393.7 299.9 388C297.3 376.4 295.1 364.4 295.1 352C295.1 339.6 297.4 327.6 299.9 315.1C301.2 310.3 305.7 306 311.4 304.7L337.4 298.5C341.9 288.3 347.5 278.7 354.2 269.9L346.5 244.3C344.8 238.8 346.3 232.7 350.6 228.7C368.4 212.5 389.8 200.1 413.4 192.7C418.9 190.9 424.9 192.7 428.9 196.9L447.9 216.9C452.9 216.3 458 216 463.2 216C468.9 216 474.5 216.4 479.1 217L499.1 196.9C503.1 192.7 509.1 190.9 514.6 192.7C538.2 200.1 559.6 212.5 577.4 228.7C581.7 232.7 583.2 238.8 581.5 244.3V244.3zM463.2 256C459.5 256 455.9 256.2 452.3 256.6L432.6 258.8L411.1 236.3C403.5 239.7 396.3 243.8 389.6 248.6L398.3 277.1L385.1 294.2C381.3 300.4 377.3 307.1 374.2 314.3L366.2 332.8L336.6 339.8C336.2 343.8 336 347.9 336 352C336 356.1 336.2 360.2 336.6 364.2L366.2 371.2L374.2 389.7C377.3 396.9 381.3 403.6 385.1 409.8L398.3 426L389.6 455.4C396.3 460.2 403.5 464.3 411.1 467.8L432.6 445.2L452.3 447.4C455.9 447.8 459.5 448 463.2 448C467.3 448 471.3 447.8 475.2 447.3L495.1 444.9L516.9 467.8C524.5 464.3 531.7 460.2 538.4 455.4L529.3 424.9L541.3 408.8C545.6 402.1 549.2 396.7 552.2 389.1L560.1 371.6L591.4 364.2C591.8 360.2 592 356.1 592 352C592 347.9 591.8 343.8 591.4 339.8L560.1 332.4L552.2 314C549.2 307.4 545.6 301 541.3 295.2L529.3 279.1L538.4 248.6C531.7 243.8 524.5 239.7 516.9 236.3L495.1 259.1L475.2 256.7C471.3 256.3 467.3 256 463.2 256V256z"/>
                    </svg>
                ',
            ],
        ]);

        if (array_key_exists($this->title, $menu))
        {
            $menu[$this->title]['url'] = '#';
        }

        return view('components.theme.galaxyhub.sub-basics-account', [
            'isMember' => $isMember,
            'menu' => $menu,
            'badges' => $badges
        ]);
    }
}
