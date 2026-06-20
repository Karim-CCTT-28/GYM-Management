<head>
    <style>
        div {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        form {
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15);
            display: flex;
            flex-direction: column;
            padding: 30px;
            gap: 5px;
        }

        button a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<div>

    <form action="/users/{{ $user->id }}" method="POST">
        @csrf
        @method('PUT')

        <input type="text" name="user_name" value="{{ $user->user_name }}" placeholder='Name'>
        <input type="password" name="password" placeholder="New password">



        <div class="buttons">


            <button type="button"><a href="/employees">cancel</a></button>
            <button type="submit">
                Save
            </button>
        </div>
    </form>
</div>