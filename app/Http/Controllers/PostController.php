<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostSaveRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Images;
use App\Models\Post;
use Carbon\Carbon;
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
        // ================= Validations ================
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'slug' => 'required|string',
            'body' => 'required|string',
            'category_id' => 'required|int',
            'image' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = $validator->validated();

        // =============== Validate category ==============
        $category = Category::find($post['category_id']);

        if(!$category) { // Category not found
            return response()->json(['error' => 'Categoria no encontrada'], 404);
        } else {
            if(!$category['enabled']) { // Disabled category
                return response()->json(['error' => 'La categoria indicada no se encuentra habilitada'], 422);
            }
        }
        // ================================================

        $post['user_id'] = $request->user()->id;

        // =============== Create post image ==============
        if ($archivo = $request->file('image')) {
            $nombre = $archivo->getClientOriginalName();
            $archivo->move('images/miniatures', $nombre);

            $image = Images::create(['route' => $nombre]);

            $post['image_id'] = $image->id;
        }

        $post = Post::create($post);

        return (new PostResource($post))->response()->setStatusCode(201);
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
    public function update(Request $request, Post $post)
    {
        // ================= Validations ================
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'slug' => 'required|string',
                'body' => 'required|string',
                'enabled' => 'required|string',
                'category_id' => 'required|int',
            ]);

            if($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

        $newPost = $request->all();

        // =============== Validate category ==============
        $category = Category::find($newPost['category_id']);

        if(!$category) { // Category not found
            return response()->json(['error' => 'Categoria no encontrada'], 404);
        } else {
            if(!$category['enabled']) { // Disabled category
                return response()->json(['error' => 'La categoria indicada no se encuentra habilitada'], 422);
            }
        }

        // ============== Validate image ==================
        if(isset($newPost['image'])) { // If update image
            // ============ Create post image =============
            if ($file = $request->file('image')) {
                $name = $file->getClientOriginalName();
                $file->move('images/miniatures', $name);
    
                $image = Images::create(['route' => $name]);
    
                $newPost['image_id'] = $image->id;

                $oldImage = Images::find($post->image->id);
            }
        }

        // ============= Set publication Date =============
        if(!isset($post['publicationDate']) && $newPost['enabled'] == true) {
            $newPost['publicationDate'] = Carbon::today();
            //return response()->json(["data1" => $newPost, "data2" => $post]);
        }

        if($newPost['enabled'] == 'true') $newPost['enabled'] = true;
        else $newPost['enabled'] = false;

        $post->update($newPost);

        if(isset($oldImage)) {
            $oldImage->delete();
        }

        return (new PostResource(Post::find($post->id)))->response()->setStatusCode(201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        (Images::find($post->image->id))->delete(); // Delete related image 
        return (new PostResource($post))->response()->setStatusCode(200);
    }

    // Funcion for public route index
    public function publicIndex(Request $request) {
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

        $post->where('enabled', true);

        return PostResource::collection($post->orderBy('id', 'desc')->paginate(5));
    }

    public function publicShow(Post $post) {
        if($post['enabled'] == false) {
            return response()->json(['error' => 'No puede acceder a esta publicacion'], 403);
        }

        return new PostResource($post);
    }
}
