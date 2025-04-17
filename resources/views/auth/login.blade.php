<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-800 flex justify-center items-center h-[100svh]">
    <form
        class="flex flex-col gap-4 bg-orange-200 p-6 rounded-md w-1/4 max-sm:w-[95%] max-lg:w-1/2 max-xl:w-2/5 relative"
        action="{{ route('login.attempt') }}" method="POST">
        @csrf
        <div class="absolute flex items-center top-0 gap-2 -translate-y-[125%]">
            <img class="rounded-full w-16" src="{{ asset('images/moonvale.jpg') }}" />
            <h4 class="text-2xl text-white font-bold">MoonVale File Management System</h4>
        </div>
        <div class="flex flex-col gap-1">
            <label class="font-bold">Email</label>
            <input class="bg-white px-4 py-1 rounded-md border border-gray-400" placeholder="Enter Email" type="email"
                name="email">
            @error('email')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex flex-col gap-1">
            <label class="font-bold">Password</label>
            <input class="bg-white px-4 py-1 rounded-md border border-gray-400" placeholder="Enter Password"
                type="password" name="password">
            @error('password')
                <p class="text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <button
            class="bg-amber-950 text-white py-2 rounded-md mt-4 cursor-pointer focus:bg-amber-800 hover:bg-amber-800">Login</button>
    </form>
</body>

</html>
