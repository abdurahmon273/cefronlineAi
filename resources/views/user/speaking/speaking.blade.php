<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Speaking</title>

    @vite([
    'resources/css/app.css',
    'resources/css/test/speaking/speaking.css',
    'resources/js/app.js',
    'resources/js/test/speaking/speaking.js'
        ])
</head>

<body class="bg-gray-100 font-sans">

<div class=" mx-auto app-root">

    <div class="app-nav ">
        <div class="flex items-center bg-white py-3  px-3 justify-between gap-3   pb-3">

            <div class="min-w-[150px] flex items-center gap-2">
                @if($is_mock && $speakingExercise->full_exercise    )
                    <a href="{{ route('user.mock-practice', ['id' =>$mock_id]) }}" class="flex h-[24px] w-[44px] sm:h-[44px] sm:w-[54px] border border-gray-200 items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                        <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="/assets/arrow-left.svg" alt="arrow left">
                    </a>
                @else
                    <a href="{{ route('user.practice-test', ['id' => $speakingExercise->id, 'type' => $type,'difficulty'=>$speakingExercise->difficulty ?? 0 ,'time_limit'=>$speakingExercise->time_limit?? 0 ,'part_category'=>$speakingExercise->part_category??0,'title'=>$speakingExercise->title ?? 0,'status' => $speakingExercise->status]) }}" class="flex h-[24px] w-[44px] sm:h-[44px] sm:w-[54px] border border-gray-200 items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                        <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="/assets/arrow-left.svg" alt="arrow left">
                    </a>
                @endif
                <h1 class="text-2xl sm:text-2xl font-bold text-gray-700">Speaking</h1>

            </div>
            <div class="px-3">
                <div id="partsNav" class="flex items-center justify-center gap-2 flex-1"></div>
            </div>

            <div class="min-w-[220px] flex justify-end items-center gap-3">

                @if(auth()->user())
                    @php $user=auth()->user() @endphp
                    <div class="text-2xl sm:text-2xl font-semibold text-gray-800">{{$user->surname}} {{$user->name}} </div>
                @else
                    <div class="text-2xl sm:text-2xl font-semibold text-gray-800">Muzaffar Abdullayev</div>
                @endif

                <a href="{{ ( $is_mock && $speakingExercise->full_exercise ) ? route('user.mock-practice', ['id' =>$mock_id]) : route('user.next-exercise', ['type' => \App\Enums\CefrTypeEnum::SPEAKING->value,'id'=>$id]) }}"
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

    <div class=" app-main px-8 py-2 flex flex-col lg:flex-row gap-4 h-full m pb-2 space-x-10">

        <div class="flex-1 min-h-0">
            <div id="questionDiv" class="h-full flex flex-col overflow-hidden">


                <div id="spPart1Container" class="h-full flex flex-col hidden">
                    <div class="part-block-div  rounded-lg border-b border-gray-200 p-2 sm:p-2 flex items-center justify-between">
                        <div class="text-sm sm:text-lg font-bold text-gray-800 p-3" id="part-one-title-div">PART 1.1</div>
                        <x-test.speaking-text-size></x-test.speaking-text-size>
                    </div>

                    <div id="partOneQuestionDiv" class="mt-5 rounded-lg border-b border-gray-200 sm:p-6 bg-white">
                    </div>
                    <div id="spPart1ControlsHost" class="mt-5 flex justify-center items-center"></div>
                </div>

                <div id="spPart2Container" class="h-full flex flex-col hidden">
                    <div class="part-block-div rounded-lg border-b border-gray-200 p-2 sm:p-2 flex items-center justify-between">
                        <div class="text-sm sm:text-lg font-bold text-gray-800 p-3">PART 2</div>
                        <x-test.speaking-text-size></x-test.speaking-text-size>
                    </div>

                    <div id="partTwoQuestionDiv" class="mt-5 rounded-lg  flex flex-col overflow-hidden">
                    </div>
                    <div id="spPart2ControlsHost" class="mt-5 flex justify-center items-center"></div>
                </div>

                <div id="spPart3Container" class="h-full flex flex-col hidden">
                    <div class="part-block-div rounded-lg border-b border-gray-200 p-2 sm:p-2 flex items-center justify-between">
                        <div class="text-sm sm:text-lg font-bold text-gray-800 p-3">PART 3</div>
                        <x-test.speaking-text-size></x-test.speaking-text-size>
                    </div>

                    <div id="partThreeQuestionDiv" class="mt-5 rounded-lg  flex flex-col h-full overflow-hidden">
                    </div>
                </div>


            </div>
        </div>

        <div class="lg:w-[26%] min-h-0">
            <div class="h-full flex flex-col gap-4">

                <div class="flex flex-col gap-2">


                    <div class="bg-white rounded-lg shadow-md p-4 text-center">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-base font-bold text-gray-800">Timer</div>

                            <!-- status dot (JS classlar qo‘shadi) -->
                            <div class="flex items-center gap-2">
                                <span id="timerStatusText" class="text-xs font-semibold text-gray-500">IDLE</span>

                                <!-- dot + ping (record paytida ping ishlaydi) -->
                                <span class="relative inline-flex h-4 w-4">
                <span id="recordPing" class="hidden absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75 animate-ping"></span>
                <span id="recordDot" class="relative inline-flex rounded-full h-4 w-4 bg-gray-400"></span>
            </span>
                            </div>
                        </div>

                        <div id="timeBig" class="text-3xl font-extrabold text-gray-900 tracking-wider">00:00</div>

                        <div id="timerSubText" class="mt-2 text-xs font-semibold text-gray-500">
                            Ready
                        </div>

                        <!-- Optional: current question indicator -->
                        <div class="mt-2 text-xs text-gray-400 hidden">
                            <span id="timerQuestionLabel">Question -</span>
                        </div>

                        <!-- START/STOP debug (xohlasangiz hidden qiling) -->
                        <div class=" hidden mt-3 flex justify-center gap-2">
                            <button id="spStartBtn" type="button"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-700 text-white hover:bg-gray-800">
                                Start
                            </button>
                            <button id="spStopBtn" type="button"
                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                                Stop
                            </button>
                        </div>
                    </div>


                </div>

                <div class="bg-white rounded-lg shadow-md p-6 text-center min-h-[200px] flex items-center justify-center relative overflow-hidden">
                    <canvas id="voiceWaveCanvas" class="w-full h-full absolute inset-0"></canvas>
                </div>

                <div class="hidden lg:block"></div>
            </div>
        </div>



    </div>

    <div id="videoBox"
         class="fixed hidden bottom-4 right-4 z-[9999] rounded-xl bg-white shadow-lg overflow-hidden
            w-[280px] h-[190px]
            sm:w-[320px] sm:h-[210px]
            lg:w-[320px] lg:h-[210px]">

        <div class="absolute top-2 right-2 z-10 flex items-center gap-2 bg-white/80 border border-gray-200 px-2 py-1 rounded-full">
            <label class="relative inline-flex items-center cursor-pointer">
                <input id="cameraToggle" type="checkbox" class="sr-only peer">
                <div class="w-9 h-5 bg-gray-300 rounded-full peer peer-checked:bg-gray-500 transition"></div>
                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full peer-checked:translate-x-4 transition"></div>
            </label>
        </div>

        <video id="cameraPreview"
               class="absolute inset-0 w-full h-full object-cover hidden"
               autoplay muted playsinline></video>

        <span id="videoPlaceholder"
              class="absolute inset-0 flex items-center justify-center text-xl sm:text-2xl text-gray-700 font-bold tracking-widest">
        Video
    </span>
    </div>

    <div id="draftAlert"
         class="fixed top-4 right-4 bg-gray-100 text-black border border-gray-200 p-5 rounded-xl shadow-lg flex items-center gap-3 z-50 transition-all duration-300 ease-out transform opacity-0 -translate-y-5 hidden max-w-xl">
        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between w-full gap-3">
            <p class="font-medium text-base w-70" id="AcceptCancelAlertMessage"></p>
            <div class="flex space-x-2">
                <button id="acceptDraftBtn"
                        class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition-colors font-medium text-sm">
                    Accept
                </button>
                <button id="cancelDraftBtn"
                        class="px-4 py-2 border border-gray-400 bg-gray-100 text-[#333333] rounded-lg hover:bg-gray-100 transition-colors font-medium text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <div id="finishModal"
         class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/40 p-4 opacity-0 transition-opacity duration-200">

        <div id="finishModalCard"
             class="w-full max-w-2xl bg-white rounded-2xl shadow-xl transform scale-95 translate-y-2 transition-all duration-200 overflow-hidden">

            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-[#06b6d4]/15 flex items-center justify-center text-[#06b6d4] font-extrabold text-lg">
                        ✓
                    </div>
                    <p class="text-xl font-extrabold text-gray-900">Yakunlash</p>
                </div>

                <!-- ✅ close bor -->
                <button id="finishCloseX"
                        type="button"
                        class="w-10 h-10 hidden rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-600 text-lg">

                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5">
                <div class="flex items-center justify-between mb-4">
                    <p class="font-extrabold text-gray-900 text-base">Partlar holati</p>
                    <span id="finishTotalBadge"
                          class="text-sm font-extrabold px-3 py-1 rounded-full bg-gray-100 text-gray-800">
                    0/0 Done
                </span>
                </div>

                <div id="finishPartsList" class="space-y-3">
                    <!-- JS render qiladi -->
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-3">
                <!-- With AI toggle -->
                <div class="flex items-center gap-3">
                    <span class="text-sm font-extrabold text-gray-800">With AI</span>

                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input id="finishWithAiToggle" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-[#06b6d4] transition"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full peer-checked:translate-x-5 transition"></div>
                    </label>
                </div>

                <!-- ✅ Cancel + Accept -->
                <div class="flex items-center gap-2">
                    <button id="finishCancelBtn"
                            type="button"
                            class="px-5 py-2.5 rounded-xl  text-gray-700 font-extrabold hover:bg-gray-50">

                    </button>

                    <button id="finishAcceptBtn"
                            type="button"
                            class="px-6 py-2.5 rounded-xl bg-[#06b6d4] hover:bg-[#05a6c2] text-white font-extrabold">
                        Accept
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="speaking-form"
          method="POST"
          action="{{ route('user.speaking.store', $id) }}"
          style="display:none;"
          enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="speaking-draft-input" name="speaking_draft" value="">
        <input type="hidden" id="speaking-with-ai-input" name="with_ai" value="0">
        <div id="speaking-files-host"></div>
    </form>

    <div id="audio-loading-overlay"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] hidden">
        <div class="flex flex-col items-center justify-center text-center">
            <div class="mb-6">
                <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-white"></div>
            </div>
            <div class="text-white text-lg font-medium">Speaking audio files...</div>
            <div class="text-white text-sm mt-2 opacity-75">Please wait while we prepare your Speaking test</div>
            <div id="audio-loading-progress" class="text-white text-xs mt-3 opacity-75">0/0</div>
        </div>
    </div>

    <div id="finish-loading-overlay"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] hidden">
        <div class="flex flex-col items-center justify-center text-center">
            <div class="mb-6">
                <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-white"></div>
            </div>
            <div class="text-white text-base font-medium">Please wait, your response is uploading…</div>
            <div class="text-white text-xs opacity-75 mt-1"></div>
        </div>
    </div>

    <div id="BackendData">
        <input type="hidden" id="exercise_id" value="{{ $id ?? 0 }}">

        <input type="hidden" id="audio_instructions" value='@json($audioInstructionsData ?? null)'>

        <input type="hidden" id="part_one_first"  value='@json($part_one_first ?? null)'>
        <input type="hidden" id="part_one_second" value='@json($part_one_second ?? null)'>
        <input type="hidden" id="part_two"       value='@json($part_two ?? null)'>
        <input type="hidden" id="part_three"     value='@json($part_three ?? null)'>
        <input type="hidden" id="is_mock"     value="{{$is_mock}}">
    </div>

</div>

</body>
</html>
