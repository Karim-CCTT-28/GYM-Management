<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees</title>


    <style>
        .flex {
            display: flex;
            justify-content: end;
            gap: 20px;
        }

        .emps {
            width: 200px;
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15);
        }

        .delete,
        .update {
            cursor: pointer;
        }

        #add {
            position: fixed;
            bottom: 20px;
            right: 20px;
            margin: 20px;
            font-size: 1.5rem;
            border-radius: 50%;
            width: 50px;
            height: 50px;
        }

        #add a {
            text-decoration: none;
            color: inherit;
        }

                .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: black;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn:hover {
            background: white;
            color: black;
        }
    </style>
</head>

<body>

<a href="/management" class="btn">Back</a>
    <button id="add"><a href="users/create">+</a></button>
    <div class="flex">

        @foreach ($emps as $e)
            <div class='emps' data-id="{{ $e->id }}">

                {{$e->user_name}}
                <div class="buttons">
                    <img src="{{ asset("/images/delete.svg") }}" alt="delete" class="delete">
                    <img src="{{ asset("/images/update.svg") }}" alt="update" class="update">
                </div>
            </div>
        @endforeach
    </div>






    <script>
        document.querySelectorAll('.delete').forEach(d => {

            d.addEventListener("click", async function () {

                if (!confirm('هل متأكد انك تريد الحذف؟')) {
                    return;
                }

                let id = this.closest('.emps').dataset.id;

                let response = await fetch(`/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                response = await response.json();

                alert(response.message);
                window.location.reload()

            })
        });

        // =====================================

        document.querySelectorAll('.update').forEach(u => {

            u.addEventListener("click", function () {

                let id = this.closest('.emps').dataset.id;

                window.location.href = `/users/${id}/edit`;

            });

        });
    </script>
</body>

</html>