@php
    $files = collect($getState());
    $maxVisible = 2;
@endphp

<div class="flex items-center -space-x-3">
    @foreach ($files->take($maxVisible) as $file)
        @php
            $path = $file;
            $url = asset('storage/' . $path);
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'ico', 'bmp','tiff']);
            $icon = match (true) {
                in_array($ext, ['mp4', 'webm']) => '🎥',
                in_array($ext, ['mp3', 'wav', 'ogg']) => '🎵',
                $ext === 'pdf' => '📄',
                default => '📁',
            };
        @endphp

        <a href="{{ $url }}" target="_blank"
            class="w-10 h-10 rounded-full ring-2 ring-white bg-white flex items-center justify-center overflow-hidden hover:ring-primary-500 transition"
            title="{{ basename($path) }}">
            @if ($isImage)
                <img src="{{ $url }}" alt="file" class="w-full h-full object-cover rounded-full" />
            @else
                <span class="text-lg">{{ $icon }}</span>
            @endif
        </a>
    @endforeach

    @if ($files->count() > $maxVisible)
        <div
            class="w-10 h-10 rounded-full bg-gray-300 text-gray-800 text-xs font-semibold flex items-center justify-center ring-2 ring-white">
            +{{ $files->count() - $maxVisible }}
        </div>
    @endif
</div>