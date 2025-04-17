@extends('layout.app')

@section('content')
    <div class="px-10 flex flex-wrap gap-10">

        {{-- Display subfolders --}}
        @if (is_array($subfolders) && count($subfolders) > 0)
            @foreach ($subfolders as $subfolder)
                <a href="{{ route('folder.view', ['folder' => $folder . '/' . basename($subfolder)]) }}"
                    class="relative flex flex-col bg-white p-4 rounded-lg shadow items-center group">
                    <img class="w-20" src="{{ asset('images/folder.png') }}" />

                    <!-- Truncated name visible by default -->
                    <p class="font-bold text-center">{{ \Illuminate\Support\Str::limit(basename($subfolder), 8, '...') }}</p>

                    <!-- Full name displayed on hover as a separate tag -->
                    <p
                        class="absolute bg-gray-950 text-white p-2 rounded-md bottom-0 translate-y-[120%] opacity-0 group-hover:opacity-100">
                        {{ basename($subfolder) }}
                    </p>
                </a>
            @endforeach
        @endif
    </div>
@endsection
