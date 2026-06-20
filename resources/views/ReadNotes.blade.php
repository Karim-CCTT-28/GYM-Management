<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 40px 20px;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            font-size: 24px;
        }

        .notes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .note-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-right: 5px solid #000;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s;
        }

        .note-card:hover {
            transform: translateY(-5px);
        }

        .note-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .user-badge {
            background-color: #e8f4fd;
            color: #3498db;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .note-date {
            color: #888;
            font-size: 12px;
        }

        .note-content {
            font-size: 15px;
            line-height: 1.6;
            color: #4a5568;
            white-space: pre-line;
            flex-grow: 1;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 8px;
            color: #777;
            grid-column: 1 / -1;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: black;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn:hover {
            background: white;
            color: black;
        }
    </style>
</head>

<body>
    <a href="/management" class="btn">Back</a>
    <div class="container">

        <div class="notes-grid">
            @forelse($notes as $note)
                @if(!$note->isDeleted)
                    <div class="note-card">
                        <div class="note-header">
                            <span class="user-badge">👤 {{ $note->user->user_name ?? 'مستخدم مجهول' }}</span>
                            <span class="note-date">
                                {{ $note->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <div class="note-content">
                            {{ $note->note }}
                        </div>
                    </div>
                @endif
            @empty
                <div class="empty-state">
                    <h3>لا توجد أي ملاحظات حالياً.</h3>
                </div>
            @endforelse
        </div>
    </div>

</body>

</html>