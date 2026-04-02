<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Error')</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #0f172a; color: #e2e8f0; }
        .wrap { min-height: 100vh; display: grid; place-items: center; padding: 20px; }
        .card { background: #111827; border: 1px solid #1f2937; border-radius: 12px; padding: 24px; max-width: 700px; width: 100%; }
        h1 { margin-top: 0; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>@yield('title', 'Error')</h1>
        <p>@yield('message', 'Something went wrong.')</p>
    </div>
</div>
</body>
</html>
