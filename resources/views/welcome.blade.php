<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f8fafc">
    <title>MarginAI · Ringkasan usaha</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-authenticated="{{ auth()->check() ? 'true' : 'false' }}" class="min-h-screen bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="min-h-screen">
        <header class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a class="flex min-w-0 items-center gap-3" href="/" aria-label="MarginAI beranda">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-lg font-black text-white">M</span>
                    <span class="min-w-0">
                        <span class="block text-base font-bold tracking-tight text-slate-950 dark:text-white">MarginAI</span>
                        <span class="block truncate text-xs text-slate-500 dark:text-slate-400">Kalkulator usaha UMKM</span>
                    </span>
                </a>

                <div class="flex items-center gap-2 sm:gap-4">
                    <div class="hidden text-right sm:block">
                        <p id="business-name" class="max-w-48 truncate text-sm font-semibold text-slate-900 dark:text-white">Kedai Pagi</p>
                        <p id="business-category" class="text-xs text-slate-500 dark:text-slate-400">Kuliner</p>
                    </div>
                    <span id="mode-badge" class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800 dark:border-amber-900 dark:bg-amber-950/60 dark:text-amber-200">Contoh tampilan</span>
                    <button id="login-open" class="min-h-10 rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200" type="button">Masuk</button>
                    <button id="logout-button" class="hidden min-h-10 rounded-xl border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800" type="button">Keluar</button>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 pb-12 pt-7 sm:px-6 sm:pt-10 lg:px-8">
            <div id="app-alert" class="mb-6 hidden rounded-2xl border px-4 py-3 text-sm" role="alert"></div>

            <section class="mb-7 flex flex-col gap-5 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Ringkasan usaha</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-3xl">Pantau HPP dan margin produk</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">Lihat biaya per unit, harga rekomendasi, dan kesehatan margin dalam satu tempat.</p>
                </div>
                <div id="export-links" class="hidden flex-wrap gap-2">
                    <a class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800" href="/api/products/export/excel">Unduh Excel</a>
                    <a class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800" href="/api/products/export/pdf">Unduh PDF</a>
                </div>
            </section>

            <section id="access-panel" class="mb-7 hidden rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900 dark:bg-amber-950/40" aria-labelledby="access-title">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 id="access-title" class="font-semibold text-amber-950 dark:text-amber-100">Verifikasi diperlukan</h2>
                        <p class="mt-1 text-sm leading-6 text-amber-900 dark:text-amber-200" data-access-message></p>
                    </div>
                    <button id="verify-button" class="hidden min-h-10 shrink-0 rounded-xl bg-amber-900 px-4 text-sm font-semibold text-white transition hover:bg-amber-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-700" type="button">Kirim ulang tautan verifikasi</button>
                </div>
            </section>

            <section id="setup-panel" class="mb-7 hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-7" aria-labelledby="setup-title">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Satu langkah lagi</p>
                    <h2 id="setup-title" class="mt-1 text-xl font-bold text-slate-950 dark:text-white">Lengkapi profil usaha</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Profil ini membantu MarginAI menyesuaikan rekomendasi dengan jenis usaha Anda.</p>

                    <form id="business-form" class="mt-6 grid gap-4 sm:grid-cols-2">
                        <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200 sm:col-span-2">
                            Nama usaha
                            <input name="name" required maxlength="255" class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Contoh: Kedai Pagi">
                        </label>
                        <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                            Jenis usaha
                            <select name="category" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="kuliner">Kuliner</option>
                                <option value="konveksi">Konveksi</option>
                                <option value="kerajinan">Kerajinan</option>
                                <option value="rumahan">Produk rumahan</option>
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                            Metode harga
                            <select name="pricing_method" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="margin">Margin dari harga jual</option>
                                <option value="markup">Markup dari HPP</option>
                            </select>
                        </label>
                        <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                            <span id="pricing-target-label">Target margin (%)</span>
                            <input name="default_margin_percent" type="number" min="0" max="99.99" step="0.01" value="30.00" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            <span id="pricing-target-help" class="text-xs font-normal text-slate-500 dark:text-slate-400">Persentase laba terhadap harga jual.</span>
                        </label>
                        <div class="sm:col-span-2">
                            <button class="min-h-11 rounded-xl bg-emerald-700 px-5 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700" type="submit">Simpan profil usaha</button>
                        </div>
                    </form>
                </div>
            </section>

            <div id="dashboard-content">
                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Indikator usaha">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-sm font-medium text-slate-600 dark:text-slate-400">Produk terdaftar</h2>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300" aria-hidden="true">#</span>
                        </div>
                        <p id="kpi-product-count" class="mt-4 text-3xl font-bold tracking-tight text-slate-950 dark:text-white">4</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Produk dengan formula HPP</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-sm font-medium text-slate-600 dark:text-slate-400">Rata-rata HPP / unit</h2>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-700 dark:bg-violet-950/60 dark:text-violet-300" aria-hidden="true">↓</span>
                        </div>
                        <p id="kpi-average-hpp" data-testid="kpi-average-hpp" class="mt-4 truncate text-2xl font-bold tracking-tight text-slate-950 dark:text-white">Rp 12.450,00</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Biaya produksi per produk</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-sm font-medium text-slate-600 dark:text-slate-400">Rata-rata harga rekomendasi</h2>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300" aria-hidden="true">↑</span>
                        </div>
                        <p id="kpi-average-price" class="mt-4 truncate text-2xl font-bold tracking-tight text-slate-950 dark:text-white">Rp 18.600,00</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Berdasarkan target margin usaha</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-sm font-medium text-slate-600 dark:text-slate-400">Rata-rata harga jual</h2>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300" aria-hidden="true">Rp</span>
                        </div>
                        <p id="kpi-average-selling-price" class="mt-4 truncate text-2xl font-bold tracking-tight text-slate-950 dark:text-white">Rp 17.800,00</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Dari produk yang sudah memiliki harga jual</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-sm font-medium text-slate-600 dark:text-slate-400">Rata-rata margin jual</h2>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300" aria-hidden="true">%</span>
                        </div>
                        <p id="kpi-average-margin" class="mt-4 text-3xl font-bold tracking-tight text-slate-950 dark:text-white">29,32%</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Dihitung dari harga jual</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-sm font-medium text-slate-600 dark:text-slate-400">Rata-rata biaya bahan / batch</h2>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-50 text-orange-700 dark:bg-orange-950/60 dark:text-orange-300" aria-hidden="true">B</span>
                        </div>
                        <p id="kpi-average-material-cost" class="mt-4 truncate text-2xl font-bold tracking-tight text-slate-950 dark:text-white">Rp 5.200,00</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Bahan baku per batch produksi</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-sm font-medium text-slate-600 dark:text-slate-400">Rata-rata tenaga kerja / batch</h2>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300" aria-hidden="true">T</span>
                        </div>
                        <p id="kpi-average-labor-cost" class="mt-4 truncate text-2xl font-bold tracking-tight text-slate-950 dark:text-white">Rp 1.800,00</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Tenaga kerja langsung per batch</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-sm font-medium text-slate-600 dark:text-slate-400">Rata-rata overhead / batch</h2>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-fuchsia-50 text-fuchsia-700 dark:bg-fuchsia-950/60 dark:text-fuchsia-300" aria-hidden="true">O</span>
                        </div>
                        <p id="kpi-average-overhead-cost" class="mt-4 truncate text-2xl font-bold tracking-tight text-slate-950 dark:text-white">Rp 1.250,00</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Overhead teralokasi per batch</p>
                    </article>
                </section>

                <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6" aria-labelledby="dashboard-trends-title">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Perkembangan usaha</p>
                            <h2 id="dashboard-trends-title" class="mt-1 text-lg font-bold text-slate-950 dark:text-white">Tren HPP, harga, dan laba</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Rata-rata nilai per simulasi selama 12 bulan terakhir.</p>
                        </div>
                        <span class="text-xs text-slate-500 dark:text-slate-400">HPP dan harga per unit · laba per skenario</span>
                    </div>
                    <div id="dashboard-trends" class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3" aria-live="polite"></div>
                </section>

                <section id="raw-material-section" class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6" aria-labelledby="raw-material-title">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Master data</p>
                            <h2 id="raw-material-title" class="mt-1 text-lg font-bold text-slate-950 dark:text-white">Bahan baku</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola harga satuan dan persentase susut bahan.</p>
                        </div>
                        <button id="raw-material-add" class="min-h-10 rounded-xl bg-emerald-700 px-4 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700" type="button">Tambah bahan</button>
                    </div>
                    <div id="raw-material-list" class="mt-5 overflow-x-auto" aria-live="polite"></div>
                </section>

                <section id="labor-cost-section" class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6" aria-labelledby="labor-cost-title">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Master data</p>
                            <h2 id="labor-cost-title" class="mt-1 text-lg font-bold text-slate-950 dark:text-white">Tenaga kerja</h2>
                            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">Atur tarif langsung. Saat mengisi formula produk, kuantitas berarti jumlah jam, hari, unit, atau batch kerja untuk membuat satu batch produk.</p>
                        </div>
                        <button id="labor-cost-add" class="min-h-10 rounded-xl bg-emerald-700 px-4 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700" type="button">Tambah tenaga kerja</button>
                    </div>
                    <div id="labor-cost-list" class="mt-5 overflow-x-auto" aria-live="polite"></div>
                </section>

                <section id="overhead-cost-section" class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6" aria-labelledby="overhead-cost-title">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Master data</p>
                            <h2 id="overhead-cost-title" class="mt-1 text-lg font-bold text-slate-950 dark:text-white">Biaya overhead</h2>
                            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">Kelola biaya pendukung produksi seperti listrik, gas, air, sewa, kemasan, dan penyusutan alat.</p>
                        </div>
                        <button id="overhead-cost-add" class="min-h-10 rounded-xl bg-emerald-700 px-4 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700" type="button">Tambah biaya overhead</button>
                    </div>
                    <div class="mt-5 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm leading-6 text-sky-950 dark:border-sky-900 dark:bg-sky-950/40 dark:text-sky-100">
                        <h3 class="font-semibold">Cara alokasi biaya ke HPP</h3>
                        <ul class="mt-2 grid gap-2 sm:grid-cols-2">
                            <li><span class="font-semibold">Tetap:</span> masukkan total biaya per bulan. Biaya dibagi rata per batch berdasarkan jumlah produksi bulanan dari produk yang memakai biaya ini.</li>
                            <li><span class="font-semibold">Variabel:</span> masukkan biaya untuk satu batch. Biaya ini dibebankan ke setiap batch produk yang memakai biaya tersebut.</li>
                        </ul>
                        <p class="mt-2 text-sky-900 dark:text-sky-200">Pilih biaya overhead pada formula produk agar masuk ke HPP. Bagian biaya per unit mengikuti jumlah hasil produk dalam satu batch.</p>
                    </div>
                    <div id="overhead-cost-list" class="mt-5 overflow-x-auto" aria-live="polite"></div>
                </section>

                <section class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.8fr)_minmax(18rem,1fr)]">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Produk dan margin</h2>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Margin dihitung dari harga jual, atau harga rekomendasi jika belum diatur.</p>
                            </div>
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                <label class="grid gap-1.5 text-xs font-medium text-slate-600 dark:text-slate-400">
                                    Cari produk
                                    <input id="product-search" type="search" autocomplete="off" placeholder="Nama atau SKU" class="min-h-10 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white sm:w-56">
                                </label>
                                <button id="product-add" class="hidden min-h-10 rounded-xl bg-emerald-700 px-4 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700" type="button">Tambah produk</button>
                            </div>
                        </div>
                        <div id="product-list" class="mt-5 grid gap-3 md:grid-cols-2" aria-live="polite"></div>
                    </div>

                    <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6" aria-labelledby="margin-title">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300" aria-hidden="true">✓</span>
                            <div>
                                <h2 id="margin-title" class="text-lg font-bold text-slate-950 dark:text-white">Kesehatan margin</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">Pantau produk yang perlu ditinjau sebelum menentukan harga jual.</p>
                            </div>
                        </div>
                        <div id="margin-summary" class="mt-5 grid gap-2" aria-live="polite"></div>
                        <div class="mt-5 rounded-xl bg-slate-50 p-4 text-xs leading-5 text-slate-600 dark:bg-slate-800/70 dark:text-slate-300">
                            Ambang contoh: <span class="font-semibold text-emerald-700 dark:text-emerald-400">sehat ≥30%</span>, <span class="font-semibold text-amber-700 dark:text-amber-400">perhatian 15–&lt;30%</span>, <span class="font-semibold text-rose-700 dark:text-rose-400">risiko &lt;15%</span>.
                        </div>
                    </aside>
                </section>

                <section id="price-simulation-section" class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6" aria-labelledby="price-simulation-title">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Perencanaan usaha</p>
                            <h2 id="price-simulation-title" class="mt-1 text-lg font-bold text-slate-950 dark:text-white">Simulasi harga</h2>
                            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">Uji dampak perubahan harga jual, volume penjualan, dan harga bahan terhadap omzet, biaya, laba, serta titik impas.</p>
                        </div>
                        <span class="w-fit rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-800 dark:border-sky-900 dark:bg-sky-950/50 dark:text-sky-200">Per skenario</span>
                    </div>

                    <div class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1fr)_minmax(20rem,0.9fr)]">
                        <form id="price-simulation-form" class="grid content-start gap-4 rounded-2xl border border-slate-200 p-4 dark:border-slate-800 sm:p-5">
                            <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                                Produk
                                <select id="simulation-product" name="product_id" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white"></select>
                            </label>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                                    Harga jual simulasi (Rp)
                                    <input name="simulated_price" type="number" min="0" step="0.01" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Contoh: 15000">
                                </label>
                                <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                                    Estimasi unit terjual
                                    <input name="estimated_units_sold" type="number" min="0" max="2147483647" step="1" value="100" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Contoh: 100">
                                    <span class="text-xs font-normal text-slate-500 dark:text-slate-400">Gunakan periode yang sama dengan biaya tetap bulanan produk.</span>
                                </label>
                            </div>

                            <div>
                                <div class="flex flex-wrap items-end justify-between gap-2">
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Perubahan harga bahan</h3>
                                        <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">Isi persentase perubahan. Nilai negatif berarti harga bahan turun.</p>
                                    </div>
                                </div>
                                <div id="simulation-material-changes" class="mt-3 grid gap-2" aria-live="polite"></div>
                            </div>

                            <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                                Harga kompetitor (opsional)
                                <textarea name="competitor_prices" rows="3" class="min-h-20 resize-y rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="14000&#10;16000"></textarea>
                                <span class="text-xs font-normal leading-5 text-slate-500 dark:text-slate-400">Masukkan satu harga per baris tanpa pemisah ribuan, maksimal 20 harga. Perbandingan memakai harga skenario ini.</span>
                            </label>

                            <p id="simulation-form-error" class="hidden rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-950/50 dark:text-rose-200" role="alert"></p>
                            <button id="simulation-submit" class="min-h-11 rounded-xl bg-emerald-700 px-5 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 disabled:cursor-not-allowed disabled:opacity-60" type="submit">Hitung simulasi</button>
                            <p id="simulation-access-note" class="hidden text-xs leading-5 text-slate-500 dark:text-slate-400">Masuk untuk menyimpan simulasi dan melihat riwayat analisis usaha Anda.</p>
                        </form>

                        <div class="grid content-start gap-4">
                            <section id="simulation-result" class="hidden rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 dark:border-emerald-900 dark:bg-emerald-950/30 sm:p-5" aria-labelledby="simulation-result-title" aria-live="polite">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-800 dark:text-emerald-300">Hasil skenario terakhir</p>
                                        <h3 id="simulation-result-title" class="mt-1 font-semibold text-emerald-950 dark:text-emerald-100"></h3>
                                    </div>
                                    <span id="simulation-result-status" class="rounded-full border px-2.5 py-1 text-xs font-semibold"></span>
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div class="rounded-xl bg-white/80 p-3 dark:bg-slate-900/70">
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Omzet</p>
                                        <p id="simulation-result-revenue" class="mt-1 font-semibold tabular-nums text-slate-900 dark:text-white"></p>
                                    </div>
                                    <div class="rounded-xl bg-white/80 p-3 dark:bg-slate-900/70">
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Total biaya</p>
                                        <p id="simulation-result-cost" class="mt-1 font-semibold tabular-nums text-slate-900 dark:text-white"></p>
                                    </div>
                                    <div class="rounded-xl bg-white/80 p-3 dark:bg-slate-900/70">
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Laba bersih</p>
                                        <p id="simulation-result-profit" class="mt-1 font-semibold tabular-nums text-slate-900 dark:text-white"></p>
                                    </div>
                                    <div class="rounded-xl bg-white/80 p-3 dark:bg-slate-900/70">
                                        <p class="text-xs text-slate-500 dark:text-slate-400">BEP</p>
                                        <p id="simulation-result-bep" class="mt-1 font-semibold tabular-nums text-slate-900 dark:text-white"></p>
                                    </div>
                                </div>
                                <p id="simulation-result-cost-detail" class="mt-3 text-xs leading-5 text-slate-600 dark:text-slate-300"></p>
                                <div id="simulation-result-recommendation"></div>
                            </section>

                            <section class="rounded-2xl border border-slate-200 p-4 dark:border-slate-800 sm:p-5" aria-labelledby="simulation-history-title">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <h3 id="simulation-history-title" class="font-semibold text-slate-900 dark:text-white">Riwayat simulasi</h3>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Status analisis harga dan hasil skenario sebelumnya.</p>
                                    </div>
                                    <span id="simulation-history-count" class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">0</span>
                                </div>
                                <p id="simulation-history-error" class="mt-3 hidden rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs leading-5 text-rose-800 dark:border-rose-900 dark:bg-rose-950/50 dark:text-rose-200" role="alert"></p>
                                <div id="simulation-history-list" class="mt-3 grid gap-2" aria-live="polite"></div>
                            </section>
                        </div>
                    </div>
                </section>

                <p class="mt-6 text-center text-xs text-slate-500 dark:text-slate-500">MarginAI membantu menghitung biaya dan membaca margin. Tinjau kembali harga jual sesuai kondisi pasar Anda.</p>
            </div>
        </main>
    </div>

    <dialog id="raw-material-dialog" class="w-[calc(100%-2rem)] max-w-lg rounded-3xl border border-slate-200 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/60 dark:border-slate-800 dark:bg-slate-900 dark:text-white">
        <div class="p-6 sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Data bahan baku</p>
                    <h2 id="raw-material-dialog-title" class="mt-1 text-xl font-bold">Tambah bahan baku</h2>
                </div>
                <button id="raw-material-dialog-close" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-emerald-600 dark:hover:bg-slate-800 dark:hover:text-white" type="button" aria-label="Tutup">×</button>
            </div>
            <form id="raw-material-form" class="mt-6 grid gap-4">
                <input name="id" type="hidden">
                <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    Nama bahan
                    <input name="name" maxlength="255" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Contoh: Tepung terigu">
                    <span data-field-error="name" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                </label>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                        Satuan
                        <select name="unit" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            <option value="">Pilih satuan</option>
                            <option value="gram">Gram</option>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="meter">Meter</option>
                            <option value="pcs">Pcs</option>
                            <option value="liter">Liter</option>
                        </select>
                        <span data-field-error="unit" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                    </label>
                    <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                        Harga satuan (Rp)
                        <input name="unit_price" type="number" min="0" step="0.0001" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="0">
                        <span data-field-error="unit_price" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                    </label>
                </div>
                <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    Susut (%)
                    <input name="wastage_percent" type="number" min="0" max="100" step="0.01" required value="0" class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    <span data-field-error="wastage_percent" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                </label>
                <p id="raw-material-form-error" class="hidden rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-950/50 dark:text-rose-200" role="alert"></p>
                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button id="raw-material-dialog-cancel" class="min-h-11 rounded-xl border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800" type="button">Batal</button>
                    <button id="raw-material-submit" class="min-h-11 rounded-xl bg-emerald-700 px-5 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 disabled:cursor-wait disabled:opacity-60" type="submit">Simpan bahan</button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="labor-cost-dialog" class="w-[calc(100%-2rem)] max-w-lg rounded-3xl border border-slate-200 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/60 dark:border-slate-800 dark:bg-slate-900 dark:text-white">
        <div class="p-6 sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Data tenaga kerja</p>
                    <h2 id="labor-cost-dialog-title" class="mt-1 text-xl font-bold">Tambah tenaga kerja</h2>
                </div>
                <button id="labor-cost-dialog-close" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-emerald-600 dark:hover:bg-slate-800 dark:hover:text-white" type="button" aria-label="Tutup">×</button>
            </div>
            <form id="labor-cost-form" class="mt-6 grid gap-4">
                <input name="id" type="hidden">
                <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    Nama pekerjaan
                    <input name="name" maxlength="255" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Contoh: Menjahit pesanan">
                    <span data-field-error="name" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                </label>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                        Tipe tarif
                        <select name="rate_type" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            <option value="per_hour">Per jam</option>
                            <option value="per_day">Per hari</option>
                            <option value="per_unit">Per unit</option>
                            <option value="per_batch">Per batch</option>
                        </select>
                        <span data-field-error="rate_type" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                    </label>
                    <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                        Tarif (Rp)
                        <input name="rate" type="number" min="0" step="0.0001" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="0">
                        <span data-field-error="rate" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                    </label>
                </div>
                <p id="labor-cost-quantity-help" class="rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-sm leading-6 text-sky-900 dark:border-sky-900 dark:bg-sky-950/40 dark:text-sky-200" aria-live="polite"></p>
                <p id="labor-cost-form-error" class="hidden rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-950/50 dark:text-rose-200" role="alert"></p>
                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button id="labor-cost-dialog-cancel" class="min-h-11 rounded-xl border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800" type="button">Batal</button>
                    <button id="labor-cost-submit" class="min-h-11 rounded-xl bg-emerald-700 px-5 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 disabled:cursor-wait disabled:opacity-60" type="submit">Simpan tenaga kerja</button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="overhead-cost-dialog" class="w-[calc(100%-2rem)] max-w-lg rounded-3xl border border-slate-200 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/60 dark:border-slate-800 dark:bg-slate-900 dark:text-white">
        <div class="p-6 sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Data biaya overhead</p>
                    <h2 id="overhead-cost-dialog-title" class="mt-1 text-xl font-bold">Tambah biaya overhead</h2>
                </div>
                <button id="overhead-cost-dialog-close" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-emerald-600 dark:hover:bg-slate-800 dark:hover:text-white" type="button" aria-label="Tutup">×</button>
            </div>
            <form id="overhead-cost-form" class="mt-6 grid gap-4">
                <input name="id" type="hidden">
                <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    Nama biaya
                    <input name="name" maxlength="255" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Contoh: Listrik produksi">
                    <span data-field-error="name" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                </label>
                <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    Jenis biaya
                    <select name="type" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        <option value="fixed">Tetap — biaya bulanan</option>
                        <option value="variable">Variabel — biaya per batch</option>
                    </select>
                    <span data-field-error="type" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                </label>
                <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    <span id="overhead-cost-amount-label">Nilai biaya per bulan (Rp)</span>
                    <input name="amount" type="number" min="0" step="0.0001" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="0">
                    <span data-field-error="amount" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                </label>
                <p id="overhead-cost-allocation-help" class="rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-sm leading-6 text-sky-900 dark:border-sky-900 dark:bg-sky-950/40 dark:text-sky-200" aria-live="polite"></p>
                <p id="overhead-cost-form-error" class="hidden rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-950/50 dark:text-rose-200" role="alert"></p>
                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button id="overhead-cost-dialog-cancel" class="min-h-11 rounded-xl border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800" type="button">Batal</button>
                    <button id="overhead-cost-submit" class="min-h-11 rounded-xl bg-emerald-700 px-5 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 disabled:cursor-wait disabled:opacity-60" type="submit">Simpan biaya overhead</button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="product-dialog" class="w-[calc(100%-2rem)] max-w-4xl rounded-3xl border border-slate-200 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/60 dark:border-slate-800 dark:bg-slate-900 dark:text-white">
        <div class="max-h-[calc(100dvh-2rem)] overflow-y-auto p-5 sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Formula produk</p>
                    <h2 id="product-dialog-title" class="mt-1 text-xl font-bold">Tambah produk</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Susun komponen untuk satu batch produksi. Biaya dan HPP dihitung otomatis.</p>
                </div>
                <button id="product-dialog-close" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-emerald-600 dark:hover:bg-slate-800 dark:hover:text-white" type="button" aria-label="Tutup">×</button>
            </div>

            <form id="product-form" class="mt-6 grid gap-5">
                <input name="id" type="hidden">
                <section class="grid gap-4 rounded-2xl border border-slate-200 p-4 dark:border-slate-800 sm:grid-cols-2" aria-label="Informasi produk">
                    <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                        Nama produk
                        <input name="name" maxlength="255" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Contoh: Roti kopi">
                        <span data-field-error="name" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                    </label>
                    <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                        SKU <span class="font-normal text-slate-500 dark:text-slate-400">Opsional</span>
                        <input name="sku" maxlength="100" class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Contoh: ROT-001">
                        <span data-field-error="sku" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                    </label>
                    <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                        Hasil per batch (unit)
                        <input name="batch_yield" type="number" min="0.01" step="0.01" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Contoh: 24">
                        <span data-field-error="batch_yield" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                    </label>
                    <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                        Batch per bulan
                        <input name="batches_per_month" type="number" min="0.01" step="0.01" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="Contoh: 20">
                        <span data-field-error="batches_per_month" class="hidden text-sm font-normal text-rose-700 dark:text-rose-300"></span>
                    </label>
                </section>

                <div class="grid gap-4 lg:grid-cols-2">
                    <section class="rounded-2xl border border-slate-200 p-4 dark:border-slate-800" aria-labelledby="product-material-heading">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 id="product-material-heading" class="font-semibold text-slate-900 dark:text-white">Bahan baku</h3>
                                <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">Pilih bahan dan isi jumlah yang dipakai untuk satu batch.</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Wajib</span>
                        </div>
                        <div id="product-material-options" class="mt-4 grid gap-2"></div>
                        <p data-field-error="materials" class="mt-2 hidden text-sm text-rose-700 dark:text-rose-300"></p>
                    </section>

                    <div class="grid content-start gap-4">
                        <section class="rounded-2xl border border-slate-200 p-4 dark:border-slate-800" aria-labelledby="product-labor-heading">
                            <h3 id="product-labor-heading" class="font-semibold text-slate-900 dark:text-white">Tenaga kerja</h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">Jumlah jam, hari, unit, atau batch kerja untuk satu batch produk.</p>
                            <div id="product-labor-options" class="mt-4 grid gap-2"></div>
                        </section>

                        <section class="rounded-2xl border border-slate-200 p-4 dark:border-slate-800" aria-labelledby="product-overhead-heading">
                            <h3 id="product-overhead-heading" class="font-semibold text-slate-900 dark:text-white">Biaya overhead</h3>
                            <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">Biaya tetap dialokasikan menurut batch per bulan.</p>
                            <div id="product-overhead-options" class="mt-4 grid gap-2"></div>
                        </section>
                    </div>
                </div>

                <section class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 dark:border-emerald-900 dark:bg-emerald-950/30" aria-labelledby="product-cost-summary-heading">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 id="product-cost-summary-heading" class="font-semibold text-emerald-950 dark:text-emerald-100">Ringkasan biaya per batch</h3>
                            <p class="mt-1 text-xs text-emerald-900/80 dark:text-emerald-200/80">Estimasi mengikuti bahan terpilih dan alokasi biaya bulanan.</p>
                        </div>
                        <p id="product-hpp-unit" class="text-right text-lg font-bold tabular-nums text-emerald-800 dark:text-emerald-200">HPP / unit —</p>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                        <div class="rounded-xl bg-white/80 p-3 dark:bg-slate-900/70">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Bahan baku</p>
                            <p id="product-material-total" class="mt-1 font-semibold tabular-nums text-slate-900 dark:text-white">—</p>
                        </div>
                        <div class="rounded-xl bg-white/80 p-3 dark:bg-slate-900/70">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Tenaga kerja</p>
                            <p id="product-labor-total" class="mt-1 font-semibold tabular-nums text-slate-900 dark:text-white">—</p>
                        </div>
                        <div class="rounded-xl bg-white/80 p-3 dark:bg-slate-900/70">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Overhead</p>
                            <p id="product-overhead-total" class="mt-1 font-semibold tabular-nums text-slate-900 dark:text-white">—</p>
                        </div>
                        <div class="rounded-xl bg-white/80 p-3 dark:bg-slate-900/70">
                            <p class="text-xs text-slate-500 dark:text-slate-400">Total biaya batch</p>
                            <p id="product-batch-total" class="mt-1 font-semibold tabular-nums text-slate-900 dark:text-white">—</p>
                        </div>
                    </div>
                </section>

                <p id="product-form-error" class="hidden rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-950/50 dark:text-rose-200" role="alert"></p>
                <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button id="product-dialog-cancel" class="min-h-11 rounded-xl border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800" type="button">Batal</button>
                    <button id="product-submit" class="min-h-11 rounded-xl bg-emerald-700 px-5 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 disabled:cursor-wait disabled:opacity-60" type="submit">Simpan produk</button>
                </div>
            </form>
        </div>
    </dialog>

    <dialog id="login-dialog" class="w-[calc(100%-2rem)] max-w-md rounded-3xl border border-slate-200 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/60 dark:border-slate-800 dark:bg-slate-900 dark:text-white">
        <div class="p-6 sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Selamat datang kembali</p>
                    <h2 class="mt-1 text-xl font-bold">Masuk ke MarginAI</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Masuk untuk melihat ringkasan biaya usaha Anda.</p>
                </div>
                <button id="login-close" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-emerald-600 dark:hover:bg-slate-800 dark:hover:text-white" type="button" aria-label="Tutup">×</button>
            </div>
            <form id="login-form" class="mt-6 grid gap-4">
                <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    Email
                    <input name="email" type="email" autocomplete="username" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </label>
                <label class="grid gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                    Kata sandi
                    <input name="password" type="password" autocomplete="current-password" required class="min-h-11 rounded-xl border border-slate-300 bg-white px-3 text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                    <input name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 accent-emerald-700">
                    Ingat saya
                </label>
                <p id="login-error" class="min-h-5 text-sm text-rose-700 dark:text-rose-300" role="alert"></p>
                <button id="login-submit" class="min-h-11 rounded-xl bg-emerald-700 px-4 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 disabled:cursor-wait disabled:opacity-60" type="submit">Masuk</button>
            </form>
            <p class="mt-5 text-xs leading-5 text-slate-500 dark:text-slate-400">Pastikan email Anda sudah terverifikasi untuk mengakses data usaha.</p>
        </div>
    </dialog>
</body>
</html>
