@extends('layout.app')

@section('content')
    <div
        class="px-10 max-md:px-6 max-sm:px-4 grid grid-cols-12 max-xl:grid-cols-10 max-lg:grid-cols-8 max-md:grid-cols-6 max-sm:grid-cols-4 gap-12 max-lg:gap-10 max-md:gap-6 max-sm:gap-4 text-sm">
        @if (empty($folder))
            <a href="{{ route('folder.view', ['folder' => 'trash']) }}"
                class="flex flex-col bg-white py-4 px-2 rounded-lg shadow items-center col-span-2 justify-between h-32 cursor-pointer">
                <img class="h-[90%]" src="{{ asset('images/trash.png') }}" />
            </a>
        @else
            <a href="{{ route('folder.view', ['folder' => dirname($folder)]) }}"
                class="flex justify-center bg-white p-4 rounded-lg shadow items-center col-span-2 h-32">
                <img class="h-[55%]" src="{{ asset('images/back.jpg') }}" />
            </a>
        @endif

        @if (isset($folders) && count($folders) > 0)
            @foreach ($folders as $folder)
                <a href="{{ route('folder.view', ['folder' => $folder]) }}"
                    class="file-link flex flex-col bg-white p-4 rounded-lg shadow items-center col-span-2 justify-between relative h-32 group"
                    data-file="{{ $folder }}" data-file-type="folder">
                    <img class="w-20" src="{{ asset('images/folder.png') }}" />
                    <p class="font-bold text-center">{{ \Illuminate\Support\Str::limit(basename($folder), 18, '...') }}
                    </p>

                    <p
                        class="absolute bg-gray-950 text-white p-2 rounded-md bottom-0 translate-y-[120%] opacity-0 group-hover:opacity-100">
                        {{ basename($folder) }}
                    </p>
                </a>
            @endforeach
        @endif

        @if (isset($subfolders) && count($subfolders) > 0)
            @foreach ($subfolders as $subfolder)
                <a href="{{ route('folder.view', ['folder' => $folder . '/' . basename($subfolder)]) }}"
                    class="file-link relative flex flex-col bg-white p-4 rounded-lg shadow items-center col-span-2 justify-between h-32 group"
                    data-file="{{ $subfolder }}" data-file-type="folder">
                    <img class="w-20" src="{{ asset('images/folder.png') }}" />

                    <p class="font-bold text-center">
                        {{ \Illuminate\Support\Str::limit($folder === 'trash' ? preg_replace('/^\d+_/', '', basename($subfolder)) : basename($subfolder), 18, '...') }}
                    </p>

                    <p
                        class="absolute bg-gray-950 text-white p-2 rounded-md bottom-0 translate-y-[120%] opacity-0 group-hover:opacity-100">
                        {{ $folder === 'trash' ? preg_replace('/^\d+_/', '', basename($subfolder)) : basename($subfolder) }}
                    </p>
                </a>
            @endforeach
        @endif

        @if (isset($filesInFolder) && count($filesInFolder) > 0)
            @foreach ($filesInFolder as $file)
                @if (\Illuminate\Support\Str::endsWith(strtolower($file), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']))
                    <a href="{{ asset('storage/' . $file) }}" target="_blank" data-file="{{ $file }}"
                        data-file-type="file"
                        class="file-link flex flex-col bg-white p-4 rounded-lg shadow items-center col-span-2 justify-between h-32 relative group">
                        <img class="w-16" src="{{ asset('images/image.jpg') }}" />
                        <p class="font-bold text-center">
                            {{ \Illuminate\Support\Str::limit($folder === 'trash' ? preg_replace('/^\d+_/', '', basename($file)) : basename($file), 18, '...') }}
                        </p>

                        <p
                            class="absolute bg-gray-950 text-white p-2 rounded-md bottom-0 translate-y-[120%] opacity-0 group-hover:opacity-100">
                            {{ $folder === 'trash' ? preg_replace('/^\d+_/', '', basename($file)) : basename($file) }}
                        </p>
                    </a>
                @elseif(\Illuminate\Support\Str::endsWith(strtolower($file), ['doc', 'docx', 'pdf', 'txt', 'odt', 'rtf', 'tex']))
                    <a href="{{ asset('storage/' . $file) }}" target="_blank" data-file="{{ $file }}"
                        data-file-type="file"
                        class="file-link flex flex-col bg-white p-4 rounded-lg shadow items-center col-span-2 justify-between h-32 relative group">
                        <img class="w-16" src="{{ asset('images/word.png') }}" />
                        <p class="font-bold text-center">
                            {{ \Illuminate\Support\Str::limit($folder === 'trash' ? preg_replace('/^\d+_/', '', basename($file)) : basename($file), 18, '...') }}
                        </p>

                        <p
                            class="absolute bg-gray-950 text-white p-2 rounded-md bottom-0 translate-y-[120%] opacity-0 group-hover:opacity-100">
                            {{ $folder === 'trash' ? preg_replace('/^\d+_/', '', basename($file)) : basename($file) }}
                        </p>
                    </a>
                @endif
            @endforeach
        @endif
    </div>

    <!-- Custom Context Menu -->
    <div id="customContextMenu" class="hidden absolute bg-white shadow-md rounded-lg p-2 border border-gray-200">
        <ul class="text-sm">
            @if (isset($folder) && str_contains($folder, 'trash'))
                <li class="p-2 hover:bg-gray-200 cursor-pointer" id="restoreFile">Restore</li>
            @endif
            <li class="p-2 hover:bg-gray-200 cursor-pointer" id="openFile">Open</li>
            <li class="p-2 hover:bg-gray-200 cursor-pointer" id="openFileInNewTab">Open in new tab</li>
            <li class="p-2 hover:bg-gray-200 cursor-pointer" id="downloadFile">Download</li>
            <li class="p-2 hover:bg-gray-200 cursor-pointer" id="deleteFile">Delete</li>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileLinks = document.querySelectorAll('.file-link');
            const contextMenu = document.getElementById('customContextMenu');
            let selectedFile = null;
            let selectedFileType = null;

            fileLinks.forEach(link => {
                link.addEventListener('contextmenu', function(event) {
                    event.preventDefault(); // Prevent default right-click menu

                    selectedFile = this.getAttribute('data-file');
                    selectedFileType = this.getAttribute('data-file-type');

                    // Position the menu at the cursor
                    contextMenu.style.left = `${event.pageX}px`;
                    contextMenu.style.top = `${event.pageY}px`;
                    contextMenu.classList.remove('hidden');
                });
            });

            // Close menu on click elsewhere
            document.addEventListener('click', function() {
                contextMenu.classList.add('hidden');
            });

            // Restore File
            @if(isset($folder) && str_contains($folder, 'trash'))
                document.getElementById('restoreFile').addEventListener('click', function() {
                    if (selectedFile) {
                        const fileName = selectedFile.split('/').pop();
                        const link = document.createElement('a');
                        link.href = `/restore-file/${selectedFileType}/${selectedFile}`;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                });
            @endif

            // Context Menu Actions
            document.getElementById('openFile').addEventListener('click', function() {
                if (selectedFile) {
                    if(selectedFileType == 'folder') {
                        window.location.href = `{{ asset('folders') }}/${selectedFile}`;
                    } else {
                        window.location.href = `/file/${selectedFile}`;
                    }
                }
            });

            // Context Menu Actions
            document.getElementById('openFileInNewTab').addEventListener('click', function() {
                if (selectedFile) {
                    window.open(`{{ asset('folders/') }}/${selectedFile}`, '_blank');
                }
            });

            document.getElementById('downloadFile').addEventListener('click', function() {
                if (selectedFile) {
                    const fileName = selectedFile.split('/').pop();
                    const link = document.createElement('a');
                    link.href = `/download/${selectedFileType}/${selectedFile}`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });

            document.getElementById('deleteFile').addEventListener('click', function() {
                if (selectedFile && confirm('Are you sure you want to delete this file?')) {
                    const fileName = selectedFile.split('/').pop();
                    const link = document.createElement('a');
                    link.href = `/delete-file/${selectedFileType}/${selectedFile}`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });
        });
    </script>
@endsection
