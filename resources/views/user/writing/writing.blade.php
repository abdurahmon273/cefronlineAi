<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Writing</title>
    <link rel="stylesheet" href="{{ asset('vendor/jodit/jodit.min.css') }}">

    @vite([
        'resources/css/app.css',
        'resources/css/test/writing/writing.css',
        'resources/js/app.js',
        'resources/js/test/writing/writing.js'
    ])
</head>

<body class="bg-gray-100 font-sans">
<div class="mx-auto app-root">

    <div class="app-nav">
        <div class="flex items-center bg-white p-3 justify-between gap-3 pb-3">
            <div class="min-w-[150px] flex items-center gap-2">
                @if($is_mock)
                    <a href="{{ route('user.mock-practice', ['id' =>$mock_id]) }}" class="flex h-[24px] w-[44px] sm:h-[44px] sm:w-[54px] border border-gray-200 items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                        <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="/assets/arrow-left.svg" alt="arrow left">
                    </a>
                @else
                    <a href="{{ route('user.practice-test', ['id' => $writingExercise->id, 'type' => $type,'difficulty'=>$writingExercise->difficulty ?? 0 ,'time_limit'=>$writingExercise->time_limit?? 0 ,'part_category'=>$writingExercise->part_category??0,'title' => $writingExercise->title??0,'status' => $writingExercise->status]) }}" class="flex h-[24px] w-[44px] sm:h-[44px] sm:w-[54px] border border-gray-200 items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                     <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="/assets/arrow-left.svg" alt="arrow left">
                    </a>
                @endif
                <h1 class="text-2xl sm:text-2xl font-bold text-gray-700">Writing</h1>
            </div>

            <div class="min-w-[220px] flex justify-end items-center gap-3">


                @if(auth()->user())
                    @php $user=auth()->user() @endphp
                    <div class="text-2xl sm:text-2xl font-semibold text-gray-800">{{$user->surname}} {{$user->name}} </div>
                @else
                    <div class="text-2xl sm:text-2xl font-semibold text-gray-800">Muzaffar Abdullayev</div>
                @endif

                <a href="{{  $is_mock ? route('user.mock-practice', ['id' =>$mock_id]) : route('user.next-exercise', ['type' => \App\Enums\CefrTypeEnum::WRITING->value,'id'=>$writingExercise->id]) }}"
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

    <div class="app-main px-2 flex flex-col lg:flex-row lg:flex-nowrap gap-4 pb-6 mt-3 overflow-x-hidden">


        <div class="w-full lg:flex-[0_0_calc(50%-10.67px)] min-h-0 min-w-0">
            <div id="textAreaDiv" class="h-full min-h-0 overflow-y-auto space-y-3 pr-1">
                <div class="{{$task_one ? '' : 'hidden'}} bg-white border-2 border-transparent rounded-xl shadow-sm">
                    <div class="p-2 sm:p-3 flex items-center justify-between gap-3 flex-wrap">
                        <span class="text-sm sm:text-base font-bold text-gray-900">Task 1.1</span>
                        <div class="flex items-center gap-2 text-blue-600 text-lg sm:text-xl font-bold">
                            <button id="editor-1-undo" type="button"
                                    class="w-8 h-8 rounded-full hover:bg-gray-100 active:scale-95 flex items-center justify-center">↺</button>
                            <button id="editor-1-redo" type="button"
                                    class="w-8 h-8 rounded-full hover:bg-gray-100 active:scale-95 flex items-center justify-center">↻</button>
                        </div>
                    </div>

                    <div class="px-2 sm:px-3 pb-2">
                        <textarea id="editor-1" class="w-full p-2 outline-none resize-none text-sm"></textarea>
                        <div class="text-right pt-2 text-sm sm:text-base font-semibold text-gray-500">
                            <span id="count-1">0</span>/50
                        </div>
                    </div>
                </div>

                <div class="{{$task_one ? '' : 'hidden'}} bg-white border-2 border-transparent rounded-xl shadow-sm">
                    <div class="p-2 sm:p-3 flex items-center justify-between gap-3 flex-wrap">
                        <span class="text-sm sm:text-base font-bold text-gray-900">Task 1.2</span>
                        <div class="flex items-center gap-2 text-blue-600 text-lg sm:text-xl font-bold">
                            <button id="editor-2-undo" type="button"
                                    class="w-8 h-8 rounded-full hover:bg-gray-100 active:scale-95 flex items-center justify-center">↺</button>
                            <button id="editor-2-redo" type="button"
                                    class="w-8 h-8 rounded-full hover:bg-gray-100 active:scale-95 flex items-center justify-center">↻</button>
                        </div>
                    </div>

                    <div class="px-2 sm:px-3 pb-2">
                        <textarea id="editor-2" class="w-full p-2 outline-none resize-none text-sm"></textarea>
                        <div class="text-right pt-2 text-sm sm:text-base font-semibold text-gray-500">
                            <span id="count-2">0</span>/120
                        </div>
                    </div>
                </div>

                <div class="{{$task_two ? '' : 'hidden'}} bg-white border-2 border-transparent rounded-xl shadow-sm">
                    <div class="p-2 sm:p-3 flex items-center justify-between gap-3 flex-wrap">
                        <span class="text-sm sm:text-base font-bold text-gray-900">Task 2</span>
                        <div class="flex items-center gap-2 text-blue-600 text-lg sm:text-xl font-bold">
                            <button id="editor-3-undo" type="button"
                                    class="w-8 h-8 rounded-full hover:bg-gray-100 active:scale-95 flex items-center justify-center">↺</button>
                            <button id="editor-3-redo" type="button"
                                    class="w-8 h-8 rounded-full hover:bg-gray-100 active:scale-95 flex items-center justify-center">↻</button>
                        </div>
                    </div>

                    <div class="px-2 sm:px-3 pb-2">
                        <textarea id="editor-3" class="w-full p-2 outline-none resize-none text-sm"></textarea>
                        <div class="text-right pt-2 text-sm sm:text-base font-semibold text-gray-500">
                            <span id="count-3">0</span>/180
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full lg:flex-[0_0_calc(38%-10.67px)] min-h-0 min-w-0">
            <div id="questionDiv" class="h-full min-h-0" tabindex="0">
                <div class="bg-white rounded-xl shadow-sm p-3 sm:p-4 text-sm leading-relaxed space-y-3">
                    @if($writingExercise->task_one)
                        <div class="space-y-2">
                            <p class="text-center font-bold text-gray-900">PART 1</p>
                            <p class="whitespace-pre-line text-gray-700 leading-relaxed">
                                {{ $writingExercise->task_one?->theme }}
                            </p>
                        </div>
                    @endif

                    @if($writingExercise->task_two)
                        <div class="space-y-2 pt-2">
                            <p class="text-center font-bold text-gray-900">PART 2</p>
                            <p class="whitespace-pre-line text-gray-700 leading-relaxed">
                                {{ $writingExercise->task_two?->theme }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="w-full lg:flex-[0_0_calc(12%-10.67px)] min-h-0 min-w-0">
            <div class="h-full flex flex-col justify-between gap-4">
                <div class="space-y-3">
                    <div class="bg-white rounded-xl shadow-sm p-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-700 text-sm sm:text-base">Time:</span>
                            <span id="timeSmall" class="text-xs sm:text-sm font-semibold text-gray-500"></span>
                        </div>
                        <div id="timeBig"
                             class="mt-2 text-center font-extrabold text-xl sm:text-3xl tracking-widest text-gray-900">
                            00:00
                        </div>
                    </div>

                    <div class="w-full bg-white rounded-xl shadow-sm p-2 flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="shrink-0 text-gray-700">
                            <path d="M4 22V4a1 1 0 0 1 .4-.8A6 6 0 0 1 8 2c3 0 5 2 7.333 2q2 0 3.067-.8A1 1 0 0 1 20 4v10a1 1 0 0 1-.4.8A6 6 0 0 1 16 16c-3 0-5-2-8-2a6 6 0 0 0-4 1.528"/>
                        </svg>

                        <button onclick="openFinishModal()" type="button"
                                class="w-full flex items-center gap-3 text-center bg-[#06b6d4] hover:bg-[#05a6c2]
                                       px-2 py-2 font-bold text-white text-base sm:text-lg transition active:scale-[0.99] rounded-lg">
                            <span class="flex-1 text-center">Yakunlash</span>
                        </button>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="minusText()" type="button"
                                class="w-1/2 rounded-xl px-3 py-2 sm:py-1 bg-white shadow-sm font-bold text-sm hover:bg-gray-50">
                            A-
                        </button>
                        <button onclick="plusText()" type="button"
                                class="w-1/2 rounded-xl px-3 py-2 sm:py-1 bg-white shadow-sm font-bold text-sm hover:bg-gray-50">
                            A+
                        </button>
                    </div>

                    <div class="pt-2 flex">
                        <div class="w-full flex flex-col gap-2 items-end">
                            <button id="part-1-1-button" type="button"
                                    class="part-pill w-fit min-w-[92px] sm:min-w-[110px] px-3 py-2 rounded-xl bg-white shadow-sm hover:bg-gray-50 font-semibold text-sm sm:text-base transition duration-200 ease-out transform active:scale-[0.99]">
                                Part 1.1
                            </button>

                            <button id="part-1-2-button" type="button"
                                    class="part-pill w-fit min-w-[92px] sm:min-w-[110px] px-3 py-2 rounded-xl bg-white shadow-sm hover:bg-gray-50 font-semibold text-sm sm:text-base transition duration-200 ease-out transform active:scale-[0.99]">
                                Part 1.2
                            </button>

                            <button id="part-2-button" type="button"
                                    class="part-pill w-fit min-w-[92px] sm:min-w-[110px] px-3 py-2 rounded-xl bg-white shadow-sm hover:bg-gray-50 font-semibold text-sm sm:text-base transition duration-200 ease-out transform active:scale-[0.99]">
                                Part 2
                            </button>
                        </div>
                    </div>
                </div>

                <div class="hidden lg:block"></div>
            </div>
        </div>

    </div>
</div>

<div id="videoBox"
     class="fixed hidden bottom-4 right-4 z-[9999] rounded-xl bg-white shadow-lg overflow-hidden w-[220px] h-[150px] sm:w-[260px] sm:h-[170px]">
    <div class="absolute top-2 right-2 z-10 flex items-center gap-2 bg-white/80 border border-gray-200 px-2 py-1 rounded-full">
        <label class="relative inline-flex items-center cursor-pointer">
            <input id="cameraToggle" type="checkbox" class="sr-only peer">
            <div class="w-9 h-5 bg-gray-300 rounded-full peer peer-checked:bg-gray-500 transition"></div>
            <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full peer-checked:translate-x-4 transition"></div>
        </label>
    </div>

    <video id="cameraPreview" class="absolute inset-0 w-full h-full object-cover hidden" autoplay muted playsinline></video>

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
            <button id="acceptDraftBtn" type="button"
                    class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition-colors font-medium text-sm">
                Accept
            </button>
            <button id="cancelDraftBtn" type="button"
                    class="px-4 py-2 border border-gray-400 bg-gray-100 text-[#333333] rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm">
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
                    class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-600 text-lg">
                ✕
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
                        class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-extrabold hover:bg-gray-50">
                    Cancel
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


<form id="writing-form" method="POST" action="{{ route('user.writing.store',  $writingExercise->id  ) }}" style="display:none;">
    @csrf
    <input type="hidden" id="writing-draft-input" name="writing_draft" value="">
    <input type="hidden" id="writing-with-ai-input" name="with_ai" value="0">
</form>


<div id="BackendData">
    <input type="hidden" id="exercise_id" value="{{$writingExercise->id??0}}">
    <input type="hidden" id="task_one_time_limit" value="{{$task_one_time_limit??0}}">
    <input type="hidden" id="task_two_time_limit" value="{{$task_two_time_limit??0}}">
    <input type="hidden" id="task_one" value="{{json_encode($task_one)}}">
    <input type="hidden" id="task_two" value="{{json_encode($task_two)}}">
</div>
<script src="{{ asset('vendor/jodit/jodit.min.js') }}"></script>
</body>
</html>
