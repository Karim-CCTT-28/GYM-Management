<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .box {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-top: 5px solid #000;
            max-width: 400px;
            width: 100%;
        }

        .icon {
            font-size: 50px;
            color: #000;
            margin-bottom: 10px;
        }

        h1 {
            color: #000;
            margin-bottom: 10px;
        }

        p {
            color: #555;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #000;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #218838;
        }
    </style>
</head>

<body>

    <div class="box">
        <div class="icon">✔</div>
        <h1>Success!</h1>
        <p>Operation completed successfully.</p>

        <a href="{{ $path }}" class="btn">Go Back</a>
    </div>

</body>
</html>