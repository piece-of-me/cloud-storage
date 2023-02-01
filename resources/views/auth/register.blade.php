@extends('auth.layout.index')

@section('content')
    <div class="pt-8 px-6 h-screen bg-slate-400 flex justify-center">
        <div class="form-container rounded-lg bg-neutral-600 w-full md:w-1/2 2xl:w-1/5 h-min">
            <form method="POST" action="{{ route('register.register') }}" class="mb-0">
                @csrf

                <div class="flex flex-col">
                    <div class="flex justify-center mt-8">
                        <img src="{{ asset('images/logo.png') }}" class="h-32 max-w-max" alt="Логотип">
                    </div>
                    <p class="text-2xl text-white text-center font-bold mb-8">Регистрация</p>

                    @error('email')
                    <div class="flex justify-center mb-3 px-2">
                        <div class="flex flex-row w-10/12">
                            <svg class="h-6 w-6 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span class="ml-1 text-red-500">{{ $message }}</span>
                        </div>
                    </div>
                    @enderror

                    <div class="flex justify-center">
                        <div class="flex flex-row pb-3 w-10/12">
                            <label for="email"
                                   class="h-12 px-1 @error('email') bg-red-500 @else bg-neutral-500 @enderror rounded-l-lg flex justify-center items-center">
                                <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </label>
                            <input type="text" name="email" id="email"
                                   class="h-12 pl-2 w-full bg-neutral-700 @error('email') border-2 border-red-500 @enderror hover:bg-neutral-800 text-green-500 text-xl rounded-r-lg"
                                   placeholder="Электронная почта" required value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="bg-neutral-700 flex justify-center py-5">
                        <button type="submit"
                                class="px-6 py-3 rounded-lg bg-green-700 hover:bg-green-500 text-xl text-white">
                        <span class="flex flex-row justify-center items-center">
                            <span class="pr-1">Зарегистрироваться</span>
                            <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                <polyline points="10 17 15 12 10 7"/>
                                <line x1="15" y1="12" x2="3" y2="12"/>
                            </svg>
                        </span>
                        </button>
                    </div>

                    <div class="bg-neutral-600">
                        <div class="flex justify-center sm:justify-around py-5 flex-col sm:flex-row">
                            <a href="{{ route('reset.index') }}" class="text-green-700 hover:text-green-500 text-center p-2">Забыли пароль?</a>
                            <a href="{{ route('login.index') }}" class="text-green-700 hover:text-green-500 text-center p-2">Авторизоваться</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection