@php
    $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout');
    $profile_url = View::getSection('profile_url') ?? config('adminlte.profile_url', 'logout');

    if (Auth::check()) {
        if (config('adminlte.usermenu_profile_url', false)) {
            $profile_url = Auth::user()->adminlte_profile_url();
        }
    }

    if (config('adminlte.use_route_url', false)) {
        $profile_url = $profile_url ? route($profile_url) : '';
        $logout_url = $logout_url ? route($logout_url) : '';
    } else {
        $profile_url = $profile_url ? url($profile_url) : '';
        $logout_url = $logout_url ? url($logout_url) : '';
    }

    $opdRankInfo = null;
    if (Auth::check()) {
        $activeOpdId = request('opd') ?? (request('filter_opd') ?? Auth::user()->id_opd);
        $currentMonth = null;
        $currentYear = null;
        if (request('month')) {
            $parts = explode('-', request('month'));
            if (count($parts) === 2) {
                $currentYear = $parts[0];
                $currentMonth = $parts[1];
            }
        } elseif (request('filter_month')) {
            $currentMonth = request('filter_month');
            $currentYear = request('filter_year');
        }
        $opdRankInfo = $activeOpdId ? \App\Models\Opd::getRank($activeOpdId, $currentMonth, $currentYear) : null;
    }
@endphp

<li class="nav-item dropdown user-menu">

    {{-- User menu toggler --}}
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" style="display: flex; align-items: center; padding-top: 10px; padding-bottom: 10px;">
        @if(config('adminlte.usermenu_image'))
            <img src="{{ Auth::user()->adminlte_image() }}"
                 class="user-image img-circle elevation-2"
                 alt="{{ Auth::user()->name }}">
        @endif
        <span @if(config('adminlte.usermenu_image')) class="d-none d-md-inline" @endif style="text-align: right; line-height: 1.1;">
            <div style="font-weight: 500;">a.n. {{ Auth::user()->name }}</div>
            <small id="navbar-opd-rank" class="text-muted font-weight-bold" style="font-size: 10px; display: {{ $opdRankInfo ? 'block' : 'none' }}; margin-top: 1px;">
                <i class="fas fa-trophy text-warning mr-1"></i>Peringkat: #<span class="rank-val">{{ $opdRankInfo['rank'] ?? '' }}</span>
            </small>
        </span>
    </a>

    {{-- User menu dropdown --}}
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

        {{-- User menu header --}}
        @if(!View::hasSection('usermenu_header') && config('adminlte.usermenu_header'))
            <li class="user-header {{ config('adminlte.usermenu_header_class', 'bg-primary') }}
                @if(!config('adminlte.usermenu_image')) h-auto @endif">
                @if(config('adminlte.usermenu_image'))
                    <img src="{{ Auth::user()->adminlte_image() }}"
                          class="img-circle elevation-2"
                          alt="{{ Auth::user()->name }}">
                @endif
                <p class="@if(!config('adminlte.usermenu_image')) mt-0 @endif">
                    {{ Auth::user()->name }}
                    <small id="navbar-dropdown-opd-rank" class="d-block text-white-50 mt-1" style="font-size: 12px; display: {{ $opdRankInfo ? 'block' : 'none' }} !important;">
                        Peringkat Keuangan: <strong>#<span class="rank-val">{{ $opdRankInfo['rank'] ?? '' }}</span></strong> dari <span class="total-val">{{ $opdRankInfo['total'] ?? '' }}</span>
                    </small>
                    @if(config('adminlte.usermenu_desc'))
                        <small>{{ Auth::user()->adminlte_desc() }}</small>
                    @endif
                </p>
            </li>
        @else
            @yield('usermenu_header')
        @endif

        {{-- Configured user menu links --}}
        @each('adminlte::partials.navbar.dropdown-item', $adminlte->menu("navbar-user"), 'item')

        {{-- User menu body --}}
        @hasSection('usermenu_body')
            <li class="user-body">
                @yield('usermenu_body')
            </li>
        @endif

        {{-- User menu footer --}}
        <li class="user-footer">
            @if($profile_url)
                <a href="{{ $profile_url }}" class="btn btn-default btn-flat">
                    <i class="fa fa-fw fa-user"></i>
                    {{ __('adminlte::menu.profile') }}
                </a>
            @endif
            <a class="btn btn-default btn-flat float-right @if(!$profile_url) btn-block @endif"
               href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-fw fa-power-off"></i>
                {{ __('adminlte::adminlte.log_out') }}
            </a>
            <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
                @if(config('adminlte.logout_method'))
                    {{ method_field(config('adminlte.logout_method')) }}
                @endif
                {{ csrf_field() }}
            </form>
        </li>

    </ul>

</li>
