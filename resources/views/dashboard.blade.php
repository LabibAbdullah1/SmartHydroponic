<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icon-iot.png') }}">
    <title>Smart Hidroponik - Dashboard KA</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Input disabled style */
        input:disabled,
        select:disabled {
            background-color: #f9fafb;
            /* bg-gray-50 */
            cursor: not-allowed;
            color: #9ca3af;
            /* text-gray-400 */
            border-color: #e5e7eb;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    @php
        $user = auth()->user();
        // Cek apakah user login DAN role-nya admin
        $isAdmin = $user && $user->role === 'admin';
    @endphp

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class=" text-white p-2 rounded-lg h-16 w-16">
                        <img src="{{ asset('icon-iot.png') }}" alt="">
                    </div>
                    <div>
                        <h1 class="text-md lg:text-lg font-bold text-gray-900 leading-tight">Smart Hidroponik</h1>
                        <p class="text-xs text-gray-500 font-medium">
                            powered by Labib.Dev
                        </p>
                    </div>
                </div>

                {{--  DESKTOP MENU --}}
                <div class="hidden md:block bg-white">
                    <div class="max-w-7xl mx-auto px-6 py-3 flex gap-4 items-center h-full">
                        <a href="{{ route('dashboard') }}"
                            class="px-5 py-2 rounded-md text-sm font-semibold transition
                    {{ request()->routeIs('dashboard')
                        ? 'bg-green-600 text-white shadow'
                        : 'bg-gray-100 text-gray-600 hover:bg-green-100 hover:text-green-700' }}">
                            Dashboard
                        </a>

                        <a href="{{ route('history.index') }}"
                            class="px-5 py-2 rounded-md text-sm font-semibold transition
                    {{ request()->routeIs('history.*')
                        ? 'bg-green-600 text-white shadow'
                        : 'bg-gray-100 text-gray-600 hover:bg-green-100 hover:text-green-700' }}">
                            Riwayat Tanam
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <form action="{{ route('logout') }}" method="POST" class="hidden md:block">
                            @csrf <button type="submit"
                                class="text-red-500 hover:text-red-700 text-sm font-bold transition">
                                Logout
                            </button>
                        </form>
                    @endauth

                    <span id="sysStatusBadge"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 transition-colors duration-300">
                        <span id="sysStatusDot" class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>
                        <span id="sysStatusText">Connecting...</span>
                    </span>
                    <div class="hidden md:flex flex-col items-end mr-2">
                        <span class="text-sm font-semibold text-gray-700">{{ now()->format('d M Y') }}</span>
                        <span class="text-xs text-gray-400" id="clock">00:00:00</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- NAVBAR MOBILE --}}
        <div class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t shadow-lg">
            <div class="flex h-16 justify-around items-center px-4">
                <a href="{{ route('dashboard') }}"
                    class="flex flex-col items-center justify-center text-xs transition
            {{ request()->routeIs('dashboard') ? 'text-green-600 font-bold' : 'text-gray-500 hover:text-green-600' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12l8.954-8.955a1.125 1.125 0 011.591 0L21.75 12M4.5 9.75V19.875 c0 .621.504 1.125 1.125 1.125H9.75V15 c0-.621.504-1.125 1.125-1.125h2.25 c.621 0 1.125.504 1.125 1.125v6h4.125 c.621 0 1.125-.504 1.125-1.125V9.75" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('history.index') }}"
                    class="flex flex-col items-center justify-center text-xs transition
            {{ request()->routeIs('history.*') ? 'text-green-600 font-bold' : 'text-gray-500 hover:text-green-600' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Riwayat</span>
                </a>

                @auth
                    <form action="{{ route('logout') }}" method="POST" class="flex flex-col items-center justify-center">
                        @csrf <button type="submit"
                            class="text-red-500 hover:text-red-700 flex flex-col items-center text-xs">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                @endauth
            </div>
        </div>

        {{-- INFO NUTRISI & CHART (SAMA SEPERTI SEBELUMNYA) --}}
        {{-- Saya singkat bagian ini agar tidak kepanjangan, isinya SAMA PERSIS dengan kode Anda sebelumnya --}}
        <div id="kaAlertBox"
            class="bg-white border border-gray-100 p-6 rounded-2xl shadow-sm mb-8 flex items-start gap-4 transition-all duration-500">
            <div class="flex-shrink-0 p-3 bg-gray-50 rounded-xl" id="kaIconBox">
                <svg id="kaIcon" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-md font-bold text-gray-800 mb-1" id="kaTitle">Analisis Sistem Cerdas</h3>
                <p class="text-sm text-gray-600 font-medium leading-relaxed" id="kaMessage">Menghubungkan ke server
                    KA...</p>
                <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Saran dosis dihitung berdasarkan volume air real-time & target PPM.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-md font-bold text-gray-800">Kadar Nutrisi</h3>
                        <p class="text-xs text-gray-500">Satuan Part Per Million (PPM)</p>
                    </div>
                    <span class="p-2 bg-green-50 rounded-lg text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                    </span>
                </div>
                <div class="relative h-72"><canvas id="chartPPM"></canvas></div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-md font-bold text-gray-800">Suhu Udara (Ambient)</h3>
                        <p class="text-xs text-gray-500">Suhu lingkungan sekitar tandon (°C)</p>
                    </div>
                    <span class="p-2 bg-orange-50 rounded-lg text-sky-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                        </svg>
                    </span>
                </div>
                <div class="relative h-72"><canvas id="chartSuhu"></canvas></div>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex items-center gap-2 mb-6">
                <span class="w-1 h-6 bg-blue-600 rounded-full"></span>
                <h3 class="text-xl font-bold text-gray-800">Analisa & Performa Tanaman</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- STATS BOXES (SAMA SEPERTI SEBELUMNYA) --}}
                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Suhu Hari Ini</p>
                            <h4 class="text-md font-bold text-red-600 mt-1">{{ $stats['today_max_temp'] }}° <span
                                    class="text-sm text-blue-400 font-normal">/ {{ $stats['today_min_temp'] }}°</span>
                            </h4>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-xl text-blue-500"><svg class="w-6 h-6" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg></div>
                    </div>
                    <div class="mt-4 flex gap-2 text-xs"><span
                            class="px-2 py-1 bg-red-50 text-red-600 rounded">Max</span><span
                            class="px-2 py-1 bg-blue-50 text-blue-600 rounded">Min</span></div>
                </div>

                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Usia Tanaman</p>
                            <div class="flex items-baseline mt-1">
                                <h4 class="text-md font-bold text-gray-800 text-green-600">{{ $stats['plant_ages'] }}
                                </h4>
                            </div>
                        </div>
                        <div class="bg-green-50 p-3 rounded-xl text-green-500"><svg class="w-6 h-6" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg></div>
                    </div>
                    <div class="mt-4 text-xs text-gray-500">Varietas: <span
                            class="font-bold text-gray-700">{{ $setting->plant_name }}</span></div>
                </div>

                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Rekor Panas</p>
                            <h4 class="text-md font-bold text-red-600 mt-1">{{ $stats['plant_max_temp'] }}°C</h4>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-xl text-orange-500"><svg class="w-6 h-6" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z">
                                </path>
                            </svg></div>
                    </div>
                    <div class="mt-4 text-xs bg-gray-50 p-1 rounded text-center text-gray-500">Selama periode tanam ini
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Top Nutrisi</p>
                            <h4 class="text-md font-bold text-gray-800 mt-1 text-purple-600">
                                {{ (int) $stats['plant_max_ppm'] }}</h4>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-xl text-purple-500"><svg class="w-6 h-6" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                </path>
                            </svg></div>
                    </div>
                    <div class="mt-4 flex justify-between items-center"><span class="text-xs text-gray-400">Target:
                            {{ $setting->target_ppm }} PPM</span><span
                            class="text-xs font-bold bg-purple-100 text-purple-600 px-2 py-1 rounded">PPM</span></div>
                </div>
            </div>
        </div>

        {{-- FORM KONFIGURASI --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">⚙️ Konfigurasi Lahan</h3>
                    <p class="text-sm text-gray-500">Sesuaikan parameter fisik tandon & target tanaman.</p>
                </div>
                @if (session('success'))
                    <div
                        class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-semibold flex items-center shadow-sm animate-bounce">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <form action="{{ route('settings.update') }}" method="POST" class="p-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 mb-4 border-b pb-2">
                            <span class="bg-green-100 text-green-600 p-1.5 rounded-lg"><svg class="w-6 h-6"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 22V10 M7 6c3 0 5 2 5 6 -3 0-5-2-5-6 m10 3c-3 0-5 2-5 6 3 0 5-2 5-6" />
                                </svg></span>
                            <h4 class="font-semibold text-gray-700">Target Tanaman</h4>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tanaman</label>
                            <input type="text" name="plant_name" value="{{ $setting->plant_name ?? '' }}"
                                {{ !$isAdmin ? 'disabled' : '' }}
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 transition"
                                placeholder="Contoh: Selada">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Target Nutrisi (PPM)</label>
                            <input type="number" name="target_ppm" value="{{ $setting->target_ppm ?? 800 }}"
                                {{ !$isAdmin ? 'disabled' : '' }}
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 transition">
                            <p class="text-xs text-gray-400 mt-1">Ref: Selada (560-840), Tomat (1400-3500)</p>
                        </div>
                    </div>

                    <div x-data="{ shape: '{{ old('tank_shape', $setting->tank_shape ?? 'kotak') }}' }" class="space-y-6">
                        <div class="flex items-center gap-2 mb-4 border-b pb-2">
                            <span class="bg-blue-100 text-blue-600 p-1.5 rounded-lg"><svg class="w-5 h-5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg></span>
                            <h4 class="font-semibold text-gray-700">Dimensi Fisik Tandon</h4>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bentuk Wadah</label>
                            <select name="tank_shape" x-model="shape" {{ !$isAdmin ? 'disabled' : '' }}
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 bg-white">
                                <option value="kotak">Kotak / Persegi Panjang</option>
                                <option value="tabung_tegak">Tabung Tegak (Silinder Berdiri)</option>
                                <option value="tabung_tidur">Tabung Tidur (Silinder Horizontal)</option>
                            </select>
                        </div>
                        <div x-show="shape !== 'tabung_tidur'" class="animate-fade-in-down">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi Total Wadah (cm)</label>
                            <input type="number" step="0.1" name="tank_height_cm"
                                {{ !$isAdmin ? 'disabled' : '' }}
                                value="{{ old('tank_height_cm', $setting->tank_height_cm ?? 30) }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 transition"
                                placeholder="Jarak dasar sampai bibir atas">
                        </div>
                        <div x-show="shape === 'tabung_tegak' || shape === 'tabung_tidur'" style="display: none;"
                            class="animate-fade-in-down">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diameter (cm) <span
                                    x-show="shape === 'tabung_tidur'" class="text-xs text-blue-500">(Sekaligus Tinggi
                                    Wadah)</span></label>
                            <input type="number" step="0.1" name="tank_diameter"
                                {{ !$isAdmin ? 'disabled' : '' }}
                                value="{{ old('tank_diameter', $setting->tank_diameter ?? 40) }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5"
                                placeholder="Garis tengah lingkaran">
                        </div>
                        <div class="grid grid-cols-2 gap-4 animate-fade-in-down">
                            <div x-show="shape === 'kotak' || shape === 'tabung_tidur'">
                                <label class="block text-xs text-gray-500 mb-1">Panjang (cm)</label>
                                <input type="number" step="0.1" name="tank_length"
                                    {{ !$isAdmin ? 'disabled' : '' }}
                                    value="{{ old('tank_length', $setting->tank_length ?? 50) }}"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                            </div>
                            <div x-show="shape === 'kotak'">
                                <label class="block text-xs text-gray-500 mb-1">Lebar (cm)</label>
                                <input type="number" step="0.1" name="tank_width"
                                    {{ !$isAdmin ? 'disabled' : '' }}
                                    value="{{ old('tank_width', $setting->tank_width ?? 30) }}"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                            </div>
                        </div>

                        {{-- TOMBOL CANGGIH (LOGIN / SIMPAN) --}}
                        <div class="mt-8 flex justify-center md:justify-end">
                            @if ($isAdmin)
                                {{-- TOMBOL SIMPAN (Admin) - Tetap Biru --}}
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 font-semibold flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                        </path>
                                    </svg>
                                    <span class="text-sm">Simpan Konfigurasi</span>
                                </button>
                            @else
                                {{-- TOMBOL LOGIN (Tamu) - Diubah jadi Hijau --}}
                                <div class="flex flex-col items-center gap-2">

                                    <button type="button" onclick="showLoginPopup()"
                                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 font-semibold flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <span class="text-sm">Buka Akses Admin (Edit)</span>
                                    </button>

                                    @if (!$isAdmin)
                                        <p class="text-xs text-center text-gray-400 max-w-[200px] leading-tight">
                                            Data hanya dapat diubah oleh Administrator terdaftar.
                                        </p>
                                    @endif

                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- TOMBOL PANEN (HANYA MUNCUL JIKA ADMIN) --}}
        @if ($isAdmin)
            <div
                class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-8 mb-8 text-white flex flex-col md:flex-row justify-between items-center gap-6 mt-4">
                <div>
                    <h3 class="text-xl font-bold mb-2">🌱 Selesai Masa Tanam?</h3>
                    <p class="text-blue-100 max-w-xl text-sm">Jika Anda melakukan panen atau mengganti tanaman, tekan
                        tombol panen & simpan rapor. Sistem akan <span class="font-bold text-white">menganalisis
                            data</span>, menghitung skor kualitas nutrisi, dan menyimpannya ke halaman Riwayat.</p>
                </div>
                <form id="finishForm" action="{{ route('planting.finish') }}" method="POST">
                    @csrf
                    <button type="button" id="finishBtn"
                        class="bg-white text-blue-700 hover:bg-gray-100 px-8 py-4 rounded-xl font-bold shadow-md transition-all transform hover:scale-105 flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm">Panen & Simpan Rapor</span>
                    </button>
                </form>
            </div>
        @endif

        <footer class="mt-12 text-center text-sm text-gray-400 pb-12 md:pb-4">
            &copy; {{ date('Y') }} Smart Hidroponik System. Developed with Laravel & IoT Tech.
        </footer>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Jam Digital Simple
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString();
        }, 1000);

        // --- CHART & REALTIME LOGIC (Disingkat karena tidak berubah) ---
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    grid: {
                        borderDash: [2, 4],
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    radius: 0,
                    hoverRadius: 6
                },
                line: {
                    borderWidth: 3
                }
            }
        };
        const ctxPPM = document.getElementById('chartPPM').getContext('2d');
        const chartPPM = new Chart(ctxPPM, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'PPM',
                    data: [],
                    borderColor: '#10b981',
                    backgroundColor: (context) => {
                        const ctx = context.chart.ctx;
                        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
                        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4
                }]
            },
            options: commonOptions
        });
        const ctxSuhu = document.getElementById('chartSuhu').getContext('2d');
        const chartSuhu = new Chart(ctxSuhu, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Suhu',
                    data: [],
                    borderColor: '#3b82f6',
                    backgroundColor: (context) => {
                        const ctx = context.chart.ctx;
                        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');
                        return gradient;
                    },
                    fill: true,
                    tension: 0.4
                }]
            },
            options: commonOptions
        });

// --- REALTIME UPDATE LOGIC (PERBAIKAN PRIORITAS) ---
        function updateCharts() {
            fetch('/sensor-data')
                .then(response => response.json())
                .then(data => {
                    // 1. Update Chart Data (Jika ada)
                    if (data.labels && data.labels.length > 0) {
                        chartPPM.data.labels = data.labels;
                        chartPPM.data.datasets[0].data = data.ppm;
                        chartPPM.update('none');

                        chartSuhu.data.labels = data.labels;
                        chartSuhu.data.datasets[0].data = data.temp;
                        chartSuhu.update('none');
                    }

                    // Ambil Elemen HTML
                    const box = document.getElementById('kaAlertBox');
                    const msg = document.getElementById('kaMessage');
                    const title = document.getElementById('kaTitle');
                    const iconBox = document.getElementById('kaIconBox');
                    const icon = document.getElementById('kaIcon');

                    const badge = document.getElementById('sysStatusBadge');
                    const dot = document.getElementById('sysStatusDot');
                    const text = document.getElementById('sysStatusText');

                    // Reset Class Dasar dulu
                    box.className = "p-6 rounded-2xl shadow-sm mb-8 flex items-start gap-4 transition-all duration-500 border";
                    iconBox.className = "flex-shrink-0 p-3 rounded-xl";

                    // ===============================================
                    // LOGIKA UTAMA: CEK OFFLINE DULUAN (PRIORITAS 1)
                    // ===============================================

                    if (!data.is_online) {
                        // --- SKENARIO 1: SISTEM OFFLINE (ABU-ABU) ---

                        // 1. Ubah Tampilan Box jadi Abu-abu
                        box.classList.add('bg-gray-50', 'border-gray-200');
                        iconBox.classList.add('bg-gray-200', 'text-gray-500');

                        title.innerText = "Sistem Offline";
                        title.className = "text-md font-bold text-gray-600 mb-1";
                        msg.innerText = "Perangkat tidak terhubung. Data yang ditampilkan adalah data terakhir.";
                        msg.className = "text-sm text-gray-500 font-medium leading-relaxed";

                        // Icon Awan Silang / Mati
                        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />';

                        // 2. Ubah Badge jadi Merah
                        text.innerText = "System Offline";
                        badge.className = "inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 transition-colors duration-300";
                        dot.className = "w-2 h-2 bg-red-500 rounded-full mr-2"; // Tidak berkedip

                    } else {
                        // --- SKENARIO 2: SISTEM ONLINE (HIJAU/KUNING/MERAH) ---

                        // 1. Ubah Badge jadi Hijau (Online)
                        text.innerText = "System Online";
                        badge.className = "inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 transition-colors duration-300";
                        dot.className = "w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"; // Berkedip

                        // 2. Baru Cek Status Nutrisi (KA)
                        msg.innerText = data.ka_message;

                        if (data.ka_status === 'WARNING' || data.ka_status === 'ERROR') {
                            // KUNING
                            box.classList.add('bg-yellow-50', 'border-yellow-200');
                            iconBox.classList.add('bg-yellow-100', 'text-yellow-600');
                            title.innerText = "Tindakan Diperlukan";
                            title.className = "text-md font-bold text-yellow-600 mb-1";
                            msg.className = "text-sm text-yellow-600 font-medium leading-relaxed";
                            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';

                        } else if (data.ka_status === 'OVER') {
                            // MERAH (Overdosis)
                            box.classList.add('bg-red-50', 'border-red-600');
                            iconBox.classList.add('bg-red-200', 'text-red-600');
                            title.innerText = "Nutrisi Berlebih";
                            title.className = "text-md font-bold text-red-800 mb-1";
                            msg.className = "text-sm text-red-700 font-medium leading-relaxed";
                            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';

                        } else {
                            // HIJAU (Optimal)
                            box.classList.add('bg-white', 'border-green-600');
                            iconBox.classList.add('bg-green-200', 'text-green-600');
                            title.innerText = "Kondisi Optimal";
                            title.className = "text-md font-bold text-green-600 mb-1";
                            msg.className = "text-sm text-green-600 font-medium leading-relaxed";
                            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);

                    // FALLBACK: Jika Fetch Error (Server Mati), Paksa Tampilan Offline
                    const box = document.getElementById('kaAlertBox');
                    const title = document.getElementById('kaTitle');
                    const msg = document.getElementById('kaMessage');
                    const iconBox = document.getElementById('kaIconBox');
                    const badge = document.getElementById('sysStatusBadge');
                    const dot = document.getElementById('sysStatusDot');
                    const text = document.getElementById('sysStatusText');

                    // Style Box Error
                    box.className = "p-6 rounded-2xl shadow-sm mb-8 flex items-start gap-4 transition-all duration-500 border bg-gray-50 border-gray-200";
                    iconBox.className = "flex-shrink-0 p-3 rounded-xl bg-gray-200 text-gray-500";
                    title.innerText = "Connection Error";
                    title.className = "text-md font-bold text-gray-600 mb-1";
                    msg.innerText = "Gagal terhubung ke server.";

                    // Style Badge Error
                    text.innerText = "Server Error";
                    badge.className = "inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800";
                    dot.className = "w-2 h-2 bg-red-500 rounded-full mr-2";
                });
        }
        updateCharts();
        setInterval(updateCharts, 2000);

        // --- LOGIC TOMBOL FINISH (Hanya jika admin) ---
        const finishBtn = document.getElementById('finishBtn');
        if (finishBtn) {
            finishBtn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Akhiri Sesi Tanam?',
                    text: 'Data statistik akan diarsipkan ke Riwayat dan tidak dapat diubah.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Panen & Simpan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl p-6',
                        title: 'text-sm md:text-lg font-bold text-gray-800',
                        htmlContainer: 'text-xs md:text-md text-gray-600 leading-relaxed mt-2',
                        confirmButton: 'bg-blue-600 hover:bg-blue-700 ml-2 text-white text-sm px-5 py-2 rounded-xl',
                        cancelButton: 'bg-gray-200 hover:bg-gray-300 mr-2 text-gray-700 text-sm px-5 py-2 rounded-xl ml-2'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('finishForm').submit();
                    }
                });
            });
        }

        // --- LOGIC POPUP LOGIN AJAX (STYLED) ---
        function showLoginPopup() {
            Swal.fire({
                title: '🔐 Login Admin',
                // HTML Form dengan styling Tailwind pada Inputnya
                html: `
            <div class="text-left mt-2">
                <p class="text-sm text-gray-500 mb-5 text-center leading-relaxed">
                    Masukkan kredensial administrator untuk membuka gembok konfigurasi.
                </p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 ml-1">Email Address</label>
                        <input type="email" id="swal-input1"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 block p-3 outline-none transition-all placeholder-gray-400"
                            placeholder="admin@hidroponik.com">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 ml-1">Password</label>
                        <input type="password" id="swal-input2"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 block p-3 outline-none transition-all placeholder-gray-400"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Masuk Sistem',
                cancelButtonText: 'Batal',
                reverseButtons: true, // Posisi tombol Batal di kiri, Masuk di kanan
                buttonsStyling: false, // WAJIB: Matikan style bawaan SweetAlert

                // --- CLASS TAILWIND CUSTOM ---
                customClass: {
                    popup: 'rounded-3xl p-8 w-full max-w-sm bg-white shadow-2xl',
                    title: 'text-xl md:text-2xl font-bold text-gray-800 tracking-tight',
                    htmlContainer: 'text-sm text-gray-600',
                    // Tombol Confirm (Hijau)
                    confirmButton: 'bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-xl text-sm transition-all shadow-md hover:shadow-lg focus:ring-4 focus:ring-green-200 ml-3',
                    // Tombol Cancel (Abu-abu)
                    cancelButton: 'bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-3 px-6 rounded-xl text-sm transition-all focus:ring-4 focus:ring-gray-100'
                },

                focusConfirm: false,
                preConfirm: () => {
                    const email = Swal.getPopup().querySelector('#swal-input1').value
                    const password = Swal.getPopup().querySelector('#swal-input2').value

                    if (!email || !password) {
                        Swal.showValidationMessage(`⚠️ Harap isi Email dan Password`);
                        return false;
                    }

                    // AJAX Login Request
                    return fetch('{{ route('login.ajax') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                email: email,
                                password: password
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.statusText)
                            }
                            return response.json()
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Login Gagal: Email atau Password Salah`)
                        })
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Toast Sukses Kecil sebelum Reload
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'success',
                        title: 'Login Berhasil! Mengaktifkan mode edit...'
                    }).then(() => {
                        location.reload(); // Reload halaman
                    });
                }
            })
        }
    </script>
</body>

</html>
