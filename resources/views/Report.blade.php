@extends("layouts.navigation")

@section("Report")





    <style>
        .table-container {
            width: 100%;
            max-height: 500px;
            overflow-x: auto;
            overflow-y: auto;
        }

        .Subscriptions,
        .Expenses {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th,
        td {
            border-bottom: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        #expenses {
            color: red;
        }

        #pure {
            color: green;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .table-container p {
            margin: 0;
        }

        .export {
            border-radius: 5px;
            position: fixed;
            right: 50px;
            top: 50px;
            border: none;
            background-color: black;
            width: 100px;
            height: 30px;
            color: white;
        }
    </style>


    <button id="water">Set Water Balance</button>



    <div class="info">
        <p>Water Balance : <span id="water-balance">0</span></p>
        <p>Total : 2030</p>
        <p id="expenses">Expenses : 100</p>
        <p id="pure">Pure Total : 1930</p>
    </div>

    <hr>
    <div class="table-container">
        <p>Subscriptions</p>
        <table class="Subscriptions">

            <thead>
                <tr>

                    <th>Name</th>
                    <th>Subscriptions Type</th>
                    <th>Price</th>
                    <th>ID</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Karim Hamza</td>
                    <td>3 years</td>
                    <td>2000</td>
                    <td>3089</td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="2"><strong>Total</strong></td>
                    <td colspan="2"><strong>2000</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <hr>

    <div class="table-container">
        <p>Expenses</p>

        <table class="Expenses">

            <thead>
                <tr>
                    <th>Clause</th>
                    <th>Recipient</th>
                    <th>Amount</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Fix the window</td>
                    <td>Karim Hamza</td>
                    <td>100</td>
                </tr>


            </tbody>

            <tfoot>
                <tr>
                    <td colspan="2"><strong>Total</strong></td>
                    <td colspan="2"><strong>100</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>





    <button class="export">Export as PDF</button>



    <script>
        let water = document.getElementById("water");
        let waterBalance = document.getElementById("water-balance");

        water.addEventListener("click", function () {

            let value;

            do {
                value = prompt("رصيد المياه:");
            } while (value !== null && (isNaN(value) || value.trim() === ""));

            if (value !== null) {
                waterBalance.textContent = value;
            }

        });
    </script>
@endsection