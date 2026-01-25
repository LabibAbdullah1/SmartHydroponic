<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icon-iot.png') }}">
    <title>Riwayat Tanam - Smart Hidroponik</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    @php
        $user = auth()->user();
        $isAdmin = $user && $user->role === 'admin';
    @endphp

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                {{-- LOGO & JUDUL --}}
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

                {{-- NAVBAR DESKTOP (MENU) --}}
                <div class="hidden md:block bg-white">
                    <div class="max-w-7xl mx-auto px-6 py-3 flex gap-4 items-center h-full">
                        {{-- Link Dashboard --}}
                        <a href="{{ route('dashboard') }}"
                            class="px-5 py-2 rounded-md text-sm font-semibold transition
                {{ request()->routeIs('dashboard')
                    ? 'bg-green-600 text-white shadow'
                    : 'bg-gray-100 text-gray-600 hover:bg-green-100 hover:text-green-700' }}">
                            Dashboard
                        </a>

                        {{-- Link Riwayat --}}
                        <a href="{{ route('history.index') }}"
                            class="px-5 py-2 rounded-md text-sm font-semibold transition
                {{ request()->routeIs('history.*')
                    ? 'bg-green-600 text-white shadow'
                    : 'bg-gray-100 text-gray-600 hover:bg-green-100 hover:text-green-700' }}">
                            Riwayat Tanam
                        </a>
                    </div>
                </div>

                {{-- BAGIAN KANAN (LOGOUT, STATUS SYSTEM, JAM) --}}
                <div class="flex items-center gap-4">

                    {{-- Tombol Logout (Hanya Tampil Jika Login) --}}
                    @auth
                        <form action="{{ route('logout') }}" method="POST" class="hidden md:block">
                            @csrf
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-bold transition">
                                Logout
                            </button>
                        </form>
                    @endauth

                    {{-- Badge Status System (Diberi ID agar bisa berubah warna via JS) --}}
                    <span
                        class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 transition-colors duration-300">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                        <span>Database Connected</span>
                    </span>

                    {{-- Jam Digital --}}
                    <div class="hidden lg:flex flex-col items-end mr-2">
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
                    <span>Home</span>
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
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 flex flex-col items-center text-xs">
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

        <div id="kaAlertBox"
            class="bg-white border border-gray-100 p-6 rounded-2xl shadow-sm mb-8 flex items-start gap-4 transition-all duration-500">
            <div class="flex-shrink-0 p-3 bg-gray-50 rounded-xl" id="kaIconBox">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-6 mb-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-md font-bold text-green-800 mb-1">Riwayat Tanam</h3>
                <p class="text-sm text-green-600 font-medium leading-relaxed">Rekapitulasi analisis selama periode tanam
                </p>
            </div>
        </div>

        @if ($histories->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada riwayat</h3>
                <p class="mt-1 text-sm text-gray-500">Data akan muncul setelah Anda melakukan "Panen" di Dashboard.</p>
                <div class="mt-6">
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
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
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Tanaman</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Periode</th>
                                <th scope="col"
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Durasi</th>
                                <th scope="col"
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Avg PPM</th>
                                <th scope="col"
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Suhu Ekstrim</th>
                                <th scope="col"
                                    class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Skor Nutrisi</th>

                                {{-- KOLOM HAPUS (HANYA ADMIN) --}}
                                @if ($isAdmin)
                                    <th scope="col"
                                        class="px-6 py-4 text-center text-xs font-bold text-red-600 uppercase tracking-wider">
                                        Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($histories as $history)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                                🌱</div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">
                                                    {{ $history->plant_name }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $history->finished_at->format('d M Y, H:i') }} (Panen)</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $history->started_at->format('d M') }} -
                                            {{ $history->finished_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $totalMinutes = $history->started_at->diffInMinutes($history->finished_at);
                                            $days = intdiv($totalMinutes, 1440);
                                            $hours = intdiv($totalMinutes % 1440, 60);
                                            $minutes = $totalMinutes % 60;
                                        @endphp
                                        <span
                                            class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $days }} Hari {{ $hours }} Jam {{ $minutes }}
                                            Menit
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600 font-mono">
                                        {{ $history->avg_ppm }} PPM
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        <span class="text-red-500 font-bold">{{ $history->max_temp }}°</span> /
                                        <span class="text-blue-500 font-bold">{{ $history->min_temp }}°</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $score = $history->ppm_accuracy_score;
                                            $colorClass =
                                                $score >= 80
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($score >= 50
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : 'bg-red-100 text-red-800');
                                        @endphp
                                        <span
                                            class="px-3 py-1 inline-flex text-sm font-bold rounded-lg {{ $colorClass }}">
                                            {{ $score }}%
                                        </span>
                                    </td>

                                    {{-- TOMBOL HAPUS (HANYA ADMIN) --}}
                                    @if ($isAdmin)
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            {{-- Berikan ID unik pada form berdasarkan ID history --}}
                                            <form id="delete-form-{{ $history->id }}"
                                                action="{{ route('history.destroy', $history->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')

                                                {{-- Ubah type jadi 'button' dan tambahkan onclick --}}
                                                <button type="button" onclick="confirmDelete('{{ $history->id }}')"
                                                    class="text-red-500 hover:text-red-700 font-bold text-sm hover:underline transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <footer class="mt-12 text-center text-sm text-gray-400 pb-12 md:pb-2">
            &copy; {{ date('Y') }} Smart Hidroponik System. Developed with Laravel & IoT Tech.
        </footer>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        setInterval(() => {
            const now = new Date();
            const clockElement = document.getElementById('clock');
            if (clockElement) {
                clockElement.innerText = now.toLocaleTimeString();
            }
        }, 1000);

        // --- FUNGSI HAPUS DENGAN SWEETALERT ---
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Riwayat?',
                text: "Data yang dihapus tidak dapat dikembalikan lagi.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                buttonsStyling: false, // Matikan style bawaan
                customClass: {
                    popup: 'rounded-2xl p-6 bg-white shadow-xl',
                    title: 'text-lg font-bold text-gray-800',
                    htmlContainer: 'text-sm text-gray-500',
                    // Tombol Konfirmasi Merah (Bahaya)
                    confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-5 rounded-lg text-sm ml-2 transition-all shadow-md',
                    // Tombol Batal Abu-abu
                    cancelButton: 'bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-2 px-5 rounded-lg text-sm transition-all'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>

</body>

</html>
