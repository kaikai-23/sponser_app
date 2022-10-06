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
        $sponsers = Sponser::with('user')->latest()->paginate(4);
        return view('sponsers.index', compact('sponsers'));
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
        $sponser->image = self::createFileName($file);

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
        $sponser = Sponser::find($id);
        $categories = Category::all();
        return view('sponsers.edit', compact('sponser','categories'));
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
        $sponser = Sponser::find($id);

        if ($request->user()->cannot('update', $sponser)) {
            return redirect()->route('sponsers.show', $sponser)
                ->withErrors('自分の記事以外は更新できません');
        }

        $file = $request->file('image');
        if ($file) {
            $delete_file_path = 'images/sponsers/' . $sponser->image;
            $sponser->image = self::createFileName($file);
        }
        $sponser->fill($request->all());

        // トランザクション開始
        DB::beginTransaction();
        try {
            // 更新
            $sponser->save();

            if ($file) {
              // 画像アップロード
            if (!Storage::putFileAs('images/sponsers', $file, $sponser->image)) {
                  // 例外を投げてロールバックさせる
            throw new \Exception('画像ファイルの保存に失敗しました。');
            }

              // 画像削除
            if (!Storage::delete($delete_file_path)) {
                  //アップロードした画像を削除する
                Storage::delete('images/sponsers/' . $sponser->image);
                  //例外を投げてロールバックさせる
                throw new \Exception('画像ファイルの削除に失敗しました。');
                }
            }

            // トランザクション終了(成功)
            DB::commit();
        } catch (\Exception $e) {
            // トランザクション終了(失敗)
            DB::rollback();
            return back()->withInput()->withErrors($e->getMessage());
        }

        return redirect()->route('sponsers.show', $sponser)
            ->with('notice', '記事を更新しました');
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
    private static function createFileName($file)
    {
        return date('YmdHis') . '_' . $file->getClientOriginalName();
    }
}
