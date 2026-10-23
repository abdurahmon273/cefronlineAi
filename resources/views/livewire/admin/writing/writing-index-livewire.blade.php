<div>

    <div class="row ">
        <div class="col-6 "  draggable="true">
            <a href="{{route('admin.writing.task1.exercise.index')}}" class="card c-header border-none w-100 pt-4 pb-4 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                    <h4 class="card-title">
                            Task One
                    </h4>
            </a>
        </div>
        <div  class="col-6 "  draggable="true">
            <a href="{{route('admin.writing.task2.exercise.index')}}" class="card c-header border-none w-100 pt-4 pb-4 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                    <h4 class="card-title">
                            Task Two
                    </h4>
            </a>
        </div>
        <div class="col-12 "  draggable="true">
            <a href="{{route('admin.writing.full.exercises.index')}}" class="card c-header border-none w-100 pt-4 pb-4 d-flex justify-content-center align-items-center"  style="border-radius: 8px;">
                    <h4 class="card-title">
                            Full Writing
                    </h4>
            </a>
        </div>

    </div>
    <div colspan="5">
        {{ $exercises->links() }}
    </div>
</div>
