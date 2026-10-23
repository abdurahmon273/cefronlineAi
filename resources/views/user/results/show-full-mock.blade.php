@extends('layouts.user')

@section('content')


    <div class="flex-1 w-full lg:w-auto">
        <div class="px-4 sm:px-6 py-4 sm:py-6">
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-12">
                <div class="lg:col-span-9">
                    <div class="rounded-2xl sm:rounded-3xl bg-[#fafafa] p-4 sm:p-6 lg:p-[32px]">
                        <div class="mb-6 flex items-center gap-4">
                              @if($type)
                              <a href="{{route('user.mock-practice',['id'=>$exercise_id])}}" class="flex h-[44px] w-[44px] sm:h-[54px] sm:w-[54px] items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                                  <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="{{ asset("assets/arrow-left.svg") }}" alt="arrow left">
                              </a>
                              @else
                              <a href="{{route('user.results')}}" class="flex h-[44px] w-[44px] sm:h-[54px] sm:w-[54px] items-center justify-center rounded-[12px] sm:rounded-[16px] bg-white hover:bg-slate-200 transition-colors flex-shrink-0">
                                  <img class="h-[20px] w-[20px] sm:h-[24px] sm:w-[24px]" src="{{ asset("assets/arrow-left.svg") }}" alt="arrow left">
                              </a>
                              @endif
                            <div>
                                <p class="text-sm text-gray-500">Results > <span class="text-orange-500 font-medium">Full mock</span></p>
                                <h1 class="text-2xl sm:text-3xl lg:text-[32px] font-extrabold text-slate-900">Full mock</h1>
                            </div>
                        </div>

                        <x-tab-navigation
                            :tabs="$testTypes"
                            :buttonClass="'test-type-button'"
                            :identifier="'test-type'" />

                        {{-- Speaking Content --}}
                        <div class="test-type-content {{ $testTypes[0]=='Listening' ? ' ': 'hidden' }}" data-test-type="Listening">
                           @livewire('user.results.show-listening-livewire',['data'=>$listeningData,'type'=>0])
                        </div>

                        {{-- Reading Content --}}
                        <div class="test-type-content  {{ $testTypes[0]=='Reading' ? ' ': 'hidden' }} " data-test-type="Reading">
                            @livewire('user.results.show-reading-livewire',['data'=>$readingData,'type'=>0])
                        </div>

                        {{-- Writing Content --}}
                        <div class="test-type-content  {{ $testTypes[0]=='Writing' ? ' ': 'hidden' }} " data-test-type="Writing">
                            @livewire('user.results.show-writing-livewire',['data'=>$writingData,'type'=>0])
                        </div>

                        {{-- Listening Content --}}
                        <div class="test-type-content  {{ $testTypes[0]=='Speaking' ? ' ': 'hidden' }}" data-test-type="Speaking">
                            @livewire('user.results.show-speaking-livewire',['data'=>$speakingData,'type'=>0])

                        </div>

                    </div>

                </div>
                @include('components.membership-card')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        (() => {
            const initTabToggle = (buttonSelector, contentSelector, dataAttr) => {
                const buttons = document.querySelectorAll(buttonSelector);
                const contents = document.querySelectorAll(contentSelector);

                buttons.forEach(button => {
                    button.addEventListener('click', () => {
                        // Update button styles
                        buttons.forEach(btn => {
                            btn.classList.remove('bg-white', 'text-orange-600', 'shadow-sm');
                            btn.classList.add('text-gray-500', 'hover:text-gray-700');
                        });
                        button.classList.add('bg-white', 'text-orange-600', 'shadow-sm');
                        button.classList.remove('text-gray-500', 'hover:text-gray-700');

                        // Hide all contents and show selected one
                        contents.forEach(content => content.classList.add('hidden'));
                        const activeValue = button.getAttribute(`data-${dataAttr}`);
                        const selectedContent = document.querySelector(
                            `${contentSelector}[data-${dataAttr}="${activeValue}"]`
                        );
                        if (selectedContent) {
                            selectedContent.classList.remove('hidden');
                        }
                    });
                });
            };

            // Initialize all tab toggles
            initTabToggle('.test-type-button', '.test-type-content', 'test-type');
            initTabToggle('.speaking-part-button', '.speaking-part-content', 'part');
        })();
    </script>
@endsection
