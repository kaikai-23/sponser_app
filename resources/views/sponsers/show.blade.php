<x-app-layout>
    <div class="container lg:w-3/4 md:w-4/5 w-11/12 mx-auto my-8 px-8 py-4 bg-white shadow-md">

        <x-flash-message :message="session('notice')" />

        <x-validation-errors :errors="$errors" />
        <article class="mb-2">
            <h2 class="font-bold font-sans break-normal text-gray-900 pt-6 pb-1 text-3xl md:text-4xl">{{ $sponser->title }}</h2>
            <p class="text-sm mb-2 md:text-base font-normal text-gray-600">
                投稿者: {{ $sponser->user->name }}<br>
                カテゴリー: {{ $sponser->category->name }} <br>
                <!--<span class="text-red-400 font-bold">{{ date('Y-m-d H:i:s', strtotime('-1 day')) < $sponser->created_at ? 'NEW' : '' }}</span>-->
                募集日: {{ $sponser->created_at }}
            </p>
            <img src="{{ $sponser->image_url}}" alt="" class="mb-4">
            <p class="text-gray-700 text-base">{!! nl2br(e($sponser->body)) !!}</p>
        </article>
        <div class="flex flex-row text-center my-4">
            @can('update', $sponser)
                <a href="{{ route('sponsers.edit', $sponser) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20 mr-2">編集</a>
            @endcan
            @can('delete', $sponser)
                <form action="{{ route('sponsers.destroy', $sponser) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="submit" value="削除" onclick="if(!confirm('削除しますか？')){return false};" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-20">
                </form>
            @endcan
        </div>
    </div>
</x-app-layout>
