@extends("layouts.navigation")


@section("Expenses")

    <style>
        tr:hover {
            background-color: #ccc;
            cursor: pointer;
        }

        .Expenses {
            border-collapse: collapse;
            width: fit-content;

        }

        th,
        td {
            border-bottom: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .new {
            border-radius: 50%;
            position: fixed;
            right: 50px;
            bottom: 50px;
            border: none;
            background-color: black;
            width: 50px;
            height: 50px;
            font-size: 2rem;
            color: white;
        }

        .new:active {
            background-color: white;
            color: black;
        }

        #save,
        #cancel-add {
            width: 100px;
            height: 30px;
            border: none;
            border-radius: 5px;
        }

        #save {
            background-color: green;
            color: white;
        }

        #save:hover {
            background-color: #0D530E;
        }

        .buttons {
            margin: auto;
        }

        .new-expenses ,
        .update-expenses {
            position: absolute;
            display: none;
            flex-direction: column;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            padding: 30px;
            border-radius: 5px;
            gap: 5px;
            background-color: white;
        }
    </style>
    <table class="Expenses">

        <thead>
            <tr>
                <th>Clause</th>
                <th>Recipient</th>
                <th>Amount</th>
            </tr>
        </thead>

        <tbody id="expenses-body">

            @forelse($expenses as $expense)
                <tr data-id="{{ $expense->id }}">
                    <td>{{ $expense->clause }}</td>
                    <td>{{ $expense->recipient }}</td>
                    <td>{{ $expense->amount }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">لا توجد مصاريف اليوم</td>
                </tr>
            @endforelse

        </tbody>

    </table>



    <div class="new-expenses">
        <input type="text" placeholder="Clause">
        <input type="text" placeholder="Recipient">
        <input type="text" placeholder="Amount" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        <input type="hidden" id="user_name" value="{{ session("user_name") }}">

        <div class="buttons">
            <button type="button" id="cancel-add">cancel</button>
            <button type="submit" id="save">save</button>
        </div>
    </div>

    <!-- =========================== -->

    <div class="update-expenses">
        <input type="hidden" id="update_id">

        <input type="text" id="update_clause" placeholder="Clause">
        <input type="text" id="update_recipient" placeholder="Recipient">
        <input type="text" id="update_amount" placeholder="Amount" oninput="this.value=this.value.replace(/[^0-9]/g,'')">


        <div class="buttons">
            <button type="button" id="cancel-update">Cancel</button>
            <button type="button" id="save-update">Update</button>
        </div>
    </div>

    <button class="new">+</button>



    <script>
















        let add = document.getElementsByClassName("new")[0];
        let addDialog = document.getElementsByClassName("new-expenses")[0];
        let cancel = document.getElementById("cancel-add");
        let save = document.getElementById("save");
        let user_name = document.getElementById("user_name").value;



        add.addEventListener("click", function () {
            addDialog.style.display = "flex";
        });

        cancel.addEventListener("click", function () {
            addDialog.style.display = "none";

            document.querySelectorAll(".new-expenses input").forEach(input => {
                if (input.id !== "user_name") {
                    input.value = "";
                }
            });
        });


        async function storeExpense() {

            let inputs = document.querySelectorAll(".new-expenses input");

            let clause = inputs[0].value;
            let recipient = inputs[1].value;
            let amount = inputs[2].value;

            let response = await fetch("/expenses", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    user_name: user_name,
                    clause: clause,
                    recipient: recipient,
                    amount: amount
                })
            });

            let result = await response.json();

            if (result.status) {

                addDialog.style.display = "none";

                inputs.forEach(input => {
                    input.value = "";
                });

                loadExpenses();
            } else {
                alert(result.message);
            }
        }

        save.addEventListener("click", storeExpense);




        updateRow()
        function updateRow() {
            let rows = document.querySelectorAll("#expenses-body tr");

            rows.forEach(row => {

                row.addEventListener("click", async function () {
                    let id = this.dataset.id;

                    let response = await fetch(`/expenses/${id}`);

                    let data = await response.json();

                    let form = document.querySelector(".update-expenses");
                    form.style.display = "flex";

                    document.querySelector("#update_id").value = data.id;
                    document.querySelector("#update_clause").value = data.clause;
                    document.querySelector("#update_recipient").value = data.recipient;
                    document.querySelector("#update_amount").value = data.amount;

                })
            });
        }        
    </script>
@endsection