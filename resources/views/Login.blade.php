<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management - Login</title>

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
            background-color: #f5f5f5;
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            width: 400px;
            padding: 30px;
            background-color: white;
            border-radius: 5px;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .input-box {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        input {
            padding: 12px;
            border: none;
            border-bottom: 1px solid #ccc;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-bottom: 1px solid black;
        }

        button {
            margin-top: 10px;
            padding: 12px;
            border: none;
            background-color: black;
            color: white;
            cursor: pointer;
            border-radius: 3px;
            transition: 0.2s;
        }

        button:hover {
            opacity: 0.9;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: gray;
            font-size: 14px;
        }

        .face {
            display: none;
        }

        .container {

            /* display: none; */
        }
    </style>
</head>

<body>

    <div class="container">

        <h1>Gym Management</h1>

        <form action="/login" method="POST" onsubmit="event.preventDefault(); checkLoginData();">

            <div class="input-box">
                <label>User</label>
                <select id="userSelect">
                    <option value="" selected disabled>User</option>

                </select>
            </div>

            <div class="input-box">
                <label>Password</label>
                <input id="password" type="password" name="password" placeholder="Enter your password">
            </div>



            <button type="submit">Login</button>

        </form>

        <div class="footer">
            Welcome Back
        </div>

    </div>



    <!-- ========================Check Face======================== -->

    <div class="face">

        <div class="camera-box">

            <video id="video" autoplay playsinline width="300"></video>

            <canvas id="canvas" style="display:none;"></canvas>

        </div>
        <button type="button" onclick="checkFace()">Login</button>
    </div>
</body>






<script>

    async function checkLoginData() {

        let user = document.getElementById("userSelect").value;
        let password = document.getElementById("password").value;

        if (!user || !password) {
            alert("Select user and enter password");
            return;
        }

        let response = await fetch("/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                user,
                password
            })
        });

        let data = await response.json();

        if (data.success) {

            startCamera();

        } else {
            alert("Wrong username or password");
        }

        // console.log(data.message);
        
    }
    async function loadUsers() {

        let response = await fetch("/users");
        let users = await response.json();

        let select = document.getElementById("userSelect");


        users.forEach(user => {

            let option = document.createElement("option");

            option.value = user.user_name;
            option.textContent = user.user_name;

            select.appendChild(option);
        });
    }

    loadUsers();

    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");

    // Start the camera and show live video
    async function startCamera() {

        const stream = await navigator.mediaDevices.getUserMedia({
            video: true
        });

        video.srcObject = stream;

        document.getElementsByClassName("container")[0].style = "display:none;"
        document.getElementsByClassName("face")[0].style = "display:block;"
    }


    async function checkFace() {

        let user = document.getElementById("userSelect").value;




        //  auto photo
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        let ctx = canvas.getContext("2d");

        // Draw the video at the top left of the canvas
        ctx.drawImage(video, 0, 0);

        canvas.toBlob(async function (blob) {

            let formData = new FormData();

            formData.append("image", blob, "face.jpg");
            formData.append("user", user);
            formData.append("_token", "{{ csrf_token() }}");
            let faceResponse = await fetch("/check-face", {
                method: "POST",
                body: formData
            });

            let faceData = await faceResponse.json();

            if (faceData.matched) {
                alert("Login Success");
            } else {
                alert("Face Not Match");
            }

        }, "image/jpeg");

    }



</script>

</html>