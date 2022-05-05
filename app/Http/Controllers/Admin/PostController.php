<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('created_at','desc')->get();
        return view('admin.posts.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'published_at' => 'nullable|date|before_or_equal:today',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $data = $request->all();

        $slug = Str::slug( $data['title']);
        $slug_base = $slug;
        $counter=1;

        $current_post = Post::where('slug',$slug)->first();
        while($current_post){
            $slug = $slug_base.'-'.$counter;
            $counter++;
            $current_post = Post::where('slug',$slug)->first();
        }


        $post= new Post();
        $post->fill($data);
        $post->slug = $slug;
        $post->save();

        return redirect()->route('admin.posts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('admin.posts.edit',compact('post','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'published_at' => 'nullable|date|before_or_equal:today'
        ]);

        $data = $request->all();
        

        if($post->title != $data['title']){
            $slug = Str::slug($data['title']);;
            $slug_base = $slug;

            $counter = 1;
            $current_post = Post::where('slug',$slug)->first();
            $data['slug'] = $slug;

            while($current_post){
                $slug = $slug_base.'-'.$counter;
                $counter++;
                $current_post = Post::where('slug',$slug)->first();
            }

        }

        

        $post->update($data);

        return redirect()->route('admin.posts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index');
    }
}
