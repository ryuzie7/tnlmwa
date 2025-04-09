<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UiTM Inventory System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9fafb;
        }
        .square-button {
            width: 200px;
            height: 200px;
            font-size: 20px;
            background-color: #6366f1;
            color: white;
            border-radius: 12px;
            margin: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .square-button:hover {
            background-color: #4f46e5;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col">
        <nav class="bg-white shadow p-4 flex justify-between">
            <div class="text-xl font-bold">UiTM Inventory</div>
            @auth
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
            @endauth
        </nav>
        <main class="flex-grow">
            @yield('content')
        </main>
    </div>
</body>
</html>
