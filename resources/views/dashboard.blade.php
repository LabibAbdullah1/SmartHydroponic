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
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class=" text-white p-2 rounded-lg h-16 w-16">
                        <img src="{{ asset('icon-iot.png') }}" alt="">
                    </div>
                    <div>
                        <h1 class="text-md lg:text-lg font-bold text-gray-900 leading-tight">Smart Hidroponik</h1>
                        <p class="text-xs text-gray-500 font-medium">Powered by Labib.Dev</p>
                    </div>
                </div>
                {{--  DESKTOP --}}
                <div class="hidden md:block bg-white">
                    <div class="max-w-7xl mx-auto px-6 py-3 flex gap-4">

                        {{-- Dashboard --}}
                        <a href="{{ route('dashboard') }}"
                            class="px-5 py-2 rounded-md text-sm font-semibold transition
                    {{ request()->routeIs('dashboard')
                        ? 'bg-green-600 text-white shadow'
                        : 'bg-gray-100 text-gray-600 hover:bg-green-100 hover:text-green-700' }}">
                            Dashboard
                        </a>

                        {{-- Riwayat Tanam --}}
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
            <div class="flex h-16">

                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                    class="flex-1 flex flex-col items-center justify-center text-xs transition
            {{ request()->routeIs('dashboard') ? 'text-green-600 font-bold' : 'text-gray-500 hover:text-green-600' }}">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.125 1.125 0 011.591 0L21.75 12M4.5 9.75V19.875
                    c0 .621.504 1.125 1.125 1.125H9.75V15
                    c0-.621.504-1.125 1.125-1.125h2.25
                    c.621 0 1.125.504 1.125 1.125v6h4.125
                    c.621 0 1.125-.504 1.125-1.125V9.75" />
                    </svg>

                    <span>Dashboard</span>
                </a>

                {{-- Riwayat Tanam --}}
                <a href="{{ route('history.index') }}"
                    class="flex-1 flex flex-col items-center justify-center text-xs transition
            {{ request()->routeIs('history.*') ? 'text-green-600 font-bold' : 'text-gray-500 hover:text-green-600' }}">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                    <span>Riwayat</span>
                </a>

            </div>
        </div>

        {{-- Alert Kondisi Nutrisi --}}
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
                <div class="relative h-72">
                    <canvas id="chartPPM"></canvas>
                </div>
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
                <div class="relative h-72">
                    <canvas id="chartSuhu"></canvas>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex items-center gap-2 mb-6">
                <span class="w-1 h-6 bg-blue-600 rounded-full"></span>
                <h3 class="text-xl font-bold text-gray-800">Analisa & Performa Tanaman</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Suhu Hari Ini</p>
                            <h4 class="text-md font-bold text-red-600 mt-1">
                                {{ $stats['today_max_temp'] }}° <span class="text-sm text-blue-400 font-normal">/
                                    {{ $stats['today_min_temp'] }}°</span>
                            </h4>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-xl text-blue-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2 text-xs">
                        <span class="px-2 py-1 bg-red-50 text-red-600 rounded">Max</span>
                        <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded">Min</span>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Usia Tanaman</p>
                            <div class="flex items-baseline mt-1">
                                <h4 class="text-md font-bold text-gray-800 text-green-600">
                                    {{ $stats['plant_ages'] }}
                                </h4>
                            </div>
                        </div>
                        <div class="bg-green-50 p-3 rounded-xl text-green-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-500">
                        Varietas: <span class="font-bold text-gray-700">{{ $setting->plant_name }}</span>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Rekor Panas</p>
                            <h4 class="text-md font-bold text-red-600 mt-1">{{ $stats['plant_max_temp'] }}°C</h4>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-xl text-orange-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs bg-gray-50 p-1 rounded text-center text-gray-500">
                        Selama periode tanam ini
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Top Nutrisi</p>
                            <h4 class="text-md font-bold text-gray-800 mt-1 text-purple-600">
                                {{ (int) $stats['plant_max_ppm'] }}
                            </h4>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-xl text-purple-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-xs text-gray-400">Target: {{ $setting->target_ppm }} PPM</span>
                        <span class="text-xs font-bold bg-purple-100 text-purple-600 px-2 py-1 rounded">PPM</span>
                    </div>
                </div>
            </div>
        </div>

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
                            <span class="bg-green-100 text-green-600 p-1.5 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22V10
                                        M7 6c3 0 5 2 5 6
                                        -3 0-5-2-5-6
                                        m10 3c-3 0-5 2-5 6
                                        3 0 5-2 5-6" />
                                </svg>
                            </span>
                            <h4 class="font-semibold text-gray-700">Target Tanaman</h4>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tanaman</label>
                            <input type="text" name="plant_name" value="{{ $setting->plant_name ?? '' }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 transition"
                                placeholder="Contoh: Selada">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Target Nutrisi (PPM)</label>
                            <input type="number" name="target_ppm" value="{{ $setting->target_ppm ?? 800 }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 transition">
                            <p class="text-xs text-gray-400 mt-1">Ref: Selada (560-840), Tomat (1400-3500)</p>
                        </div>
                    </div>

                    <div x-data="{ shape: '{{ $setting->tank_shape ?? 'kotak' }}' }" class="space-y-6">
                        <div class="flex items-center gap-2 mb-4 border-b pb-2">
                            <span class="bg-blue-100 text-blue-600 p-1.5 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </span>
                            <h4 class="font-semibold text-gray-700">Dimensi Fisik Tandon</h4>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi Total Wadah (cm)</label>
                            <input type="number" step="0.1" name="tank_height_cm"
                                value="{{ $setting->tank_height_cm ?? 30 }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bentuk Wadah</label>
                            <select name="tank_shape" x-model="shape"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 bg-white">
                                <option value="kotak">Kotak / Persegi Panjang</option>
                                <option value="tabung">Tabung / Silinder</option>
                            </select>
                        </div>

                        <div x-show="shape === 'kotak'" class="grid grid-cols-2 gap-4 animate-fade-in-down">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Panjang (cm)</label>
                                <input type="number" step="0.1" name="tank_length"
                                    value="{{ $setting->tank_length ?? 50 }}"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Lebar (cm)</label>
                                <input type="number" step="0.1" name="tank_width"
                                    value="{{ $setting->tank_width ?? 30 }}"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5">
                            </div>
                        </div>

                        <div x-show="shape === 'tabung'" style="display: none;" class="animate-fade-in-down">
                            <label class="block text-xs text-gray-500 mb-1">Diameter (cm)</label>
                            <input type="number" step="0.1" name="tank_diameter"
                                value="{{ $setting->tank_diameter ?? 40 }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5"
                                placeholder="Garis tengah lingkaran">
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-center  md:justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                            </path>
                        </svg>
                        <span class="text-sm">
                            Simpan & Mulai Tanam Baru
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <div
            class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-8 mb-8 text-white flex flex-col md:flex-row justify-between items-center gap-6 mt-4">
            <div>
                <h3 class="text-xl font-bold mb-2">🌱 Selesai Masa Tanam?</h3>
                <p class="text-blue-100 max-w-xl text-sm">
                    Jika Anda melakukan panen atau mengganti tanaman, tekan tombol panen & simpan rapor. Sistem akan
                    <span class="font-bold text-white">menganalisis data</span>, menghitung skor kualitas
                    nutrisi, dan menyimpannya ke halaman Riwayat sebagai rapor.
                </p>
            </div>
            <form id="finishForm" action="{{ route('planting.finish') }}" method="POST">
                @csrf
                <button type="button" id="finishBtn"
                    class="bg-white text-blue-700 hover:bg-gray-100 px-8 py-4 rounded-xl font-bold shadow-md transition-all transform hover:scale-105 flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm">
                        Panen & Simpan Rapor
                    </span>
                </button>
            </form>
        </div>

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

        // --- KONFIGURASI CHART ---
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#1f2937',
                    bodyColor: '#1f2937',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    grid: {
                        borderDash: [2, 4],
                        color: '#f3f4f6'
                    },
                    beginAtZero: false
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        display: false
                    }
                } // Hide X labels biar bersih
            },
            elements: {
                point: {
                    radius: 0,
                    hoverRadius: 6
                }, // Titik cuma muncul pas hover
                line: {
                    borderWidth: 3
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            },
            animation: {
                duration: 0
            }
        };

        // 1. Chart Nutrisi
        const ctxPPM = document.getElementById('chartPPM').getContext('2d');
        const chartPPM = new Chart(ctxPPM, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'PPM',
                    data: [],
                    borderColor: '#10b981', // Emerald 500
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

        // 2. Chart Suhu
        const ctxSuhu = document.getElementById('chartSuhu').getContext('2d');
        const chartSuhu = new Chart(ctxSuhu, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Suhu',
                    data: [],
                    borderColor: '#3b82f6', // Blue 500
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

        // --- REALTIME UPDATE LOGIC ---
        function updateCharts() {
            fetch('/sensor-data')
                .then(response => response.json())
                .then(data => {
                    // Update Chart Data
                    if (data.labels && data.labels.length > 0) {
                        chartPPM.data.labels = data.labels;
                        chartPPM.data.datasets[0].data = data.ppm;
                        chartPPM.update('none');

                        chartSuhu.data.labels = data.labels;
                        chartSuhu.data.datasets[0].data = data.temp;
                        chartSuhu.update('none');
                    }

                    // Update KA Alert Box
                    const box = document.getElementById('kaAlertBox');
                    const msg = document.getElementById('kaMessage');
                    const title = document.getElementById('kaTitle');
                    const iconBox = document.getElementById('kaIconBox');
                    const icon = document.getElementById('kaIcon');

                    msg.innerText = data.ka_message;

                    // Reset Classes
                    box.className =
                        "p-6 rounded-2xl shadow-sm mb-8 flex items-start gap-4 transition-all duration-500 border";
                    iconBox.className = "flex-shrink-0 p-3 rounded-xl";

                    if (data.ka_status === 'WARNING' || data.ka_status === 'ERROR') {
                        // KUNING/ORANGE
                        box.classList.add('bg-yellow-50', 'border-yellow-200');
                        iconBox.classList.add('bg-yellow-100', 'text-yellow-600');
                        title.innerText = "Tindakan Diperlukan";
                        title.className = "text-md font-bold text-yellow-600 mb-1";
                        msg.className = "text-sm text-yellow-600 font-medium leading-relaxed";
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';

                    } else if (data.ka_status === 'OVER') {
                        // MERAH
                        box.classList.add('bg-red-50', 'border-red-600');
                        iconBox.classList.add('bg-red-200', 'text-red-600');
                        title.innerText = "Nutrisi Berlebih";
                        title.className = "text-md font-bold text-red-800 mb-1";
                        msg.className = "text-sm text-red-700 font-medium leading-relaxed";
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';

                    } else {
                        // HIJAU (AMAN)
                        box.classList.add('bg-white', 'border-green-600'); // White card with green border
                        iconBox.classList.add('bg-green-200', 'text-green-600');
                        title.innerText = "Kondisi Optimal";
                        title.className = "text-md font-bold text-green-600 mb-1";
                        msg.className = "text-sm text-green-600 font-medium leading-relaxed";
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                    }

                    // --- BARU: UPDATE STATUS SYSTEM (ONLINE/OFFLINE) ---
                    const badge = document.getElementById('sysStatusBadge');
                    const dot = document.getElementById('sysStatusDot');
                    const text = document.getElementById('sysStatusText');

                    if (data.is_online) {
                        // KONDISI: ONLINE (HIJAU)
                        text.innerText = "System Online";

                        // Style Badge Hijau
                        badge.classList.remove('bg-gray-100', 'text-gray-800', 'bg-red-100', 'text-red-800');
                        badge.classList.add('bg-green-100', 'text-green-800');

                        // Style Dot (Berkedip Hijau)
                        dot.classList.remove('bg-gray-500', 'bg-red-500');
                        dot.classList.add('bg-green-500', 'animate-pulse');
                    } else {
                        // KONDISI: OFFLINE (MERAH/ABU)
                        text.innerText = "System Offline";

                        // Style Badge Merah
                        badge.classList.remove('bg-green-100', 'text-green-800', 'bg-gray-100', 'text-gray-800');
                        badge.classList.add('bg-red-100', 'text-red-800');

                        // Style Dot (Merah Diam - Tidak Berkedip)
                        dot.classList.remove('bg-green-500', 'animate-pulse', 'bg-gray-500');
                        dot.classList.add('bg-red-500');
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    // Jika fetch error (misal server mati), set ke Offline juga
                    document.getElementById('sysStatusText').innerText = "Server Error";
                    document.getElementById('sysStatusBadge').className =
                        "inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800";
                    document.getElementById('sysStatusDot').className = "w-2 h-2 bg-red-500 rounded-full mr-2";
                });
        }

        // Jalankan Update
        updateCharts();
        setInterval(updateCharts, 2000);

        // sweetAlert
        document.getElementById('finishBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Akhiri Sesi Tanam?',
                text: 'Data statistik akan diarsipkan ke Riwayat dan tidak dapat diubah.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb', // blue-600
                cancelButtonColor: '#6b7280', // gray-500
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
                buttonsStyling: false // WAJIB agar Tailwind aktif
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('finishForm').submit();
                }
            });
        });
    </script>
</body>

</html>
