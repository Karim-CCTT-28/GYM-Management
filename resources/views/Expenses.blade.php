@extends("layouts.navigation")


@section("Expenses")

    <style>
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

        .new-expenses {
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

        <tbody>
            <tr>
                <td>Fix the window</td>
                <td>Karim Hamza</td>
                <td>100</td>
            </tr>
     

        </tbody>

    </table>



    <div class="new-expenses">
        <input type="text" placeholder="Clause">
        <input type="text" placeholder="Recipient">
        <input type="text" placeholder="Amount" oninput="this.value=this.value.replace(/[^0-9]/g,'')">


        <div class="buttons">
            <button type="button" id="cancel-add">cancel</button>
            <button type="submit" id="save">save</button>
        </div>
    </div>


    <button class="new">+</button>



    <script>
        let add = document.getElementsByClassName("new")[0];
        let addDialog = document.getElementsByClassName("new-expenses")[0]
        let cancel = document.getElementById("cancel-add");

        add.addEventListener("click", function () {
            addDialog.style = "display:flex";
        })

        cancel.addEventListener("click", function () {
            addDialog.style = "display:none";

            let inputs = document.querySelectorAll(".new-expenses input");

            for (i = 0; i < inputs.length; i++) {
                inputs[i].value = "";
            }
        })
    </script>
@endsection