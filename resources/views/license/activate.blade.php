<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>License Activation - {{ config('app.name', 'RedShark') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
        }
        .license-bg {
            background: 
                radial-gradient(ellipse at 20% 20%, rgba(239, 68, 68, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(168, 85, 247, 0.1) 0%, transparent 70%),
                linear-gradient(180deg, #0f172a 0%, #1e1b4b 100%);
        }
        .license-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .license-input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .license-input:focus {
            border-color: rgba(239, 68, 68, 0.5);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        .shark-icon {
            animation: swim 3s ease-in-out infinite;
        }
        @keyframes swim {
            0%, 100% { transform: translateX(0) rotate(0deg); }
            25% { transform: translateX(5px) rotate(2deg); }
            75% { transform: translateX(-5px) rotate(-2deg); }
        }
        .pulse-red {
            animation: pulse-red 2s infinite;
        }
        @keyframes pulse-red {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            50% { box-shadow: 0 0 0 15px rgba(239, 68, 68, 0); }
        }
    </style>
</head>
<body class="license-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-red-500 to-red-700 shadow-lg shadow-red-500/30 mb-6 shark-icon">
                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">{{ config('app.name', 'RedShark') }}</h1>
            <p class="text-slate-400">License Activation Required</p>
        </div>

        <!-- License Card -->
        <div class="license-card rounded-2xl p-8">
            <!-- Status Messages -->
            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-500/20 border border-red-500/30 text-red-300 flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-500/20 border border-green-500/30 text-green-300 flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Current Status -->
            @if($licenseInfo['status'] !== 'not_configured')
                <div class="mb-6 p-4 rounded-xl bg-slate-800/50 border border-slate-700">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2 h-2 rounded-full {{ $licenseInfo['status'] === 'valid' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        <span class="text-sm font-medium {{ $licenseInfo['status'] === 'valid' ? 'text-green-400' : 'text-red-400' }}">
                            {{ ucfirst(str_replace('_', ' ', $licenseInfo['status'])) }}
                        </span>
                    </div>
                    @if(isset($licenseInfo['message']))
                        <p class="text-sm text-slate-400">{{ $licenseInfo['message'] }}</p>
                    @endif
                </div>
            @endif

            <!-- Activation Form -->
            <form action="{{ route('license.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="license_key" class="block text-sm font-medium text-slate-300 mb-2">
                        License Key
                    </label>
                    <input 
                        type="text" 
                        id="license_key" 
                        name="license_key" 
                        value="{{ old('license_key') }}"
                        placeholder="REDS-STD-XXXXXXXX-XXXXXXXX-XXXXXXXX"
                        class="license-input w-full px-4 py-3 rounded-xl text-white placeholder-slate-500 focus:outline-none font-mono text-sm"
                        required
                        autofocus
                    >
                    @error('license_key')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Domain Info -->
                <div class="p-4 rounded-xl bg-slate-800/30 border border-slate-700/50">
                    <div class="flex items-center gap-2 text-sm text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                        <span>This license will be bound to:</span>
                    </div>
                    <p class="mt-1 font-mono text-white">{{ $currentDomain }}</p>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-3 px-6 rounded-xl bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold transition-all duration-200 flex items-center justify-center gap-2 pulse-red"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Activate License
                </button>
            </form>

            <!-- Help Text -->
            <div class="mt-6 pt-6 border-t border-slate-700/50">
                <p class="text-sm text-slate-500 text-center">
                    Don't have a license key? 
                    <a href="mailto:support@example.com" class="text-red-400 hover:text-red-300 transition-colors">
                        Contact Support
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-slate-600 text-sm mt-8">
            &copy; {{ date('Y') }} {{ config('app.name', 'RedShark') }}. All rights reserved.
        </p>
    </div>
</body>
</html>

