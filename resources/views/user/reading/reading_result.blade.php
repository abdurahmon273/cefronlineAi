<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reading Result</title>

    @vite([
        'resources/css/app.css',
        'resources/css/test/reading/reading.css',
        'resources/js/app.js',
        'resources/js/test/reading_result/reading_result.js'
    ])
</head>

<body class="bg-gray-100 font-sans">

    {{-- APP WRAP --}}
    <div class=" mx-auto app-root">

        <div class="app-nav ">
            <div class="flex items-center bg-white p-2 justify-around gap-3   pb-3">
                <div class="min-w-[150px] flex items-center gap-2">
                    <a href="{{ route('user.practice-test', ['id' =>$reading_exercise_id, 'type' => $type,'difficulty'=>$difficulty ?? 0 ,'time_limit'=>$time_limit?? 0 ,'part_category'=>$part_category_number??0,'title'=>$name,'status' => $status ]) }}" class="flex h-[24px] w-[44px] sm:h-[44px] sm:w-[54px] border border-gray-200 items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                        <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="/assets/arrow-left.svg" alt="arrow left">
                    </a>
                    <h1 class="text-2xl sm:text-2xl font-bold text-gray-700">Reading</h1>

                </div>

                <div id="partsNav" class="flex items-center justify-center gap-2 flex-1">

                </div>

                <div class="min-w-[220px] flex justify-end items-center gap-3">

                    @if(auth()->user())
                        @php $user=auth()->user() @endphp
                        <div class="text-2xl sm:text-2xl font-semibold text-gray-800">{{$user->surname}} {{$user->name}} </div>
                    @else
                        <div class="text-2xl sm:text-2xl font-semibold text-gray-800">Muzaffar Abdullayev</div>
                    @endif

                    <a href="{{ route('user.next-exercise', ['type' => \App\Enums\CefrTypeEnum::READING->value,'id'=>$reading_exercise_id]) }}"
                       class="inline-flex items-center gap-2
              px-4 py-2
              bg-orange-600 hover:bg-orange-700
              text-white text-sm font-semibold
              rounded-xl
              transition-all duration-200
              shadow-sm hover:shadow-md">

                        More Practice

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
                <div id="questionDiv" class="h-full  rounded-md ">

                    <div id="partOneQuestionDiv"
                        class="hidden rounded-lg  h-full w-full flex flex-col gap-2 bg-gray-100">
                        <div class="h-full p-2 sm:p-2 bg-white rounded-lg">
                            <!-- Part One content will be injected here by JS -->
                        </div>
                    </div>

                    <div id="partTwoQuestionDiv" class="hidden rounded-lg h-full w-full bg-gray-100">
                        <div class="h-full p-2 sm:p-2 bg-white rounded-lg overflow-y-auto">
                            <!-- Part Two content will be injected here by JS -->
                        </div>
                    </div>


                    <div id="partThreeQuestionDiv" class="hidden  h-full w-full ">
                        <div class="h-full p-2 sm:p-2  ">
                            <div class="grid h-full grid-cols-1 lg:grid-cols-[60%_40%] ">

                                <!-- LEFT (TEXTS) -->
                                <div
                                    class="pr-2  bg-white  text-sm sm:text-base p-2 rounded-lg text-gray-900 leading-relaxed overflow-y-auto">
                                    <div class="mb-4">
                                        <div class="font-bold text-gray-900">PART 3</div>
                                        <p id="partThreeAdviceP" class="mt-1 text-xs sm:text-sm text-black"></p>
                                    </div>

                                    <div id="partThreeTextsWrap" class="space-y-4 py-4"></div>
                                </div>

                                <!-- RIGHT (SELECTS) -->
                                <div class="pl-2 p-2 space-y-3 text-sm sm:text-base overflow-y-auto">
                                    <div id="partThreeSelectsWrap" class="space-y-3"></div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div id="partFourQuestionDiv" class="hidden  h-full w-full ">
                        <div class="h-full p-2 sm:p-2  rounded-lg">
                            <div class="grid h-full grid-cols-1 lg:grid-cols-[60%_40%] gap-x-1">

                                <!-- LEFT: TEXT -->
                                <div
                                    class="pr-2 border-r-2 bg-white rounded-lg text-sm sm:text-base p-2 text-gray-900 leading-relaxed overflow-y-auto">
                                    <div class="mb-4">
                                        <div class="font-bold text-gray-900">PART 4</div>
                                        <p id="partFourAdviceP" class="mt-1 text-xs sm:text-sm text-black"></p>
                                    </div>

                                    <div class="space-y-4 py-4">
                                        <p id="partFourTitleH3" class="font-bold text-center w-full"></p>
                                        <div id="partFourTextDiv" class="space-y-4"></div>
                                    </div>
                                </div>

                                <!-- RIGHT: QUESTIONS -->
                                <div id="partFourQuestionsDiv"
                                    class="pl-2 p-2 space-y-3 text-sm sm:text-base overflow-y-auto"></div>

                            </div>
                        </div>
                    </div>


                    <div id="partFiveQuestionDiv" class="hidden  h-full w-full bg-gray-100">
                        <div class="h-full p-2 sm:p-2 e ">
                            <div class="grid h-full grid-cols-1 lg:grid-cols-[60%_40%]">

                                <!-- LEFT -->
                                <div
                                    class="pr-2 border-r-2 bg-white rounded-lg p-2 text-gray-900 leading-relaxed overflow-y-auto">
                                    <div class="mb-4">
                                        <div class="font-bold text-gray-900">PART 5</div>
                                        <p id="partFiveAdviceP" class="mt-1 text-xs sm:text-sm text-black"></p>
                                    </div>
                                    <h3 id="partFiveTitleH3" class="text-center font-bold text-xl sm:text-2xl text-gray-900 tracking-wide"></h3>

                                    <div id="partFiveReadingWrap" class="space-y-4 py-4"></div>
                                </div>

                                <!-- RIGHT -->
                                <div class="pl-2 p-2 space-y-3 overflow-y-auto">
                                    <div id="partFiveFillGapsWrap"></div>
                                    <div id="partFiveMchqWrap" class="space-y-3"></div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- RIGHT SIDEBAR --}}
            <div class="w-full
                    md:[width:clamp(300px,40vw,420px)]
                    lg:[width:clamp(260px,33vw,360px)]
                    xl:[width:clamp(240px,30vw,345px)]
                    2xl:[width:clamp(220px,22vw,345px)]
                    3xl:[width:clamp(200px,18vw,280px)]  min-h-0">
                <div class="h-full flex flex-col justify-between gap-4">

                    <div class="space-y-2">

                        <div class="bg-white   rounded-xl p-1">
                            <div class=" time-text-div flex items-center  justify-between absolute">
                                <span class="font-bold text-gray-700 text-sm sm:text-base">Time:</span>
                                <span id="timeSmall" class="text-xs sm:text-sm font-semibold text-gray-500"></span>
                            </div>

                            <div id="timeBig"
                                class="mt-2 text-center font-extrabold text-2xl sm:text-2xl tracking-widest text-gray-800">
                                00:00
                            </div>
                        </div>

                        <div class="flex gap-2 items-center">
                            <div class="w-full flex gap-2">
                                <button onclick="minusText()" type="button"
                                    class="w-1/2 rounded-xl  px-3 py-2 sm:py-1 bg-white font-bold text-sm">
                                    A-
                                </button>
                                <button onclick="plusText()" type="button"
                                    class="w-1/2 rounded-xl  px-3 py-2 sm:py-1 bg-white font-bold text-sm">
                                    A+
                                </button>
                            </div>

                            <div class="w-1/2"></div>
                        </div>



                        <div id="focusQuestionWrap"></div>

                        <div class="mt-3 bg-white rounded-2xl p-4">
                            <div class="flex items-center justify-between gap-4">

                                <!-- LEFT: ONLY percentage ring -->
                                <div class="relative w-[92px] h-[92px] shrink-0">
                                    <svg viewBox="0 0 120 120" class="w-full h-full -rotate-90">
                                        <!-- background ring -->
                                        <circle cx="60" cy="60" r="46"
                                                class="fill-none stroke-gray-200"
                                                stroke-width="12" />

                                        <!-- progress ring (static demo) -->
                                        <circle cx="60" cy="60" r="46"
                                                class="fill-none stroke-orange-500"
                                                stroke-width="12"
                                                stroke-linecap="round"
                                                stroke-dasharray="{{$percentageData['array']}}"
                                                stroke-dashoffset="{{$percentageData['offset']}}" />
                                    </svg>

                                    <!-- center text -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-xl font-extrabold text-gray-900" data-stat-percent>{{$percentageData['percent']}}%</div>
                                    </div>
                                </div>

                                @if($percentageData['is_mock'])
                                    <!-- RIGHT: ONLY CEFR -->
                                    <div class="text-right">
                                        <div class="text-4xl font-extrabold text-gray-900 leading-none" data-stat-cefr>{{$percentageData['mock_ball']}}</div>
                                        <div class="mt-1 text-xs font-semibold tracking-wider text-gray-500">Ball</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="{{$percentageData['degree']=='Below B1' ? 'text-xl ' : 'text-4xl'}} font-extrabold text-gray-900 leading-none" data-stat-cefr>{{$percentageData['degree']}}</div>
                                        <div class="mt-1 text-xs font-semibold tracking-wider text-gray-500">LEVEL</div>
                                    </div>
                                @endif


                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        {{-- Backend Data --}}
        <div id="BackendData">
            <input type="hidden" id="exercise_id" value="{{ $id ?? 0 }}">
            <input type="hidden" id="time_limit" value="{{ $time_limit ?? 60 }}">

            {{-- result draft (serverdan) --}}
            <input type="hidden" id="reading_draft" value="{{ json_encode($reading_draft ?? null) }}">

            <input type="hidden" id="part_one" value="{{ json_encode($part_one ?? null) }}">
            <input type="hidden" id="part_two" value="{{ json_encode($part_two ?? null) }}">
            <input type="hidden" id="part_three" value="{{ json_encode($part_three ?? null) }}">
            <input type="hidden" id="part_four" value="{{ json_encode($part_four ?? null) }}">
            <input type="hidden" id="part_five" value="{{ json_encode($part_five ?? null) }}">
        </div>

    </div>

</body>

</html>
