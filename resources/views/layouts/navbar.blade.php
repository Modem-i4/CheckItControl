<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm fixed-top w-100 py-0" style="z-index: 1500">
    <div class="container">

        <a class="navbar-brand" href="{{ url('/') }}">
            {{ __(config('app.name', 'CheckIt для Вчителів')) }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            @if(Auth::user() != null)
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a href="/quizzes" class="text-dark ml-2 d-flex">
                            <h4 class="mdi mdi-checkbox-multiple-marked mb-0 h4"> {{ __("Квізи") }}</h4>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/lessons" class="text-dark ml-2 d-flex">
                            <h4 class="mdi mdi-school mb-0 h4"> {{ __("Уроки") }}</h4>
                        </a>
                    </li>
                    <li class="nav-item">
                        @if(Auth::user()->hasRole("premium"))
                        <a href="/groups" class="text-dark ml-2 d-flex">
                        @else
                        <a href="#" class="ml-2 d-flex text-decoration-none text-muted" style="cursor: default">
                        @endif
                            <h4 class="mdi mdi-account-group mb-0 h4"> {{ __("Класи") }}</h4>
                        </a>
                    </li>
                    <li class="nav-item">
                        @if(Auth::user()->hasRole("premium"))
                        <a href="/school" class="text-dark ml-2 d-flex">
                        @else
                        <a href="#" class="ml-2 d-flex text-decoration-none text-muted" style="cursor: default">
                        @endif
                            <h4 class="mdi mdi-account-group mb-0 h4"> {{ __("Школа") }}</h4>
                        </a>
                    </li>
                </ul>
        @endif

        <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <lang-changer></lang-changer>
                </li>
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __("Увійти") }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __("Зареєструватися") }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre style="font-size: 0.9rem;">
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __("Вийти") }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
