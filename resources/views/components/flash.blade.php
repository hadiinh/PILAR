@if(session('success'))
    <x-alert variant="success" class="mb-4">{{ session('success') }}</x-alert>
@endif

@if(session('status'))
    <x-alert variant="info" class="mb-4">{{ session('status') }}</x-alert>
@endif

@if(session('error'))
    <x-alert variant="danger" class="mb-4">{{ session('error') }}</x-alert>
@endif

@if($errors->any())
    <x-alert variant="danger" title="Periksa kembali isian Anda" class="mb-4">
        <ul class="list-disc pl-5 space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-alert>
@endif
