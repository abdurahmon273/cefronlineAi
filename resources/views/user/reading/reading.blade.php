<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reading</title>

    @vite([
    'resources/css/app.css',
    'resources/css/test/reading/reading.css',
    'resources/js/app.js',
    'resources/js/test/reading/reading.js'
    ])

</head>

<body class="bg-gray-100 font-sans">

{{-- APP WRAP --}}
<div class=" mx-auto app-root">

    <div class="app-nav ">
        <div class="flex items-center bg-white p-2 justify-around gap-3   pb-3">
            <div class="min-w-[150px] flex items-center gap-2">
                @if($is_mock)
                    <a href="{{ route('user.mock-practice', ['id' =>$mock_id]) }}" class="flex h-[24px] w-[44px] sm:h-[44px] sm:w-[54px] border border-gray-200 items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                        <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="/assets/arrow-left.svg" alt="arrow left">
                    </a>
                @else

                    <a href="{{ route('user.practice-test', ['id' =>$id, 'type' => $type,'difficulty'=>$difficulty ?? 0 ,'time_limit'=>$time_limit?? 0 ,'part_category'=>$part_category_number??0,'title' => $name??0,'status' => $status]) }}" class="flex h-[24px] w-[44px] sm:h-[44px] sm:w-[54px] border border-gray-200 items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                        <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="/assets/arrow-left.svg" alt="arrow left">
                    </a>
                @endif

                <h1 class="text-2xl sm:text-2xl font-bold text-gray-700">Reading</h1>

            </div>

            <div id="partsNav" class="flex items-center justify-center gap-2 flex-1">

            </div>



            <div class="min-w-[220px] flex justify-end   flex justify-end items-center gap-3">

                @if(auth()->user())
                    @php $user=auth()->user() @endphp
                    <div class="text-2xl sm:text-2xl font-semibold text-gray-800">{{$user->surname}} {{$user->name}} </div>
                @else
                    <div class="text-2xl sm:text-2xl font-semibold text-gray-800">Muzaffar Abdullayev</div>
                @endif

                        <a href="{{ $is_mock ? route('user.mock-practice', ['id' =>$mock_id]) : route('user.next-exercise', ['type' => $type,'id'=>$id])   }}"
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
            <div id="questionDiv" class="h-full  rounded-md ">

                <div id="partOneQuestionDiv" class="hidden rounded-lg  h-full w-full flex flex-col gap-2 bg-white"></div>

                <div id="partTwoQuestionDiv" class="hidden rounded-lg h-full w-full bg-gray-100">

                </div>


                <div id="partThreeQuestionDiv" class="hidden rounded-lg h-full w-full bg-gray-100">
                    <div class="h-full p-2 sm:p-2  rounded-lg">
                        <div class="grid h-full grid-cols-1 lg:grid-cols-[60%_40%] gap-x-1">

                            <!-- LEFT (TEXTS) -->
                            <div class="pr-2 bg-white rounded-lg shadow-md text-sm sm:text-base p-2 text-gray-900  overflow-y-auto">
                                <div class="mb-4">
                                    <div class="font-bold text-gray-900">PART 3</div>
                                    <p id="partThreeAdviceP" class="mt-1  text-black"></p>
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
                    <div class="h-full p-2 sm:p-2  ">
                        <div class="grid h-full grid-cols-1 lg:grid-cols-[60%_40%] gap-x-1">

                            <!-- LEFT: TEXT -->
                            <div class="pr-2 bg-white rounded-lg border-r-2  text-sm sm:text-base p-2 text-gray-900  overflow-y-auto">
                                <div class="mb-4">
                                    <div class="font-bold text-gray-900">PART 4</div>
                                    <p id="partFourAdviceP" class="mt-1  text-black"></p>
                                </div>

                                <div class="space-y-4 py-4">
                                    <p id="partFourTitleH3" class="font-bold text-center w-full"></p>
                                    <div id="partFourTextDiv" class="space-y-4"></div>
                                </div>
                            </div>

                            <!-- RIGHT: QUESTIONS -->
                            <div id="partFourQuestionsDiv" class="pl-2 p-2 space-y-3 text-sm sm:text-base overflow-y-auto"></div>

                        </div>
                    </div>
                </div>


                <div id="partFiveQuestionDiv" class="hidden  h-full w-full ">
                    <div class="h-full p-2 sm:p-2  rounded-lg">
                        <div class="grid h-full grid-cols-1 lg:grid-cols-[60%_40%]">

                            <!-- LEFT -->
                            <div class="pr-2 bg-white rounded-lg p-2 text-gray-900  overflow-y-auto">
                                <div class="mb-4">
                                    <div class="font-bold text-gray-900">PART 5</div>
                                    <p id="partFiveAdviceP" class="mt-1  text-black"></p>
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

                    <div class="w-full bg-white p-1 flex items-center  gap-4">
                        <!-- FLAG ICON -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="22"
                            height="22"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="shrink-0"
                        >
                            <path d="M4 22V4a1 1 0 0 1 .4-.8A6 6 0 0 1 8 2c3 0 5 2 7.333 2q2 0 3.067-.8A1 1 0 0 1 20 4v10a1 1 0 0 1-.4.8A6 6 0 0 1 16 16c-3 0-5-2-8-2a6 6 0 0 0-4 1.528"/>
                        </svg>
                        <button
                            onclick="openFinishModal()"
                            type="button"
                            class="
            w-full
            flex items-center gap-3
            text-center
            bg-[#06b6d4]
            hover:bg-[#05a6c2]
            px-2 py-1
            font-bold
            text-white
            text-base sm:text-lg
            transition
            active:scale-[0.99]
        "
                        >


                            <!-- TEXT -->
                            <span class="flex-1 text-center">
            Yakunlash
        </span>
                        </button>
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

                        <!-- ✅ PEN COLOR PICKER (A+ ning o'ng tarafida) -->
                        <div class="w-1/2 flex justify-end">
                            <div id="penColorBox"
                                 class="relative w-full max-w-[180px] rounded-xl  bg-white  px-2 py-1 flex items-center justify-between cursor-pointer select-none">
                                <div class="flex items-center gap-2">
                                    <div id="penColorPreview"
                                         class="w-7 h-5 rounded-md border  bg-white"></div>
                                    <div class="text-xs font-bold text-gray-700">Pen</div>
                                </div>

                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>

                                <!-- Dropdown -->
                                <div id="penColorDropdown"
                                     class="hidden absolute right-0 top-[110%] z-50 w-full border-2  bg-white rounded-lg p-2 shadow-md">
                                    <div class="grid grid-cols-2 gap-2">
                                        <!-- default / remove -->
                                        <button type="button" data-color=""
                                                class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300 bg-white"
                                                title="Default (remove)"></button>

                                        <button type="button" data-color="#fff200"
                                                class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300"
                                                style="background:#fff200" title="Yellow"></button>

                                        <button type="button" data-color="#00ff00"
                                                class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300"
                                                style="background:#00ff00" title="Green"></button>

                                        <button type="button" data-color="#00ffff"
                                                class="pen-swatch w-8 h-7 rounded-md border-2 border-gray-300"
                                                style="background:#00ffff" title="Turquoise"></button>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>





                       <div id="focusQuestionWrap"></div>


                </div>

                <!-- VIDEO BOX -->
                <div id="videoBox"
                     class="rounded-lg hidden bg-white h-[22%] lg:h-[28%] relative overflow-hidden">
                    <div
                        class="absolute top-1 right-1 z-10 flex items-center gap-2 bg-white/80 border border-gray-300 px-2 py-1 rounded-full">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input id="cameraToggle" type="checkbox" class="sr-only peer">
                            <div
                                class="w-9 h-5 bg-gray-300 rounded-full peer peer-checked:bg-gray-500 transition"></div>
                            <div
                                class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full peer-checked:translate-x-4 transition"></div>
                        </label>
                    </div>

                    <video
                        id="cameraPreview"
                        class="absolute inset-0 w-full h-full object-cover hidden"
                        autoplay
                        muted
                        playsinline>
                    </video>

                    <span id="videoPlaceholder"
                          class="absolute inset-0 flex  items-center justify-center text-lg sm:text-2xl text-gray-700 font-bold tracking-widest">
                            Video
                        </span>
                </div>


            </div>
        </div>

    </div>

    {{--      Data       --}}
    <div id="BackendData">
        <input type="hidden" id="exercise_id" value="{{$id??0}}">
        <input type="hidden" id="time_limit" value="{{$time_limit??60}}">
        <input type="hidden" id="part_one" value="{{json_encode($part_one)}}">
        <input type="hidden" id="part_two" value="{{json_encode($part_two)}}">
        <input type="hidden" id="part_three" value="{{json_encode($part_three)}}">
        <input type="hidden" id="part_four" value="{{json_encode($part_four)}}">
        <input type="hidden" id="part_five" value="{{json_encode($part_five)}}">
    </div>

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

    <form id="reading-form" method="POST" action="{{ route('user.reading.store',  $id  ) }}" style="display: none;">
        @csrf
        <input type="hidden" id="reading-draft-input" name="reading_draft" value="">
    </form>

</div>

<script>
</script>


</body>
</html>
