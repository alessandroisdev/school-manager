<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SGE') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.ts'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}">
                                <h2 class="text-2xl font-black text-blue-600 tracking-tight">SGE</h2>
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex items-center">
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-blue-600 border-b-2 border-blue-600 font-medium' : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent' }} px-1 py-5 text-sm transition-colors">
                                Dashboard
                            </a>
                            <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'text-blue-600 border-b-2 border-blue-600 font-medium' : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent' }} px-1 py-5 text-sm transition-colors">
                                Gestão de Alunos
                            </a>
                        </div>
                    </div>

                    <!-- Settings Dropdown & Unit Selector -->
                    <div class="flex items-center space-x-4">
                        @if(Auth::user()->units->count() > 1)
                            <form method="POST" action="{{ route('unit.switch') }}">
                                @csrf
                                <select name="unit_id" onchange="this.form.submit()" class="border-gray-300 rounded-md text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach(Auth::user()->units as $unit)
                                        <option value="{{ $unit->id }}" {{ session('active_unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @elseif(Auth::user()->units->count() == 1)
                            <span class="text-sm text-gray-600 font-medium bg-gray-100 px-3 py-1 rounded-full">{{ Auth::user()->units->first()->name }}</span>
                        @endif

                        <div class="border-l border-gray-300 h-6 mx-2"></div>

                        <span class="text-sm font-semibold text-gray-700">{{ Auth::user()->name }}</span>

                        <form method="POST" action="{{ route('logout') }}" class="ml-4">
                            @csrf
                            <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-8">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
