@extends('layouts.guest')

@section('title', 'Atur Kata Sandi Baru')

@section('content')
<x-card padding="p-6 sm:p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-zinc-900">Atur Kata Sandi Baru</h1>
        <p class="text-sm text-zinc-500 mt-1">Buatlah kata sandi yang kuat dan mudah diingat.</p>
    </div>

    <x-flash />

    <form action="{{ route('password.reset.submit') }}" method="POST" class="space-y-4">
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

        <x-button type="submit" variant="primary" size="lg" block>Atur Kata Sandi</x-button>
    </form>

    <p class="text-center text-sm text-zinc-600 mt-6">
        <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Kembali ke halaman login</a>
    </p>
</x-card>

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
                
                // Get rules from PasswordPolicyService
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
