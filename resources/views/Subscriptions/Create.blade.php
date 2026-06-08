<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Subscription</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .subscriber {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .subscriber img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid black;
        }

        h2 {
            color: black;
            margin-top: 0;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .btn ,button {
             display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: black;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
            border: none;
        }

        .btn:hover {
            background: black;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">

        {{-- Subscriber Info --}}
        <div class="card">
            <h2>Subscriber</h2>

            <div class="subscriber">
                <img src="{{ $subscriber->image }}" alt="img">

                <div>
                    <p><b>Name:</b> {{ $subscriber->name }}</p>
                    <p><b>Phone:</b> {{ $subscriber->phone }}</p>
                    <p><b>ID:</b> {{ $subscriber->id }}</p>
                </div>
            </div>

            <a href="/subscribers/{{  $subscriber->id  }}" class="btn">Back</a>
        </div>

        {{-- Create Subscription --}}
        <div class="card">
            <h2>Create Subscription</h2>

            <form method="POST" action="/subscriptions">
                @csrf

                {{-- hidden subscriber_id --}}
                <input type="hidden" name="subscriber_id" value="{{ $subscriber->id }}">

                <label>Start Date</label>
                <input type="date" name="start_date">

                <label>End Date</label>
                <input type="date" name="end_date">

                <button type="submit">Create Subscription</button>
            </form>
        </div>

    </div>

</body>

</html>