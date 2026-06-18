@extends("layouts.navigation")

@section("Report")

    <style>
        .summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .summary p {
            margin: 8px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .income {
            color: #198754;
        }

        .expense {
            color: #dc3545;
        }

        .balance {
            color: #0d6efd;
            font-size: 18px;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f1f3f5;
        }

        .action-btn {
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 15px;
        }

        #water {
            background: black;
            color: white;
        }

        .export {
            position: fixed;
            top: 50px;
            right: 50px;
            background: black;
            color: white;
        }
    </style>

    <button id="water" class="action-btn">
        Set Water Balance
    </button>

    <div class="summary">

        <p>
            Water Balance :
            <span id="water-balance">0</span>
        </p>

        <p class="income">Total Subscription Revenue : 0</p>
        <p class="expense">Total Expenses : 0</p>
        <p class="balance">Final Balance : 0</p>

    </div>

    <hr>

    <div class="table-container">

        <div class="section-title">
            Subscription Records Total : <span>0</span>
        </div>

        <table>
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

    <hr>

    <div class="table-container">

        <div class="section-title">
            Expense Records Total : <span>0</span>
        </div>

        <table>
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

    <button class="action-btn export">
        Export as PDF
    </button>

    <script>

        const waterBtn = document.getElementById("water");
        const waterBalance = document.getElementById("water-balance");

        waterBtn.addEventListener("click", async function () {

            let value;

            do {
                value = prompt("Enter Water Balance:");
            }
            while (
                value !== null &&
                (isNaN(value) || value.trim() === "")
            );

            if (value !== null) {

                waterBalance.textContent = value;

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
            }

        });

        // =================================================
        loadReport()

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



        
        document.getElementById("water-balance").textContent = report.water_balance;






        
            // Total subscriptions
            let totalSubscriptions = report.subscriptions.reduce((sum, sub) => {
                return sum + Number(sub.subscription_type?.price || 0);
            }, 0);

            // Total expenses
            let totalExpenses = report.expenses.reduce((sum, exp) => {
                return sum + Number(exp.amount || 0);
            }, 0);

            let finalBalance = totalSubscriptions - totalExpenses;

            // Summary
            document.querySelector(".income").innerHTML =
                `Total Subscription Revenue : ${totalSubscriptions}`;

            document.querySelector(".expense").innerHTML =
                `Total Expenses : ${totalExpenses}`;

            document.querySelector(".balance").innerHTML =
                `Final Balance : ${finalBalance}`;

            document.getElementById("water-balance").textContent =
                report.water_balance ?? 0;

            // عدد الاشتراكات
            document.querySelectorAll(".section-title span")[0].textContent =
                report.subscriptions.length;

            // جدول الاشتراكات
            let subTable = document.querySelectorAll("tbody")[0];
            subTable.innerHTML = "";

            report.subscriptions.forEach(sub => {

                let duration =
                    `${sub.subscription_type?.duration ?? ""} ${sub.subscription_type?.duration_unit ?? ""
                    }`;

                subTable.innerHTML += `
                    <tr>
                        <td>${sub.subscriber?.name ?? "-"}</td>
                        <td>${duration}</td>
                        <td>${sub.start_date}</td>
                        <td>${sub.end_date}</td>
                        <td>${sub.subscription_type?.price ?? 0}</td>
                    </tr>
                `;
            });

            document.querySelectorAll(".section-title span")[1].textContent =
                report.expenses.length;

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
    </script>

@endsection