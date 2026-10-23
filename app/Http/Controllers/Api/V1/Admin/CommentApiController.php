<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\Admin\CommentResource;
use App\Models\Comment;
use App\Models\Video;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CommentApiController extends Controller
{
    public function index($id)
    {
        abort_if(Gate::denies('comment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $comments=Comment::with(['video' => function ($query) {
            $query->withCount('videoWatched');
        }, 'user'])->where('video_id', $id)->get();
        if ($comments->isNotEmpty()) {
        return CommentResource::collection($comments);
        }else{
            return response()->json([
                'success' => false,
                'errors' => 'Comments mavjud emas',
            ]);
        }
    }

    public function store(StoreCommentRequest $request, $id)
    {
        $video=Video::where('id', $id)->first();
        if ($video){
            $comment = Comment::create([
                'comment' => $request->comment,
                'video_id' => $id,
                'user_id' => Auth::id(),
            ]);
            if ($comment) {
                return \response()->json([
                    'success' => true,
                    'message'=>"Comment jo'natildi",
                    'comment' => new CommentResource($comment)
                ]);
            } else {
                return \response()->json([
                    'success' => false,
                    'errors' => 'Malumot yaratilmadi'
                ]);
            }
        }else{
            return response()->json([
                'success' => false,
                'errors' => 'Bu video mavjud emas',
            ]);
        }

    }

    public function show(Comment $comment)
    {
        abort_if(Gate::denies('comment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new CommentResource($comment->load(['video' => function ($query) {
            $query->withCount('videoWatched');
        }, 'user']));
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $comment->update($request->all());

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Comment $comment)
    {
        abort_if(Gate::denies('comment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $comment->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
