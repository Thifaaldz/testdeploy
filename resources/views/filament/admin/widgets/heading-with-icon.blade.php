<div class="flex items-center gap-3">
    <div class="p-2 bg-white rounded-md">
        {!! $icon !!}
    </div>
    <div>
        <h2 class="text-xl font-bold text-gray-900">{{ $heading }}</h2>
        @if(isset($subheading))
            <p class="text-sm text-gray-500">{{ $subheading }}</p>
        @endif
    </div>
</div>
