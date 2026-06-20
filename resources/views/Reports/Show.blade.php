<head>
    <script src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>

</head>

<style>
    body {
        font-family: Arial;
        background: #f5f6fa;
        padding: 20px;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }

    h2 {
        margin-bottom: 10px;
    }

    .row {
        margin: 5px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    th {
        background: #000;
        color: white;
    }

    .btn {
        padding: 8px 12px;
        background: #000;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        margin: 10px;
        cursor: pointer;
    }

    a {
        color: white;
        text-decoration: none;

    }
</style>
<button class="btn"><a href="/reports">Back</a></button>
<button class="btn btn-primary export">Export as PDF</button>

<div id="report-content">


    <div class="card">
        <div>👤 User: {{ $report->user->user_name }}</div>
        <div>📅 Date: {{ $report->created_date }}</div>
        <div>Water Balance: {{ number_format($report->water_balance, 2) }}</div>
    </div>

    <div class="card">
        <div>Total Subscription Revenue: {{ $subscriptionTotal }}</div>
        <div>Total Expenses: {{ $expenseTotal }}</div>
        <div><b>Final Balance: {{ $finalBalance }}</b></div>
    </div>

    <div class="card">
        <h3>Subscription Records Total : {{ $report->subscriptions->count() }}</h3>

        <table border="1" cellpadding="8" cellspacing="0" width="100%">
            <tr>
                <th>Member Name</th>
                <th>Subscription Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Price</th>
            </tr>

            @foreach($report->subscriptions as $s)
                <tr>
                    <td>{{ $s->subscriber->name ?? '-' }}</td>
                    <td>
                        {{ $s->subscriptionType->duration ?? '' }}
                        {{ $s->subscriptionType->duration_unit ?? '' }}
                    </td>
                    <td>{{ $s->start_date }}</td>
                    <td>{{ $s->end_date }}</td>
                    <td>{{ $s->subscriptionType->price ?? 0 }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="card">
        <h3>Expense Records Total : {{ $report->expenses->count() }}</h3>

        <table border="1" cellpadding="8" cellspacing="0" width="100%">
            <tr>
                <th>Description</th>
                <th>Recipient</th>
                <th>Amount</th>
            </tr>

            @foreach($report->expenses as $e)
                <tr>
                    <td>{{ $e->clause }}</td>
                    <td>{{ $e->recipient }}</td>
                    <td>{{ $e->amount }}</td>
                </tr>
            @endforeach
        </table>
    </div>

</div>

<script>
    document.querySelector('.export').addEventListener('click', function () {
        console.log("LMMN");

        const element = document.getElementById('report-content');
        html2pdf()
            .set({
                margin: 10,
                filename: 'report.pdf',
                image: { type: 'jpeg', quality: 1 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            })
            .from(element)
            .save();

    })
</script>