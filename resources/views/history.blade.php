<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icon-iot.png') }}">
    <title>Riwayat Tanam - Smart Hidroponik</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
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
                        <h1 class="text-lg font-bold text-gray-900 leading-tight">Smart Hidroponik</h1>
                        <p class="text-xs text-gray-500 font-medium">Powered by Labib.Dev</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden md:flex ml-10 space-x-4">
                        <a href="{{ route('dashboard') }}"
                           class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm transition">
                           Dashboard
                        </a>

                        <a href="{{ route('history.index') }}"
                           class="text-gray-900 font-bold px-3 py-2 rounded-md text-sm">
                           Riwayat Tanam
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden md:flex flex-col items-end mr-2">
                        <span class="text-sm font-semibold text-gray-700">{{ now()->format('d M Y') }}</span>
                        <span class="text-xs text-gray-400" id="clock">00:00:00</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">📚 Arsip Laporan Panen</h2>
            <p class="text-gray-500">Rekapitulasi performa sistem dan kualitas nutrisi (KA) dari masa tanam sebelumnya.</p>
        </div>

        @if($histories->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada riwayat</h3>
                <p class="mt-1 text-sm text-gray-500">Data akan muncul setelah Anda melakukan "Panen" di Dashboard.</p>
                <div class="mt-6">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanaman</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Durasi</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Avg PPM</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Suhu Ekstrim</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Skor Nutrisi (KA)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($histories as $history)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                            🌱
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $history->plant_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $history->finished_at->format('d M Y, H:i') }} (Panen)</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $history->started_at->format('d M') }} - {{ $history->finished_at->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $history->started_at->diffInDays($history->finished_at) }} Hari
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600 font-mono">
                                    {{ $history->avg_ppm }} PPM
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    <span class="text-red-500 font-bold">{{ $history->max_temp }}°</span> /
                                    <span class="text-blue-500 font-bold">{{ $history->min_temp }}°</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $score = $history->ppm_accuracy_score;
                                        $colorClass = 'bg-red-100 text-red-800';
                                        if($score >= 80) $colorClass = 'bg-green-100 text-green-800';
                                        elseif($score >= 50) $colorClass = 'bg-yellow-100 text-yellow-800';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-sm font-bold rounded-lg {{ $colorClass }}">
                                        {{ $score }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </main>

    <script>
        setInterval(() => {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            const clockElement = document.getElementById('clock');
            if(clockElement) {
                clockElement.innerText = timeString;
            }
        }, 1000);
    </script>

</body>
</html>
