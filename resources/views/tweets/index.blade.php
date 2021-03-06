@extends('layouts.app')

@section('content')

    <div class="text-center">
        <h2><b>投稿を探す🔎</b></h2>
    </div>

    <br>

    <form action="{{ route('tweets.index') }}" method="GET">
        <div class="form-group row">
            <div class="col-sm-2 col-xs-2">
            </div>
            <div class="col-sm-2 col-xs-2">
                <label class="col-form-label">キーワード</label>
            </div>
            <div class="col-sm-4 col-xs-4">
                <input type="text" name="tweet_text" value="{{ request('tweet_text') }}" class="form-control mr-5">
            </div>
            <div class="col-sm-4 col-xs-4">
                <button type="search" class="btn btn-primary">検索</button>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-2 col-xs-2">
            </div>
            <div class="col-sm-2 col-xs-2">
                <label class="col-form-label">地域カテゴリ</label>
            </div>
            <div class="col-sm-2 col-xs-2">
                <select name="tweet_city" value="{{ request('tweet_city') }}" class="form-control">
                    <option value="">未選択</option>
                    @foreach ($allcities as $city)
                        <option value="{{ $city->id }}">{{ $city->city }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <br>
    
    <div class="text-center">
        <button class="btn btn-success" onclick=location.href='/maps'>場所を探す🔎</button>
    </div>

    <div class="container width">
        <div class="row justify-content-center">
            <div class="col-md-8 mb-3 text-right">
                <a href="{{ url('users') }}">ユーザ一覧 <i class="fas fa-users" class="fa-fw"></i> </a>
            </div>
            <div class="col-md-8 mb-3 text-left">
            <p>全{{ $timelines->count() }}件</p>
            </div>
            @if (isset($timelines))
                @foreach ($timelines as $timeline)
                    <div class="col-md-8 mb-3">
                        <div class="card">

                            <div class="card-header p-3 w-100 d-flex">
                                <img src="{{ Storage::disk('s3')->url($timeline->user->profile_image) }}" class="rounded-circle" width="50" height="50">
                                <div class="ml-2 d-flex flex-column">
                                    <p class="mb-0">{{ $timeline->user->name }}</p>
                                    <a href="{{ url('users/' .$timeline->user->id) }}" class="text-secondary">{{ $timeline->user->screen_name }}</a>
                                </div>
                                <div class="d-flex justify-content-end flex-grow-1">
                                    <p class="mb-0 text-secondary">{{ $timeline->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>

                            <img class="card-img" src="{{ Storage::disk('s3')->url($timeline->image) }}">

                            <div class="card-body">
                                <h4 class="mb-2">{{ $timeline->text_title }}</h4>
                                <div class="justify-content-flex-start">
                                    {!! nl2br(e($timeline->text)) !!}
                                    <br>
                                    <form action="{{ route('tweets.index')}}" method="GET">
                                        <input type="hidden" name="tweet_city" value="{{ $timeline->city->id }}" class="form-control">
                                        <button type="submit" class="btn p-0 border-0"><i class="fas fa-hashtag"></i>
                                            {{ $timeline->city->city }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="card-footer py-1 d-flex justify-content-end bg-white">
                                <div class="dropdown mr-3 d-flex align-items-center">
                                    @if ($timeline->user->id === Auth::user()->id)
                                        <a href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-fw"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                            <form method="POST" action="{{ url('tweets/' .$timeline->id) }}" class="mb-0">
                                                @csrf
                                                @method('DELETE')
                                                <a href="{{ url('tweets/' .$timeline->id .'/edit') }}" class="dropdown-item">編集</a>
                                                <button type="submit" class="dropdown-item del-btn">削除</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                <div class="mr-3 d-flex align-items-center">
                                    <a href="{{ url('tweets/' .$timeline->id) }}"><i class="far fa-comment fa-fw"></i></a>
                                    <p class="mb-0 text-secondary">{{ count($timeline->comments) }}</p>
                                </div>

                                <div class="d-flex align-items-center">
                                    @if (!in_array($user->id, array_column($timeline->favorites->toArray(), 'user_id'), TRUE))
                                        <form method="POST" action="{{ url('favorites/') }}" class="mb-0">
                                            @csrf
                                            <input type="hidden" name="tweet_id" value="{{ $timeline->id }}">
                                            <button type="submit" class="btn p-0 border-0 text-primary"><i class="far fa-bookmark fa-fw"></i></button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ url('favorites/' .array_column($timeline->favorites->toArray(), 'id', 'user_id')[$user->id]) }}" class="mb-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn p-0 border-0 text-danger"><i class="fas fa-bookmark fa-fw"></i></button>
                                        </form>
                                    @endif
                                    <p class="mb-0 text-secondary">{{ count($timeline->favorites) }}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        @if ($timelines->count() == 0)
            <div class="mt-5">
                <div class="text-center">
                    <p class="h2">検索結果がありませんでした</p>
                </div>
            </div>
        @endif

        <div class="my-4 d-flex justify-content-center">
            {{ $timelines->links() }}
        </div>
    </div>

@endsection