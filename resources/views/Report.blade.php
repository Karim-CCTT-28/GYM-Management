@extends("layouts.navigation")

@section("Report")
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f6fa;
        padding: 20px;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 15px;
    }

    h2 {
        margin-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th, td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }

    th {
        background: #000;
        color: white;
    }

    .action-btn {
        padding: 8px 12px;
        background: #000;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        margin: 10px 0;
        border: none;
        cursor: pointer;
    }
    
    
    </style>

<button class="action-btn export">Export as PDF</button>
<button id="water" class="action-btn">Set Water Balance</button>

<div id="report-content">
    <h2>Session Report Details</h2>

    <div class="card">
        <div>User: {{ session('user_name') }}</div>
        <div>Date: <span id="report-date">-</span></div>
        <div>Water Balance: <span id="water-balance">0.00</span></div>
    </div>

    <div class="card">
        <div class="income">Total Subscription Revenue: 0</div>
        <div class="expense">Total Expenses: 0</div>
        <div><b><span class="balance">Final Balance: 0</span></b></div>
    </div>

    <div class="card">
        <h3 class="section-title">Subscription Records Total : <span>0</span></h3>

        <table border="1" cellpadding="8" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Member Name</th>
                    <th>Subscription Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="card">
        <h3 class="section-title">Expense Records Total : <span>0</span></h3>

        <table border="1" cellpadding="8" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Recipient</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>


<script>
    const waterBtn = document.getElementById("water");
    const waterBalance = document.getElementById("water-balance");

    waterBtn.addEventListener("click", async function () {
        let value;
        do {
            value = prompt("Enter Water Balance:");
        } while (
            value !== null &&
            (isNaN(value) || value.trim() === "")
        );

        if (value !== null) {
            waterBalance.textContent = Number(value).toFixed(2);

            let form = new FormData();
            form.append("water_balance", value);

            let res = await fetch("/set-water-balance", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: form
            });

            let data = await res.json();
            console.log(data);
            
            loadReport();
        }
    });

    // =================================================
    loadReport();

    async function loadReport() {
        const userName = "{{ session('user_name') }}";

        const res = await fetch("/session-report", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                user_name: userName
            })
        });

        const data = await res.json();

        if (!res.ok) {
            alert(data.message);
            return;
        }

        renderReport(data.report);
    }

    function renderReport(report) {
        document.getElementById("report-date").textContent = report.created_date;
        document.getElementById("water-balance").textContent = Number(report.water_balance || 0).toFixed(2);

        let totalSubscriptions = report.subscriptions.reduce((sum, sub) => {
            return sum + Number(sub.subscription_type?.price || 0);
        }, 0);

        let totalExpenses = report.expenses.reduce((sum, exp) => {
            return sum + Number(exp.amount || 0);
        }, 0);

        let finalBalance = Number(report.water_balance || 0) + totalSubscriptions - totalExpenses;

        document.querySelector(".income").innerHTML = `Total Subscription Revenue: ${totalSubscriptions}`;
        document.querySelector(".expense").innerHTML = `Total Expenses: ${totalExpenses}`;
        document.querySelector(".balance").innerHTML = `Final Balance: ${finalBalance}`;

        const titleSpans = document.querySelectorAll(".section-title span");
        
        titleSpans[0].textContent = report.subscriptions.length;
        let subTable = document.querySelectorAll("tbody")[0];
        subTable.innerHTML = "";

        report.subscriptions.forEach(sub => {
            let duration = `${sub.subscription_type?.duration ?? ""} ${sub.subscription_type?.duration_unit ?? ""}`;
            subTable.innerHTML += `
                <tr>
                    <td>${sub.subscriber?.name ?? "-"}</td>
                    <td>${duration.trim() || "-"}</td>
                    <td>${sub.start_date}</td>
                    <td>${sub.end_date}</td>
                    <td>${sub.subscription_type?.price ?? 0}</td>
                </tr>
            `;
        });

        titleSpans[1].textContent = report.expenses.length;
        let expTable = document.querySelectorAll("tbody")[1];
        expTable.innerHTML = "";

        report.expenses.forEach(exp => {
            expTable.innerHTML += `
                <tr>
                    <td>${exp.clause ?? "-"}</td>
                    <td>${exp.recipient ?? "-"}</td>
                    <td>${exp.amount ?? 0}</td>
                </tr>
            `;
        });
    }

    document.querySelector('.export').addEventListener('click', function () {
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
    });
</script>
@endsection