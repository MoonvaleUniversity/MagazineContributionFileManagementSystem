<div class="flex bg-gray-950 py-5 px-10 max-md:px-6 max-sm:px-4 text-white justify-between items-center">
    <div class="flex items-center gap-4">
        <img class="w-14 rounded-full" src="{{ asset('images/moonvale.jpg') }}">
        <div class="flex flex-col">
            <h5 class="text-lg font-bold">Moonvale University</h5>
            <p class="text-[0.75rem] text-orange-200">File Management</p>
        </div>
    </div>
    <div class="flex gap-16 me-8">
        <a class="font-bold" href="{{ route('folder.view') }}">Files</a>
    </div>
</div>
