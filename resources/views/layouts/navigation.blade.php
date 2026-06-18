<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/face-api.min.js') }}"></script>
    <title>Gym Management</title>

    <style>
        html,
        body {
            height: 100%;

        }

        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-y: hidden;
        }

        header {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 5px;
            border-bottom: 1px solid #ccc;
            width: fit-content;
        }

        header button {
            border: none;
            background-color: inherit;
            color: grey;
            user-select: none;
        }

        .active {
            color: black;
            border-bottom: 1px solid black;

        }

        /* .container {
            height: 95%;
            width: 95%;
            background-color: inherit;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            border-radius: 5px;
            padding: 5px;
        } */

        .container {
            height: 95vh;
            width: 95%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .yield-Subscribers,
        .yield-Expenses {
            display: flex;
            justify-content: center;
            

        }

        .yield-Expenses{
            position: absolute;
            display: flex;
            justify-content: center;
            width: 100%;
            margin-top: 100px;
        }
        .yield-EntranceGate {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            align-items: center;
        }



        
    </style>
</head>

<body>


    <div class="container">

        <header>
            <button id="1">Entrance Gate</button>
            <button id="2">Subscribers</button>
            <button id="3">Report</button>
            <button id="4">Expenses</button>
            <button id="5">Notes</button>
        </header>

        <div class="yield-EntranceGate">
            @yield("EntranceGate")
        </div>

        <div class="yield-Subscribers">
            @yield("Subscribers")
        </div>

        <div class="yield-Report">
            @yield("Report")
        </div>

        <div class="yield-Expenses">
            @yield("Expenses")
        </div>
    </div>


    <script>

        let tabs = document.querySelectorAll("header button");

        // current path
        let path = window.location.pathname;

        // remove active from all
        tabs.forEach(tab => {
            tab.classList.remove("active");
        });

        // set active button
        if (path === "/entranceGate") {
            document.getElementById("1").classList.add("active");
        }
        else if (path === "/subscribers") {
            document.getElementById("2").classList.add("active");
        }
        else if (path === "/report") {
            document.getElementById("3").classList.add("active");
        }
        else if (path === "/expenses") {
            document.getElementById("4").classList.add("active");
        }
        else if (path === "/notes") {
            document.getElementById("5").classList.add("active");
        }

        // navigation
        tabs.forEach(tab => {

            tab.addEventListener("click", function () {

                let id = tab.getAttribute("id");

                if (id === "1") {
                    window.location.href = "/entranceGate";
                }
                else if (id === "2") {
                    window.location.href = "/subscribers";
                }
                else if (id === "3") {
                    window.location.href = "/report";
                }
                else if (id === "4") {
                    window.location.href = "/expenses";
                }
                else if (id === "5") {
                    window.location.href = "/notes";
                }

            });

        });

    </script>
</body>

</html>