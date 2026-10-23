<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Listening</title>

    @vite([
        'resources/css/app.css',
        'resources/css/test/listening/listening.css',
        'resources/js/app.js',
        'resources/js/test/listening/listening.js',
    ])

</head>

<body class="bg-gray-100 font-sans">

    <div class=" mx-auto app-root">

        <div class="app-nav ">
            <div class="flex items-center bg-white p-2 justify-around gap-3   pb-3">
                <div class="min-w-[150px] flex items-center gap-2">
                    @if($is_mock)
                        <a href="{{ route('user.mock-practice', ['id' =>$mock_id]) }}" class="flex h-[24px] w-[44px] sm:h-[44px] sm:w-[54px] border border-gray-200 items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                            <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="/assets/arrow-left.svg" alt="arrow left">
                        </a>
                    @else
                        <a href="{{ route('user.practice-test', ['id' =>$id, 'type' => $type,'difficulty'=>$difficulty ?? 0 ,'time_limit'=>$time_limit?? 0 ,'part_category'=>$part_category_number??0,'title' => $name,'status' => $status]) }}" class="flex h-[24px] w-[44px] sm:h-[44px] sm:w-[54px] border border-gray-200 items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                            <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="/assets/arrow-left.svg" alt="arrow left">
                        </a>
                    @endif
                    <h1 class="text-2xl sm:text-2xl font-bold text-gray-700">Listening</h1>

                </div>

                <div id="partsNav" class="flex items-center justify-center gap-2 flex-1">

                </div>


                <div class="min-w-[220px]  flex justify-end items-center gap-3">
                    @if(auth()->user())
                        @php $user=auth()->user() @endphp
                        <div class="text-2xl sm:text-2xl font-semibold text-gray-800">{{$user->surname}} {{$user->name}} </div>
                    @else
                        <div class="text-2xl sm:text-2xl font-semibold text-gray-800">Muzaffar Abdullayev</div>
                    @endif
                    <a href="{{  $is_mock ? route('user.mock-practice', ['id' =>$mock_id]) : route('user.next-exercise', ['type' => $type,'id'=>$id]) }}"
                       class="inline-flex items-center gap-2
              px-4 py-2
              bg-orange-600 hover:bg-orange-700
              text-white text-sm font-semibold
              rounded-xl
              transition-all duration-200
              shadow-sm hover:shadow-md">

                        Next

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                </div>
            </div>
        </div>

        <div class=" app-main  px-2 flex flex-col lg:flex-row gap-4 h-full m pb-2">

            <div class="flex-1 min-h-0">
                <div id="questionDiv" class="h-full   ">

                    <div id="partOneQuestionDiv"
                        class="hidden   h-full w-full flex flex-col gap-2  overflow-y-auto">

                        <div class="p-2 sm:p-2 rounded-lg bg-white">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div class="text-sm sm:text-base font-bold text-gray-900">PART 1</div>
                            </div>

                            <p id="partOneAdvice" class="mt-2  text-gray-700 "></p>
                        </div>

                        <div class="flex-1 min-h-0 p-2 sm:p-2 ">
                            <div id="partOneGrid" class="grid grid-cols-1 md:grid-cols-2 gap-2 pb-2"></div>
                        </div>

                    </div>

                    <div id="partTwoQuestionDiv"
                         class="hidden rounded-lg h-full w-full flex flex-col gap-2 bg-white overflow-y-auto">

                        <div class="p-2 sm:p-2">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div class="text-sm sm:text-base font-bold text-gray-900">PART 2</div>
                            </div>

                            <p id="partTwoAdvice" class="mt-2 text-gray-700"></p>
                        </div>

                        <div class="p-2 sm:p-4">
                            <p id="partTwoTitle"
                               class="text-center font-bold text-xl sm:text-2xl text-gray-900 tracking-wide">
                                <!-- JS title qo‘yadi -->
                            </p>

                            <!-- JS shu yerga textni inputlarga aylantirib chiqaradi -->
                            <div id="partTwoTextWrap" class="mt-6 space-y-4 text-gray-900"></div>
                        </div>

                    </div>

                    <div id="partThreeQuestionDiv" class="hidden  h-full w-full flex flex-col gap-2 ">

                        <div class="p-2 sm:p-2 rounded-lg bg-white shadow-md">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div class="text-sm sm:text-base font-bold text-gray-900">PART 3</div>
                            </div>

                            <p id="partThreeAdvice" class="mt-2  text-gray-700 ">
                                You will hear four speakers. For each question, choose the correct speaker (A–E) from
                                the drop-down menu.
                            </p>

                            <div class="mt-3 hidden">
                                <audio class="w-full" controls></audio>
                            </div>
                        </div>

                        <div class="flex-1 min-h-0 p-2 sm:p-4">
                            <div id="partThreeGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- JS shu yerga 4 ta card generate qiladi -->
                            </div>
                        </div>

                    </div>

                    <div id="partFourQuestionDiv" class="hidden rounded-lg h-full w-full flex flex-col gap-2 ">

                        <div class="p-2 sm:p-2 bg-white rounded-md shadow-md">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div class="text-sm sm:text-base font-bold text-gray-900">PART 4</div>
                            </div>

                            <p id="partFourAdvice" class="mt-2  text-gray-700 ">
                                Look at the map. Choose the correct option for each question from the drop-down menu.
                            </p>

                            <div class="mt-3 hidden">
                                <audio class="w-full" controls></audio>
                            </div>
                        </div>

                        <div class="flex-1 min-h-0 p-2 sm:p-4">
                            <div
                                class="
                h-full min-h-0 grid grid-cols-1 gap-3
                lg:[grid-template-columns:50%_1fr]
                2xl:[grid-template-columns:55%_1fr]
                3xl:[grid-template-columns:60%_1fr]
            "
                            >

                                <!-- MAP -->
                                <div
                                    id="partFourMapBox"
                                    class="relative h-full min-h-[260px]
           bg-white shadow-md rounded-[18px]
           flex items-center justify-center
           overflow-hidden"
                                >
                                    <!-- TOP-RIGHT TOOLS -->
                                    <div class="absolute top-2 right-2 z-40 flex items-center gap-2">

                                        <!-- COLOR DROPDOWN (SIZNING STYLE) -->
                                        <div id="partFourMapPenColorBox"
                                             class="relative w-full max-w-[180px] rounded-xl bg-white border border-gray-200
            px-2 py-1 flex items-center justify-between cursor-pointer select-none shadow-sm">

                                            <div class="flex items-center gap-2">
                                                <div id="partFourMapPenColorPreview"
                                                     class="w-7 h-5 rounded-md border border-gray-400"
                                                     style="background:#000000">
                                                </div>
                                                <div class="text-xs font-bold text-gray-700">Pen</div>
                                            </div>

                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"></path>
                                            </svg>

                                            <!-- Dropdown -->
                                            <div id="partFourMapPenColorDropdown"
                                                 class="hidden absolute top-[110%] right-1/2 translate-x-1/2
                z-50 w-[240px]
                border-2 border-gray-200 bg-white
                rounded-xl p-3 shadow-lg">

                                                <div class="grid grid-cols-5 gap-2">
                                                    <button type="button" data-color="#000000"
                                                            class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300"
                                                            style="background:#000000" title="Black"></button>

                                                    <button type="button" data-color="#ef4444"
                                                            class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300"
                                                            style="background:#ef4444" title="Red"></button>

                                                    <button type="button" data-color="#22c55e"
                                                            class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300"
                                                            style="background:#22c55e" title="Green"></button>

                                                    <button type="button" data-color="#facc15"
                                                            class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300"
                                                            style="background:#facc15" title="Yellow"></button>

                                                    <button type="button" data-color="#3b82f6"
                                                            class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300"
                                                            style="background:#3b82f6" title="Blue"></button>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- RESET -->
                                        <button type="button" id="partFourMapResetBtn"
                                                class="px-3 py-2 rounded-xl text-xs font-semibold border border-gray-300
                       bg-white text-gray-700 hover:bg-gray-50 active:scale-[0.99]">
                                            Reset
                                        </button>

                                        <!-- FULLSCREEN -->
                                        <button type="button" id="partFourMapFullscreenBtn"
                                                class="p-2 rounded-xl border border-gray-300 bg-white
                       flex items-center justify-center text-gray-700 hover:bg-gray-50 active:scale-[0.99]"
                                                aria-label="Fullscreen">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M15 3h6v6"/>
                                                <path d="M9 21H3v-6"/>
                                                <path d="M21 3l-7 7"/>
                                                <path d="M3 21l7-7"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- IMAGE -->
                                    <img id="partFourMapImg"
                                         src=""
                                         alt="Map"
                                         class="w-full h-full object-contain hidden select-none cursor-crosshair"
                                         draggable="false">

                                    <!-- CANVAS overlay -->
                                    <div id="partFourMapCanvasLayer" class="absolute inset-0 z-30"></div>

                                    <!-- PENCIL HINT -->
                                    <div id="partFourMapPencilHint"
                                         class="pointer-events-none absolute bottom-2 right-2 z-40 hidden
                bg-black/70 text-white text-xs px-2 py-1 rounded-lg backdrop-blur
                flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"/>
                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                        </svg>
                                        <span>Draw</span>
                                    </div>

                                    <span id="partFourMapPlaceholder"
                                          class="text-2xl sm:text-3xl font-bold text-gray-700 tracking-widest select-none">
        MAP
    </span>
                                </div>



                                <!-- QUESTIONS -->
                                <div class="min-h-0 p-3 sm:p-4 overflow-auto">
                                    <div id="partFourList" class="space-y-3">
                                        <!-- JS shu yerga 5 ta question card generate qiladi -->
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>


                    <div id="partFiveQuestionDiv"
                        class="hidden  h-full w-full flex flex-col gap-2  overflow-y-auto">

                        <div class="p-2 sm:p-2 rounded-lg bg-white ">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div class="text-sm sm:text-base font-bold text-gray-900">PART 5</div>
                            </div>

                            <p id="partFiveAdvice" class="mt-2  text-gray-700 ">
                                You will hear three extracts. Choose the correct answer (A, B or C)...
                            </p>

                            <div class="mt-3 hidden">
                                <audio class="w-full" controls></audio>
                            </div>
                        </div>

                        <div class="flex-1 min-h-0 p-2 sm:p-2">
                            <div id="partFiveGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- JS shu yerga 6 ta card generate qiladi -->
                            </div>
                        </div>

                    </div>

                    <div id="partSixQuestionDiv" class="hidden rounded-lg h-full w-full flex flex-col gap-2 bg-white overflow-y-auto">
                        <div class="p-2 sm:p-2">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <div class="text-sm sm:text-base font-bold text-gray-900">PART 6</div>
                            </div>

                            <p id="partSixAdvice" class="mt-2  text-gray-700 ">
                                <!-- JS bu yerga advice qo'yadi -->
                            </p>

                            <div class="mt-3 hidden">
                                <audio class="w-full" controls></audio>
                            </div>
                        </div>

                        <div class="flex-1 min-h-0 p-2 sm:p-4">
                            <p id="partSixTitle"
                                class="text-center font-bold text-xl sm:text-2xl text-gray-900 tracking-wide">
                                <!-- JS title qo'yadi (text 1-qator) -->
                            </p>

                            <div id="partSixTextWrap"
                                class="mt-6 space-y-4 text-gray-900  ">
                                <!-- JS textni shu yerga row qilib chiqaradi -->
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="w-full
                    md:[width:clamp(300px,40vw,420px)]
                    lg:[width:clamp(260px,33vw,360px)]
                    xl:[width:clamp(240px,30vw,345px)]
                    2xl:[width:clamp(220px,22vw,345px)]
                    3xl:[width:clamp(200px,18vw,280px)]  min-h-0">
                <div class="h-full flex flex-col justify-between gap-4">

                    <div class="space-y-2">

                        <!-- AUDIO PANEL -->
                        <!-- AUDIO PANEL -->
                        <div class="bg-white rounded-xl p-3 relative">
                            <!-- countdown overlay (does NOT affect layout size) -->
                            <div id="audioNextCountdown"
                                 class="pointer-events-none hidden absolute -top-2 left-2 z-10
                bg-black/75 text-white text-[11px] px-2 py-1 rounded-full
                backdrop-blur">
                                Next in <span id="audioNextCountdownSec" class="font-bold">15</span>s
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="button" id="audioPlayBtn"
                                        class="w-9 h-9 flex items-center justify-center border border-gray-400 rounded-full hover:bg-gray-100 active:scale-95">
                                    <svg id="playIcon" class="w-4 h-4 text-gray-700" viewBox="0 0 24 24"
                                         fill="currentColor">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>

                                    <svg id="pauseIcon" class="w-4 h-4 text-gray-700 hidden" viewBox="0 0 24 24"
                                         fill="currentColor">
                                        <path d="M6 5h4v14H6zm8 0h4v14h-4z" />
                                    </svg>
                                </button>

                                <input type="range" id="audioProgress" min="0" max="100" value="0"
                                       class="flex-1 h-1 appearance-none bg-gray-300 rounded
               [&::-webkit-slider-thumb]:appearance-none
               [&::-webkit-slider-thumb]:w-3 [&::-webkit-slider-thumb]:h-3
               [&::-webkit-slider-thumb]:rounded-full
               [&::-webkit-slider-thumb]:bg-gray-700
               [&::-webkit-slider-thumb]:cursor-pointer" />

                                <span id="audioTime"
                                      class="text-xs font-semibold text-gray-600 min-w-[42px] text-right">0:00</span>
                            </div>

                            <!-- Part audios (hidden) -->
                            <div class="hidden">
                                <audio id="audioPlayerPartOne" preload="auto"></audio>
                                <audio id="audioPlayerPartTwo" preload="auto"></audio>
                                <audio id="audioPlayerPartThree" preload="auto"></audio>
                                <audio id="audioPlayerPartFour" preload="auto"></audio>
                                <audio id="audioPlayerPartFive" preload="auto"></audio>
                                <audio id="audioPlayerPartSix" preload="auto"></audio>
                            </div>
                        </div>






                        <div class="w-full bg-white p-1 flex items-center  gap-4">
                            <!-- FLAG ICON -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="shrink-0">
                                <path
                                    d="M4 22V4a1 1 0 0 1 .4-.8A6 6 0 0 1 8 2c3 0 5 2 7.333 2q2 0 3.067-.8A1 1 0 0 1 20 4v10a1 1 0 0 1-.4.8A6 6 0 0 1 16 16c-3 0-5-2-8-2a6 6 0 0 0-4 1.528" />
                            </svg>
                            <button onclick="openListeningFinishModal()" type="button" class="
            w-full
            flex items-center gap-3
            text-center
            bg-[#06b6d4]
            hover:bg-[#05a6c2]
            px-2 py-1
            font-bold
            text-white

            transition
            active:scale-[0.99]
        ">
                                <!-- TEXT -->
                                <span class="flex-1 text-center">
                                    Yakunlash
                                </span>
                            </button>
                        </div>

                        <div class="space-y-2">
                            <div class="flex gap-2 items-stretch">
                                <div class="w-1/2 flex items-center gap-3 rounded-xl bg-white px-3 py-2">
                                    <svg class="w-5 h-5 text-gray-700 flex-shrink-0" viewBox="0 0 24 24"
                                         fill="currentColor">
                                        <path d="M3 9v6h4l5 5V4L7 9H3z" />
                                        <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.74 2.5-2.26 2.5-4.02z" />
                                        <path
                                            d="M14 3.23v2.06c2.89 1 5 3.77 5 6.71s-2.11 5.71-5 6.71v2.06c4.01-1.07 7-4.67 7-8.77s-2.99-7.7-7-8.77z" />
                                    </svg>

                                    <input id="audioVolume" type="range" min="0" max="100" value="65" class="w-full h-1 appearance-none bg-gray-300 rounded
               [&::-webkit-slider-thumb]:appearance-none
               [&::-webkit-slider-thumb]:w-3
               [&::-webkit-slider-thumb]:h-3
               [&::-webkit-slider-thumb]:rounded-full
               [&::-webkit-slider-thumb]:bg-gray-700
               [&::-webkit-slider-thumb]:cursor-pointer
               [&::-moz-range-thumb]:w-3
               [&::-moz-range-thumb]:h-3
               [&::-moz-range-thumb]:rounded-full
               [&::-moz-range-thumb]:bg-gray-700" />
                                </div>

                                <div class="w-1/2">
                                    <div id="penColorBox"
                                         class="relative h-full rounded-xl bg-white px-3 py-2 flex items-center justify-between cursor-pointer select-none">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <div id="penColorPreview"
                                                 class="w-7 h-5 rounded-md border border-gray-400 bg-white flex-shrink-0"></div>
                                            <div class="text-xs font-bold text-gray-700">Pen</div>
                                        </div>

                                        <svg class="w-4 h-4 text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>

                                        <div id="penColorDropdown"
                                             class="hidden absolute right-0 top-[110%] z-50 w-full border-2 border-gray-200 bg-white rounded-lg p-2 shadow-md">
                                            <div class="grid grid-cols-2 gap-2">
                                                <button type="button" data-color=""
                                                        class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300 bg-white justify-self-center"
                                                        title="Default (remove)"></button>

                                                <button type="button" data-color="#fff200"
                                                        class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300 justify-self-center"
                                                        style="background:#fff200" title="Yellow"></button>

                                                <button type="button" data-color="#9be7ff"
                                                        class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300 justify-self-center"
                                                        style="background:#9be7ff" title="Blue"></button>

                                                <button type="button" data-color="#b7f7c4"
                                                        class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300 justify-self-center"
                                                        style="background:#b7f7c4" title="Green"></button>

                                                <button type="button" data-color="#ffd6a5"
                                                        class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300 justify-self-center"
                                                        style="background:#ffd6a5" title="Orange"></button>

                                                <button type="button" data-color="#fbcfe8"
                                                        class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300 justify-self-center"
                                                        style="background:#fbcfe8" title="Pink"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full flex gap-2">
                                <button onclick="minusText()" type="button"
                                        class="w-1/2 rounded-xl px-3 py-2 sm:py-1 bg-white font-bold text-sm">
                                    A-
                                </button>
                                <button onclick="plusText()" type="button"
                                        class="w-1/2 rounded-xl px-3 py-2 sm:py-1 bg-white font-bold text-sm">
                                    A+
                                </button>
                            </div>
                        </div>


                        {{-- listening.blade.php --}}
                        <div id="focusQuestionWrap"></div>



                    </div>

                    <div id="videoBox" class="rounded-lg hidden  bg-white h-[22%] lg:h-[28%] relative overflow-hidden">
                        <div
                            class="absolute top-1 right-1 z-10 flex items-center gap-2 bg-white/80 border border-gray-300 px-2 py-1 rounded-full">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input id="cameraToggle" type="checkbox" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-300 rounded-full peer peer-checked:bg-gray-500 transition">
                                </div>
                                <div
                                    class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full peer-checked:translate-x-4 transition">
                                </div>
                            </label>
                        </div>

                        <video id="cameraPreview" class="absolute inset-0 w-full h-full object-cover hidden" autoplay
                            muted playsinline>
                        </video>

                        <span id="videoPlaceholder"
                            class="absolute inset-0 flex items-center justify-center text-xl sm:text-2xl text-gray-700 font-bold tracking-widest">
                            Video
                        </span>
                    </div>

                </div>
            </div>

        </div>

        <div id="audio-loading-overlay"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] hidden">
            <div class="flex flex-col items-center justify-center text-center">
                <div class="mb-6">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-white"></div>
                </div>
                <div class="text-white text-lg font-medium">Loading audio files...</div>
                <div class="text-white text-sm mt-2 opacity-75">Please wait while we prepare your listening test</div>
                <div id="audio-loading-progress" class="text-white text-xs mt-3 opacity-75">0/0</div>
            </div>
        </div>

        <div id="finishModal"
             class="fixed inset-0 z-50 hidden items-center justify-center bg-black/55 p-4
            opacity-0 transition-opacity duration-200">

            <div id="finishModalCard"
                 class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl
                transform transition-transform duration-200 scale-95 translate-y-2">

                <!-- HEADER -->
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Finish test?</h2>
                    <button type="button"
                            onclick="window.cancelFinishModal()"
                            class="text-gray-400 hover:text-gray-700 text-2xl leading-none select-none"
                            aria-label="Close">
                        &times;
                    </button>
                </div>

                <!-- BODY -->
                <div id="finishModalBody" class="px-6 py-4 space-y-3 max-h-[65vh] overflow-y-auto">
                    <!-- JS accordion shu yerga keladi -->
                </div>

                <!-- FOOTER -->
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200">
                    <button id="finishCancelBtn"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button id="finishAcceptBtn"
                            class="px-5 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg text-sm font-semibold">
                        Accept & Finish
                    </button>
                </div>
            </div>
        </div>


        {{-- draft alert --}}
        <div id="draftAlert"
             class="fixed top-4 right-4 bg-white text-black border border-gray-200 p-5 rounded-xl shadow-lg flex items-center gap-3 z-50 transition-all duration-300 ease-out transform opacity-0 -translate-y-5 hidden max-w-xl">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between w-full gap-3">
                <p class="font-medium text-base w-70" id="AcceptCancelAlertMessage"></p>
                <div class="flex space-x-2">
                    <button id="acceptDraftBtn"
                            class="px-4 py-2 bg-[#06b6d4] hover:bg-[#05a6c2] text-white rounded-lg  transition-colors font-medium text-sm">
                        Accept
                    </button>
                    <button id="cancelDraftBtn"
                            class="px-4 py-2 border  bg-gray-100 text-[#333333] rounded-lg hover:bg-gray-100 transition-colors font-medium text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- MAP PREVIEW MODAL -->
        <div id="mapModal"
             class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/70 p-3 sm:p-6">
            <button id="mapModalClose"
                    class="absolute top-4 right-4 text-white/90 text-3xl leading-none select-none">
                &times;
            </button>

            <div class="max-h-[90vh] max-w-[95vw] min-w-[70vh]">
                <img id="mapModalImg"
                     src=""
                     alt="Map preview"
                     class="max-h-[90vh] max-w-[95vw] min-w-[70vh] object-contain rounded-xl shadow-2xl bg-white" />
            </div>
        </div>

        <form id="listening-form" method="POST" action="{{ route('user.listening.store', $id) }}"
            style="display: none;">
            @csrf
            <input type="hidden" id="listening-draft-input" name="listening_draft" value="">
        </form>



        {{-- Backend Data --}}
        <div id="BackendData">
            <input type="hidden" id="exercise_id" value="{{$id ?? 0}}">
            <input type="hidden" id="time_limit" value="{{$time_limit ?? 60}}">
            <input type="hidden" id="asset_url" value="{{$asset_url ?? 60}}">
            <input type="hidden" id="audio_control" value="{{$audio_control}}">
            <input type="hidden" id="is_mock" value="{{$is_mock}}">
            <input type="hidden" id="part_one" value="{{json_encode($part_one)}}">
            <input type="hidden" id="part_two" value="{{json_encode($part_two)}}">
            <input type="hidden" id="part_three" value="{{json_encode($part_three)}}">
            <input type="hidden" id="part_four" value="{{json_encode($part_four)}}">
            <input type="hidden" id="part_five" value="{{json_encode($part_five)}}">
            <input type="hidden" id="part_six" value="{{json_encode($part_six)}}">
        </div>


    </div>

    <script>

    </script>


</body>

</html>
