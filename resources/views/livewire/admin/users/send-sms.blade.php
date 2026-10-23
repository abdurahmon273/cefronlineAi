<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Send Sms</h5>
        <button type="button" class="close" data-dismiss="modal" id="render" wire:click="clear" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="rol">
            <div class="col p-3">
                <select class="form-control" name="shablon" wire:model.live="shablonId" id="">
                    <option value="">Tanlang</option>
                    @foreach($shablons as $id=> $shablon)
                        <option value="{{$id}}">{{$shablon}}</option>
                    @endforeach
                </select>
                <span class="text-danger">@error('shablonId') {{$message}}  @enderror</span>
            </div>

            <div class="col p-3">
              <textarea wire:model="description" disabled rows="1o" cols="51" class="form-control" name="description"></textarea>
            </div>
            @if($b==true) <h3 class="text-danger">User topilmadi. Iltimos userni tanlang</h3> @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" wire:click="clear" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" wire:click="send">Send</button>
    </div>
</div>
