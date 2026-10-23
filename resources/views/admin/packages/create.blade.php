@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.package.title') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.packages.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.package.fields.price') }}</label>
                <input class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}" oninput="updatedPriceInput(this)" data-profit-tax="{{$profit_tax}}" data-one-writing="{{$one_writing_price}}" data-one-speaking="{{$one_speaking_price}}" data-currency-value="{{$usd_current_currency}}" type="number" name="price" id="price" value="{{ old('price', '') }}" required>
                @if($errors->has('price'))
                    <div class="invalid-feedback">
                        {{ $errors->first('price') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.city.fields.name_helper') }}</span>
            </div>



            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.package.fields.writing_count') }}</label>
                <input class="form-control {{ $errors->has('writing_count') ? 'is-invalid' : '' }}" type="number" name="writing_count" id="writing_count" value="{{ old('writing_count', '') }}" required>
                @if($errors->has('writing_count'))
                    <div class="invalid-feedback">
                        {{ $errors->first('writing_count') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.city.fields.name_helper') }}</span>
            </div>


            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.package.fields.speaking_count') }}</label>
                <input class="form-control {{ $errors->has('speaking_count') ? 'is-invalid' : '' }}" type="number" name="speaking_count" id="speaking_count" value="{{ old('speaking_count', '') }}" required>
                @if($errors->has('speaking_count'))
                    <div class="invalid-feedback">
                        {{ $errors->first('speaking_count') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.package.fields.credit_price') }}</label>
                <input class="form-control {{ $errors->has('credit_price') ? 'is-invalid' : '' }}" type="number" step="0.000001"  name="credit_price" id="credit_price" value="{{ old('credit_price', '') }}" required>
                @if($errors->has('credit_price'))
                    <div class="invalid-feedback">
                        {{ $errors->first('credit_price') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.city.fields.name_helper') }}</span>
            </div>

            <div class="form-group ">
                <div class="">Writing Count: <span id="writing_count_span"></span></div>
                <div class="">Speaking Count: <span  id="speaking_count_span"></span> </div>
                <div class="">Profit: <span  id="profit_span"></span> </div>
            </div>

            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.packages.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
    <script>
        function updatedPriceInput(priceInput){
            let creditPriceInput=document.getElementById('credit_price')
            let writingSpan=document.getElementById('writing_count_span')
            let speakingSpan=document.getElementById('speaking_count_span')
            let writingInput=document.getElementById('writing_count')
            let speakingInput=document.getElementById('speaking_count')
            let profitSpan=document.getElementById('profit_span')

            if(priceInput.value>99){
                let uzsPrice=Number(priceInput.value);
                const profitTax = Number(priceInput.dataset.profitTax);
                const oneWriting = Number(priceInput.dataset.oneWriting);
                const oneSpeaking = Number(priceInput.dataset.oneSpeaking);
                const currencyValue = Number(priceInput.dataset.currencyValue);

                let credit_price_uzs = (uzsPrice / (100 + profitTax) * 100);

                creditPriceInput.value=(credit_price_uzs / currencyValue).toFixed(6)
                profitSpan.innerHTML=(uzsPrice / (100 + profitTax) * profitTax).toFixed(0)
                if((credit_price_uzs / currencyValue) > oneWriting ){
                    let count=((credit_price_uzs / currencyValue)/oneWriting).toFixed(0)
                    writingSpan.innerHTML=count
                    writingInput.value=count
                }else {
                    writingSpan.innerHTML=''
                    writingInput.value=''
                }

                if((credit_price_uzs / currencyValue) > oneSpeaking ){
                    let count =((credit_price_uzs / currencyValue)/oneSpeaking).toFixed(0);
                    speakingSpan.innerHTML=count
                    speakingInput.value=count
                }else{
                    speakingSpan.innerHTML=''
                    speakingInput.value=''

                }


            }else {
                creditPriceInput.value='';
                writingSpan.innerHTML=''
                speakingSpan.innerHTML=''
                speakingInput.value=''
                writingInput.value=''
            }
        }
    </script>
@endsection
