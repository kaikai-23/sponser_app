<?php

namespace App\Http\Controllers;

use App\Http\Requests\SponserRequest;
use App\Models\Category;
use App\Models\Sponser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SponserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sponsers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('sponsers.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param   \App\Http\Requests\SponserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SponserRequest $request)
    {
        $sponser = new Sponser($request->all());
        $sponser->user_id = $request->user()->id;

        $file = $request->file('image');
        $sponser->image = date('YmdHis') . '_' . $file->getClientOriginalName();

        DB::beginTransaction();
        try{
            $sponser->save();
            if(!storage::putFileAs('images/sponsers', $file, $sponser->image)){
                throw new \Exception('画像ファイルの保存に失敗しました。');
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()
            ->route('sponsers.show', $sponser)
            ->with('notice', '記事を登録しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sponser = Sponser::find($id);
        return view('sponsers.show', compact('sponser'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param   \App\Http\Requests\SponserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SponserRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
