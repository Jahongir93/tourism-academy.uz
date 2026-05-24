@php
    $headerMenu = \App\Models\CmsMenu::where('location', 'header')->where('is_active', true)->first();
    $menuItems = $headerMenu ? $headerMenu->menuItems()->whereNull('parent_id')->where('is_active', true)->orderBy('order_position')->with('children')->get() : collect();
@endphp

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Tourism Academy">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                {{-- Bosh sahifa --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('home') }}">
                        @if($locale == 'uz') Bosh sahifa
                        @elseif($locale == 'ru') Главная
                        @else Home
                        @endif
                    </a>
                </li>

                {{-- CMS menyu elementlari --}}
                @foreach($menuItems as $item)
                    @if($item->children->count() > 0)
                        {{-- Dropdown menyu --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                @if($locale == 'uz') {{ $item->title_uz }}
                                @elseif($locale == 'ru') {{ $item->title_ru }}
                                @else {{ $item->title_en }}
                                @endif
                            </a>
                            <ul class="dropdown-menu">
                                {{-- Asosiy sahifaga link --}}
                                <li>
                                    <a class="dropdown-item" href="{{ $item->url }}">
                                        @if($locale == 'uz') {{ $item->title_uz }}
                                        @elseif($locale == 'ru') {{ $item->title_ru }}
                                        @else {{ $item->title_en }}
                                        @endif
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                {{-- Sub-menyu elementlari --}}
                                @foreach($item->children->where('is_active', true)->sortBy('order_position') as $child)
                                    <li>
                                        <a class="dropdown-item" href="{{ $child->url }}">
                                            @if($locale == 'uz') {{ $child->title_uz }}
                                            @elseif($locale == 'ru') {{ $child->title_ru }}
                                            @else {{ $child->title_en }}
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        {{-- Oddiy menyu elementi --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is(ltrim($item->url, '/')) ? 'active' : '' }}" href="{{ $item->url }}">
                                @if($locale == 'uz') {{ $item->title_uz }}
                                @elseif($locale == 'ru') {{ $item->title_ru }}
                                @else {{ $item->title_en }}
                                @endif
                            </a>
                        </li>
                    @endif
                @endforeach

                {{-- Til tanlash --}}
                <li class="nav-item dropdown lang-switcher">
                    <button class="btn dropdown-toggle" data-bs-toggle="dropdown">
                        @if($locale == 'uz') <i class="fas fa-globe"></i> O'zbekcha
                        @elseif($locale == 'ru') <i class="fas fa-globe"></i> Русский
                        @else <i class="fas fa-globe"></i> English
                        @endif
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('lang.switch', 'uz') }}">O'zbekcha</a></li>
                        <li><a class="dropdown-item" href="{{ route('lang.switch', 'ru') }}">Русский</a></li>
                        <li><a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">English</a></li>
                    </ul>
                </li>

                {{-- Login/Dashboard --}}
                @auth
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white ms-2 px-3" href="{{ url('/dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary ms-2 px-3" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            @if($locale == 'uz') Kirish
                            @elseif($locale == 'ru') Вход
                            @else Login
                            @endif
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
