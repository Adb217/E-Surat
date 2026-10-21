@if (session('ok'))
    <div class="mb-4 rounded-xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm font-medium text-orange-800">{{ session('ok') }}</div>
@endif
@if ($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc space-y-0.5 pl-5">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif