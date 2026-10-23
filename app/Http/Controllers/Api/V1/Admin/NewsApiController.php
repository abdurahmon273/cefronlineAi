<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Http\Resources\Admin\NewsResource;
use App\Http\Resources\NewsApiResourceCollection;
use App\Models\News;
use App\Models\PremiumToUsers;
use App\Services\PremiumApiService;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('news_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $existingPlan = PremiumToUsers::where('user_id', auth()->user()->id)
            ->where(function ($query) {
                $query->where('start_time', '<=', now())
                    ->where('end_time', '>=', now());
            })
            ->orderByDesc('id')
            ->first();

        if (!$existingPlan) {
            $premiumApi = new PremiumApiService();
            return $premiumApi->unPremium();
        }

        $news=News::with('qabul')->orderBy('order_id')->get();
        $grouped = $news->groupBy(function($item) {
            return $item->created_at->format('d-m-Y');
        });
        // Ma'lumotlarni kerakli formatda qayta ishlash
        $formatted = $grouped->map(function ($items, $date) {
            return [
                'group_date' => $date,
                'data' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'description' => $item->description,
                        'link' => $item->link ?? null,
                        'status' => $item->application_button,
                        'button_text' => $item->button_text,
                        'images' =>$item->image ? $item->image->url : null,
                        'created_time' => $item->created_at->format('H:i'),
                    ];
                })->values(),
            ];
        })->values();

        return \response()->json(['data' => $formatted]);
//        return  NewsResource::collection(News::with('qabul')->orderByDesc('id')->get());
//        return new NewsApiResourceCollection(News::with('qabul')->orderByDesc('id')->get());
    }

    public function store(StoreNewsRequest $request)
    {
        $news = News::create($request->all());

        if ($request->input('image', false)) {
            $news->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
        }

        return (new NewsResource($news))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show($id)
    {
//        abort_if(Gate::denies('news_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $news=News::find($id);
        if ($news){
        return new NewsResource($news);
        }else{
            return response()->json([
                'success'=>false,
                'errors'=>"Yangilik mavjud emas"
            ]);
        }
    }

    public function update(UpdateNewsRequest $request, News $news)
    {
        $news->update($request->all());

        if ($request->input('image', false)) {
            if (! $news->image || $request->input('image') !== $news->image->file_name) {
                if ($news->image) {
                    $news->image->delete();
                }
                $news->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
            }
        } elseif ($news->image) {
            $news->image->delete();
        }

        return (new NewsResource($news))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(News $news)
    {
        abort_if(Gate::denies('news_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $news->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
