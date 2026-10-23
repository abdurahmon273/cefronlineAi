<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyNewsRequest;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\News;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class NewsController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('news_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

//        $newss = News::with(['media'])->get();
//        foreach ($newss as $news) {
//            $news->video_link = $this->extractVideoId($news->video_link);
//        }
        return view('admin.newss.index');
    }

    public function create()
    {
        abort_if(Gate::denies('news_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.newss.create');
    }

    public function store(StoreNewsRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $news = News::create($request->all());

            $notification = Notification::create([
                'title' => $request->title ?? '',
                'text' => $request->description ?? '',
                'link' => $request->link ?? '',
            ]);


            if ($request->input('image', false)) {
                $newsMedia = $news->addMedia(storage_path('tmp/uploads/' . basename($request->input('image'))))->toMediaCollection('image');
                $newsMedia->copy($notification, 'image');
            }

            if ($request->input('web_image', false)) {
                $newsWebMedia = $news->addMedia(storage_path('tmp/uploads/' . basename($request->input('web_image'))))->toMediaCollection('web_image');
                $newsWebMedia->copy($notification, 'web_image');
            }

            if ($media = $request->input('ck-media', false)) {
                Media::whereIn('id', $media)->update(['model_id' => $news->id]);
            }

            return redirect()->route('admin.newss.index');
        });
    }

    public function edit(News $newss)
    {
        abort_if(Gate::denies('news_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $news=$newss;
        return view('admin.newss.edit', compact('news'));
    }

    public function update(UpdateNewsRequest $request, News $newss)
    {
        $news=$newss;
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

        if ($request->input('web_image', false)) {
            if (! $news->web_image || $request->input('web_image') !== $news->web_image->file_name) {
                if ($news->web_image) {
                    $news->web_image->delete();
                }
                $news->addMedia(storage_path('tmp/uploads/' . basename($request->input('web_image'))))->toMediaCollection('web_image');
            }
        } elseif ($news->web_image) {
            $news->web_image->delete();
        }

        return redirect()->route('admin.newss.index');
    }

    public function show(News $newss)
    {
        $news=$newss;
        abort_if(Gate::denies('news_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $news->video_link = $this->extractVideoId($news->video_link);
        return view('admin.newss.show', compact('news'));
    }
    private function extractVideoId($url)
    {
        $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
        preg_match($pattern, $url, $matches);

        return $matches[1] ?? null;
    }
    public function destroy(News $newss)
    {
        abort_if(Gate::denies('news_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $newss->delete();

        return back();
    }

    public function massDestroy(MassDestroyNewsRequest $request)
    {
        $newss = News::find(request('ids'));

        foreach ($newss as $news) {
            $news->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('news_create') && Gate::denies('news_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new News();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
