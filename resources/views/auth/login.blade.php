<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — EVA Monitoring Proyek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">EVA Monitoring</h1>
            <p class="text-blue-300 text-sm mt-1">Sistem Monitoring Proyek Konstruksi</p>
        </div>

        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-8 shadow-2xl">
            <h2 class="text-lg font-semibold text-white mb-6">Masuk ke Sistem</h2>

            @if($errors->any())
            <div class="bg-red-500/20 border border-red-400/30 text-red-300 text-sm px-4 py-3 rounded-xl mb-4">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300/60 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition"
                        placeholder="email@domain.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300/60 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition"
                        placeholder="••••••••">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-white/20">
                    <label for="remember" class="text-sm text-blue-200">Ingat saya</label>
                </div>
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-400 text-white font-semibold py-3 rounded-xl transition-colors shadow-lg shadow-blue-500/30 mt-2">
                    Masuk
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-white/10 text-xs text-blue-300/70 text-center space-y-1">
                <p>Admin: admin@eva.test / password</p>
                <p>PM: budi@eva.test / password</p>
            </div>
        </div>
    </div>
</body>
</html>
