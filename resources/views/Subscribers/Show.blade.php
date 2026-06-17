<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscriber Details</title>

    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            position: relative;

        }

        h1 {
            margin-top: 0;
            color: black;
        }

        .info {
            margin-top: 15px;
        }

        .info p {
            margin: 10px 0;
            font-size: 16px;
        }

        .label {
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        th {
            background: black;
            color: white;
            padding: 12px;
            text-align: center;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .empty {
            text-align: center;
            color: #777;
            padding: 20px;
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

        #subscriber {
            position: absolute;
            top: 25px;
            right: 25px;

            width: 200px;
            height: 200px;

            object-fit: cover;

            border-radius: 12px;

            border: 3px solid black;
        }
    </style>

</head>

<body>

    <div class="container">

        <!-- Subscriber Info -->
        <div class="card">

            <h1>Subscriber Details</h1>

            <div class="info">

                <p>
                    <span class="label">ID:</span>
                    {{ $s->id }}
                </p>

                <p>
                    <span class="label">Name:</span>
                    {{ $s->name }}
                </p>

                <p>
                    <span class="label">Phone:</span>
                    {{ $s->phone }}
                </p>

                <p>
                    <span class="label">Created At:</span>
                    {{ $s->created_at }}
                </p>

                <img src="{{ asset('storage/Subscribers/' . $s->id . '.jpg') }}" alt="subscriber" id="subscriber">
            </div>

            <a href="/subscribers" class="btn">
                Back
            </a>

            <a href="/subscriptions/create?subscriber_id={{ $s->id }}" class="btn">
                Add Subscription
            </a>
            <!-- Subscriptions -->
        </div>

        <table>

            <thead>

                <tr>
                    <!-- <th>ID</th> -->
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Created At</th>
                    <th>Created By</th>
                    <th>Status</th>
                </tr>

            </thead>

            <tbody>

                @forelse($s->subscriptions as $sub)

                    <tr>

                        <!-- <td>{{ $sub->id }}</td> -->

                        <td>{{ $sub->start_date }}</td>

                        <td>{{ $sub->end_date }}</td>

                        <td>{{ $sub->created_at }}</td>

                        <td>{{ $sub->created_by }}</td>

                        <td>
                           @if(now()->startOfDay()->lte(\Carbon\Carbon::parse($sub->end_date)->startOfDay()))
                                <span style="color: green; font-weight: bold;">Active</span>
                            @else
                                <span style="color: red; font-weight: bold;">Disactive</span>
                            @endif
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4" class="empty">
                            No Subscriptions Found
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</body>

</html>