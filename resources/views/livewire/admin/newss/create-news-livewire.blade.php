<div class="form-group">
    <label class="required">{{ trans('cruds.news.fields.status') }}</label>
    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" wire:model.live="status" id="status" required>
        <option value="null" {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
        @foreach(App\Models\News::STATUS_SELECT as $key => $label)
            <option value="{{ $key }}" {{ old('status', '0') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @if($errors->has('status'))
        <div class="invalid-feedback">
            {{ $errors->first('status') }}
        </div>
    @endif
    <span class="help-block">{{ trans('cruds.news.fields.status_helper') }}</span>
</div>
