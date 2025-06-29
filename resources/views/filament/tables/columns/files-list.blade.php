@php
    use Illuminate\Support\Str;

    $files = collect($getState());
    $maxVisible = 2;
@endphp

<div class="flex flex-row-reverse items-center space-x-reverse -space-x-4 -space-y-4 relative">
    {{-- أولاً: لو في عدد أكبر من المسموح، نعرض +N --}}
    @if ($files->count() > $maxVisible)
        <div
            class="w-10 h-10 rounded-full bg-gray-300 text-gray-800 text-xs font-semibold flex items-center justify-center ring-2 ring-white z-10"
            style="position: static;">
            +{{ $files->count() - $maxVisible }}
        </div>
    @endif

    {{-- نعرض الصور أو الأيقونات بشكل دائري --}}
    @foreach ($files->take($maxVisible)->reverse() as $file)
        @php
            $url = asset('storage/' . $file['file_path']);
            $ext = strtolower(pathinfo($file['file_path'], PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'ico', 'bmp','tiff']);
            $icon = match (true) {
                in_array($ext, ['mp4', 'webm']) => '🎥',
                in_array($ext, ['mp3', 'wav', 'ogg']) => '🎵',
                $ext === 'pdf' => '📄',
                default => '📁',
            };
        @endphp

        @if ($isImage)
            <img src="{{ $url }}" class="w-10 h-10 rounded-full ring-2 ring-white object-cover" alt="file" style="position: static;" />
        @else
            <div title="{{ basename($file['file_path']) }}"
                class="w-10 h-10 bg-gray-200 flex items-center justify-center text-xl rounded-full ring-2 ring-white"
                style="position: static;">
                {{ $icon }}
            </div>
        @endif
    @endforeach
</div>