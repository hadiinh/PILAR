@extends('layouts.app')

@section('title', 'Ubah Kata Sandi')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <x-card padding="p-6 sm:p-8">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-yellow-100 text-yellow-700 mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6H9m6 0h3M9 15h6"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-zinc-900">Ubah Kata Sandi</h1>
            <p class="text-sm text-zinc-500 mt-2">Silakan buat kata sandi baru sebelum melanjutkan. Gunakan kata sandi yang kuat dan aman.</p>
        </div>

        <x-flash />

        <form action="{{ route('password.force-change.submit') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="password" class="block text-sm font-semibold text-zinc-900 mb-2">Kata Sandi Baru</label>
                <div class="relative">
                    <input 
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan kata sandi baru"
                        required
                        class="w-full px-4 py-2 border border-zinc-300 rounded-lg text-zinc-900 placeholder-zinc-500 focus:ring-2 focus:ring-brand-500 focus:border-transparent transition"
                        autocomplete="new-password"
                    />
                    <button type="button" class="toggle-password absolute right-3 top-2.5 text-zinc-500 hover:text-zinc-700" data-target="password">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <div class="mt-1 text-sm text-red-600">
                        @if (is_array($message))
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($message as $msg)
                                    <li>{{ $msg }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ $message }}
                        @endif
                    </div>
                @enderror
            </div>

            {{-- Password strength indicator --}}
            <div class="space-y-2">
                <p class="text-xs font-semibold text-zinc-700">Persyaratan kata sandi:</p>
                <ul class="space-y-1 text-xs text-zinc-600">
                    @foreach ($passwordRules as $rule)
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-300 requirement-check"></span>
                            {{ $rule }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-zinc-900 mb-2">Konfirmasi Kata Sandi</label>
                <div class="relative">
                    <input 
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ulangi kata sandi baru"
                        required
                        class="w-full px-4 py-2 border border-zinc-300 rounded-lg text-zinc-900 placeholder-zinc-500 focus:ring-2 focus:ring-brand-500 focus:border-transparent transition"
                        autocomplete="new-password"
                    />
                    <button type="button" class="toggle-password absolute right-3 top-2.5 text-zinc-500 hover:text-zinc-700" data-target="password_confirmation">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <x-button type="submit" variant="primary" size="lg" block>Ubah Kata Sandi</x-button>
        </form>

        <div class="mt-6 text-center">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm text-zinc-600 hover:text-zinc-800 font-semibold">Keluar dari akun ini</button>
            </form>
        </div>
    </x-card>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.dataset.target;
                const input = document.getElementById(target);
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
            });
        });

        // Password strength checker
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const checks = document.querySelectorAll('.requirement-check');
                
                const hasMin = password.length >= 8;
                const hasUpper = /[A-Z]/.test(password);
                const hasLower = /[a-z]/.test(password);
                const hasDigit = /[0-9]/.test(password);
                const hasSymbol = /[!@#$%^&*()_+\-=\[\]{};:'"<>,.?\/]/.test(password);
                
                const requirements = [hasMin, hasUpper, hasLower, hasDigit, hasSymbol];
                
                checks.forEach((check, idx) => {
                    if (requirements[idx]) {
                        check.classList.remove('bg-zinc-300');
                        check.classList.add('bg-green-500');
                    } else {
                        check.classList.remove('bg-green-500');
                        check.classList.add('bg-zinc-300');
                    }
                });
            });
        }
    });
</script>
@endpush
@endsection
