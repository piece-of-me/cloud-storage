@extends('auth.layout.index')

@section('content')
    <div class="pt-8 px-6 h-screen bg-slate-400 flex justify-center">
        <div class="rounded-lg bg-neutral-600 w-full md:w-1/2 2xl:w-1/5 h-min px-1">
            <div class="flex flex-col">
                <div class="flex justify-center mt-8">
                    <img src="{{ asset('images/logo.png') }}" class="h-32 max-w-max">
                </div>
                <p class="text-2xl text-white text-center font-bold mb-8">Поздравляем</p>

                <div class="text-lg text-white text-center font-semibold">
                    Пароль для учетной записи был сброшен. Новый пароль отправлен на почту <i>{{ $email }}</i>
                </div>

                <div class="bg-neutral-600">
                    <div class="flex justify-center sm:justify-around py-5 mb-8 flex-col sm:flex-row">
                        <a href="{{ route('login.index') }}" class="text-green-700 hover:text-green-500 text-center p-2">Авторизоваться</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection