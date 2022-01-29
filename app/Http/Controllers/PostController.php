<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostSaveRequest;
use App\Http\Resources\PostResource;
use App\Models\Images;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $post = Post::select(['*']);

        if($request->category) { // Filter by category
            $category = $request->category;
            $post->whereHas('category', function ($query) use ($category) {
                $query->where('id', $category);
            });
        }

        if($request->title) { // Filter by title
            $post->where('title', 'like', '%'.$request->title.'%');
        }

        return PostResource::collection($post->orderBy('id', 'desc')->paginate(5));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validations
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'slug' => 'required|string',
            'body' => 'required|string',
            'category_id' => 'required|string',
            'image' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = $validator->validated();

        $post['user_id'] = $request->user()->id;

        // Create post image
        if ($archivo = $request->file('image')) {
            $nombre = $archivo->getClientOriginalName();
            $archivo->move('images/miniatures', $nombre);

            $image = Images::create(['route' => $nombre]);

            $post['image_id'] = $image->id;
        }

        $post = Post::create($post);

        return response($post, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        // $post = Post::findOrFail($id);

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // TODO: Colocar la validacion de la request y guardar el usuario authenticado y la imagen de la publicacion

        $newPost = $request->all();

        $post = Post::findOrFail($id);

        $post->update($newPost);

        return response($post, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        $post->delete();
    }
}
