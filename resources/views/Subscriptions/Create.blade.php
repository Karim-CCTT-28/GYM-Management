<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Subscription</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .subscriber {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .subscriber img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid black;
        }

        h2 {
            color: black;
            margin-top: 0;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .btn,
        button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: black;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
            border: none;
        }

        .btn:hover {
            background: white;
            color: black;
        }
    </style>
</head>

<body>

    <div class="container">

        {{-- Subscriber Info --}}
        <div class="card">
            <h2>Subscriber</h2>

            <div class="subscriber">
                <img src="{{ asset('storage/Subscribers/' . $subscriber->id . '.jpg') }}" alt="img">

                <div>
                    <p><b>Name:</b> {{ $subscriber->name }}</p>
                    <p><b>Phone:</b> {{ $subscriber->phone }}</p>
                    <p><b>ID:</b> {{ $subscriber->id }}</p>
                </div>
            </div>

            <a href="/subscribers/{{  $subscriber->id  }}" class="btn">Back</a>
        </div>

        {{-- Create Subscription --}}
        <div class="card">
            <h2>Create Subscription</h2>

            <form id="subscriptionForm">
                @csrf

                {{-- hidden subscriber_id --}}
                <input type="hidden" name="subscriber_id" value="{{ $subscriber->id }}">
                <input type="hidden" name="user" value="{{ session("user_name") }}">

                <label>Start Date</label>
                <input type="date" name="start_date">


                <label>Subscription Type</label>
                <select name="subscription_type_id" id="typeSelect">
                    <option value="">Loading...</option>
                </select>


                <button type="submit" class = 'btn'>Create Subscription</button>
            </form>
        </div>

    </div>




    <script>
        async function loadTypes() {
            try {
                const res = await fetch("/subscription-types");
                const types = await res.json();

                const select = document.getElementById("typeSelect");
                select.innerHTML = '<option value="">Select type</option>';

                types.forEach(type => {
                    select.innerHTML += `
                <option value="${type.id}">
                   Price ${type.price} - Duration Unit ${type.duration_unit} - Duration ${type.duration}
                </option>
            `;
                });

            } catch (error) {
                console.error("Error loading types:", error);
            }
        }

        loadTypes();


        // ==================================================================================================

        document.getElementById('subscriptionForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            const response = await fetch('/subscriptions', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.message);
                return;
            }

            window.location.href = '/subscribers/'+ {{ $subscriber->id }};
        });
    </script>
</body>

</html>