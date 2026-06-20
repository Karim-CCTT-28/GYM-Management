<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        th {
            background: #000;
            color: white;
            padding: 12px;
            text-align: center;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        tr:hover {
            background: #f0f0f0;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-size: 14px;
        }

        .btn-show {
            background: #3498db;
        }

        .btn-show:hover {
            background: #2980b9;
        }

        button a {
            text-decoration: none;
            color: inherit;
        }

        button {
            margin: 5px;
        }

        tr {
            cursor: pointer;
            transition: 0.2s;
        }
    </style>
</head>


<button><a href="/management">Back</a></button>
<table>
    <tr>
        <th>User</th>
        <th>Net Total</th>
        <th>Created At</th>

    </tr>

    @foreach($reports as $r)
        <tr data-id="{{ $r['id'] }}">
            <td>{{ $r['user_name'] }}</td>
            <td>{{ $r['net_total'] }}</td>
            <td>{{ $r['created_at'] }}</td>
        </tr>
    @endforeach
</table>



<script>
    document.querySelectorAll("tr[data-id]").forEach(row => {

        row.addEventListener("click", function () {

            let id = this.dataset.id;

            window.location.href = `/admin-report/${id}`;

        });

    });
</script>