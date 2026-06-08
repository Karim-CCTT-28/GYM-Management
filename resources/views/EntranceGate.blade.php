@extends("layouts.navigation")

@section("EntranceGate")


    <style>
        .warring{
            background-color: rgba(255, 0, 0, 0.3);
           
        }
        .avatar {
            height: 100px;
            border-radius: 50%;

        }

        .table-container {
            width: 100%;
            max-height: 500px;
            overflow-x: auto;
            overflow-y: auto;
        }

        .EntranceGate {
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
    </style>
    <div class="table-container">

        <table class="EntranceGate">

            <thead>
                <tr>

                    <th>Picture</th>
                    <th>Name</th>
                    <th>Started Date</th>
                    <th>End Date</th>
                    <th>ID</th>
                </tr>
            </thead>
            <tbody>
                <tr>

                    <td> <img alt="Random Person" class="avatar"> </td>
                    <td>Random Person</td>
                    <td>01/01/2025</td>
                    <td>01/01/2026</td>
                    <td>2085</td>
                </tr>
                <tr class="warring">

                    <td> <img alt="Random Person" class="avatar"> </td>
                    <td>Random Person</td>
                    <td>01/01/2025</td>
                    <td>01/01/2026</td>
                    <td>2085</td>
                </tr>
                <tr>

                    <td> <img alt="Random Person" class="avatar"> </td>
                    <td>Random Person</td>
                    <td>01/01/2025</td>
                    <td>01/01/2026</td>
                    <td>2085</td>
                </tr>
                <tr>

                    <td> <img alt="Random Person" class="avatar"> </td>
                    <td>Random Person</td>
                    <td>01/01/2025</td>
                    <td>01/01/2026</td>
                    <td>2085</td>
                </tr>
                <tr>

                    <td> <img alt="Random Person" class="avatar"> </td>
                    <td>Random Person</td>
                    <td>01/01/2025</td>
                    <td>01/01/2026</td>
                    <td>2085</td>
                </tr>
                <tr>

                    <td> <img alt="Random Person" class="avatar"> </td>
                    <td>Random Person</td>
                    <td>01/01/2025</td>
                    <td>01/01/2026</td>
                    <td>2085</td>
                </tr>
                <tr>

                    <td> <img alt="Random Person" class="avatar"> </td>
                    <td>Random Person</td>
                    <td>01/01/2025</td>
                    <td>01/01/2026</td>
                    <td>2085</td>
                </tr>
                <tr>

                    <td> <img alt="Random Person" class="avatar"> </td>
                    <td>Random Person</td>
                    <td>01/01/2025</td>
                    <td>01/01/2026</td>
                    <td>2085</td>
                </tr>
            </tbody>
        </table>
    </div>
    <script>

        let images = document.getElementsByClassName("avatar");

        Array.from(images).forEach(img => {
            let random = Math.floor(Math.random() * 100);

            img.src = `https://randomuser.me/api/portraits/men/${random}.jpg`
        });
    </script>
@endsection