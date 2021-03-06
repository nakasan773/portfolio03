@extends('layouts.app')

@section('content')

    <br>

    <br>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">コメント編集画面</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('tweets.update', ['tweets' => $tweets]) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group row mb-0">
                                <div class="col-md-12 p-3 w-100 d-flex">
                                    <img src="{{ Storage::disk('s3')->url($user->profile_image) }}" class="rounded-circle" width="50" height="50">
                                    <div class="ml-2 d-flex flex-column">
                                        <p class="mb-0">{{ $user->name }}</p>
                                        <a href="{{ url('users/' .$user->id) }}" class="text-secondary">{{ $user->screen_name }}</a>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    タイトル
                                    <br>
                                    <input class="mt-1" name="text_title_edit" rows="4" value="{{ $tweets->text_title }}">
                                    <div class="col-md-5 text-right ml-5">
                                        <p class="mb-2 text-danger">12文字以内</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @if ($errors->has('text_title_edit'))
                                        <div class="row justify-content-left">
                                            <div class="cal-xs-4">
                                                <span style="color:red">{{ $errors->first('text_title_edit') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-12 mt-3">
                                    内容
                                    <textarea class="form-control mt-1" name="text_edit" rows="4">{{ old('text') ? : $tweets->text }}</textarea>
                                    @if ($errors->has('text_edit'))
                                        <div class="row justify-content-left">
                                            <div class="cal-xs-4">
                                                <span style="color:red">{{ $errors->first('text_edit') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-12 text-right">
                                    <p class="mb-4 text-danger">140文字以内</p>
                                    <button type="submit" class="btn btn-primary">
                                        変更する
                                    </button>
                                </div>
                            </div>

                            <br>

                            <div class="form-group row mb-0">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary" onclick=location.href='/users'>
                                        　戻る　
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection