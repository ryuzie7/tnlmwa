<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teaching & Learning Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 600px;
            margin-top: 20px;
        }
        .box {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: black;
            font-size: 20px;
            font-weight: bold;
        }
        .box:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome to Teaching & Learning Inventory</h1>
    
    <div class="grid">
        <a href="{{ route('assets.index') }}" class="box">Assets</a>
        <a href="{{ route('custodians.index') }}" class="box">Custodians</a>
        <a href="{{ route('usagehistory.index') }}" class="box">Usage History</a>
    </div>

    <br>
    <a href="{{ route('logout') }}">Logout</a>
</div>

</body>
</html>
