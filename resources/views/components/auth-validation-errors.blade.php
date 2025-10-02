@props(['errors'])

@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded']) }}>
        <div class="font-medium">Oops, ada error:</div>
        <ul class="mt-2 list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
