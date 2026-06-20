@extends("layouts.navigation")

@section("Subscribers")


    <style>
        tr {
            cursor: pointer;
        }

        .container-table {
            height: 500px;
            overflow-y: auto;
        }

        .Subscribers-table {
            border-collapse: collapse;
            min-width: 300px;
        }

        th,
        td {
            border-bottom: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        tr:hover {
            class='btn'
        }

        #search {
            border: none;

        }

        #search:focus {
            outline: none;
        }

        .search-box {
            border: 2px solid #ccc;
            display: flex;
            align-items: center;
            border-radius: 5px;
            width: fit-content;
            margin-bottom: 20px;
        }

        .search-box:focus-within {
            border: 2px solid black;

        }

        #Subscribers {
            display: flex;
            align-items: center;
            flex-direction: column;
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

        .btn:hover {
            background-color: #e2e8f0;
            transform: scale(1.1);
        }
    </style>



    <div id="Subscribers">


        <div class="search-box">
            <input type="text" id="search" placeholder="Search By Name or Phone">
            <img src="{{ asset("icons/search.svg") }}" alt="search icon">
        </div>
        <div class="container-table">

            <table class="Subscribers-table">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>ID</th>
                        <th>-</th>
                        <th>-</th>
                        <th>-</th>
                    </tr>
                </thead>

                <tbody id="tbody">
                    @foreach ($subscribers as $s)

                        <tr data-id="{{ $s->id }}">
                            <td>{{ $s->name }}</td>
                            <td>{{$s->phone}}</td>
                            <td>{{$s->id}}</td>
                            <td>
                                <img src="{{ asset('/images/update.svg') }}" alt="update" class='btn update'>
                            </td>
                            <td>

                                <img src="{{ asset('/images/delete.svg') }}" alt="delete" class='btn delete'>
                            </td>
                            <td>

                                <img src="{{ asset('/images/show.svg') }}" alt="show" class='btn show'>
                            </td>
                        </tr>
                    @endforeach

                </tbody>


            </table>
        </div>
    </div>









    <button class="new">+</button>


    <script>
        let add = document.getElementsByClassName("new")[0];

        add.addEventListener("click", function () {
            window.location.href = "/subscribers/create";
        });



        // ================================================
        let search = document.getElementById("search");
        let tbody = document.querySelector("tbody");

        search.addEventListener("input", async function () {

            if (!this.value) {
                location.reload();
                return;
            }

            let response = await fetch(`/subscribers?search=${this.value}`);

            let subscribers = await response.json();

            tbody.innerHTML = "";

            subscribers.forEach(s => {

                tbody.innerHTML += `
                                                <tr data-id=${s.id}>
                                                    <td>${s.name}</td>
                                                    <td>${s.phone}</td>
                                                    <td>${s.id}</td>
                                                </tr>
                                            `;
            });
        });


        //======================================


        let rows = document.querySelectorAll(".show");

        for (let i = 0; i < rows.length; i++) {

            rows[i].addEventListener("click", function () {


                let id = this.closest('tr').dataset.id;

                window.location.href = "/subscribers/" + id;

            });

        }
        //========================================================================

        document.querySelectorAll(".delete").forEach(d => {

            d.addEventListener("click", async function () {
                if (!confirm("هل أنت متأكد انك تريد الحذف؟")) return;

                let id = this.closest('tr').dataset.id;
                let response = await fetch(`/subscribers/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });


                data = await response.json();

                alert(data.message)
                window.location.reload()
            })
        });


        document.querySelectorAll(".update").forEach(btn => {
            btn.onclick = function () {
                let id = this.closest('tr').dataset.id;
                window.location.href = `/subscribers/${id}/edit`;
            };
        });
    </script>
@endsection