<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Check In Kelas' }}</title>
    @livewireStyles
    <style>
        :root {
            --bg: #f6f7f9;
            --panel: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --primary: #111827;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #d97706;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        a { color: inherit; }

        .topbar {
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            background: var(--panel);
            border-bottom: 1px solid var(--border);
        }

        .brand { font-weight: 700; }

        .nav {
            display: flex;
            gap: 14px;
            align-items: center;
            font-size: 14px;
        }

        .nav a {
            text-decoration: none;
            color: var(--muted);
        }

        .nav a:hover { color: var(--text); }

        .page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 28px 20px 40px;
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 46px;
            padding: 0 16px;
            border: 0;
            border-radius: 6px;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            line-height: 1;
            cursor: pointer;
        }

        .btn:disabled {
            opacity: .55;
            cursor: wait;
        }

        .btn-success { background: var(--success); }
        .btn-danger { background: var(--danger); }
        .btn-small { height: 30px; padding: 0 10px; font-size: 12px; }
        .btn-check-in { min-width: 110px; }

        .input {
            width: 100%;
            height: 46px;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 0 14px;
            font-size: 18px;
            outline: none;
        }

        .input:focus {
            border-color: #9ca3af;
            box-shadow: 0 0 0 3px rgba(156, 163, 175, .2);
        }

        .muted { color: var(--muted); }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand">Level Fit Admin</div>
        <nav class="nav">
            <a href="{{ url('/admin/check-in') }}">Check In</a>
            <a href="{{ url('/admin/class-details') }}">Class Details</a>
            <a href="{{ url('/admin') }}">Admin</a>
        </nav>
    </header>

    {{ $slot }}

    @livewireScripts
</body>
</html>
