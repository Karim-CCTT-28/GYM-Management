<!DOCTYPE html>
<html lang="en">

<head>
    <script src="{{ asset('js/face-api.min.js') }}"></script>
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

        button:active {
            background-color: white;
            color: black;
        }

        .camera-box {
            position: relative;
            width: 300px;
            height: 225px;
        }

        .camera-box,
        #video,
        #canvas {

            border-radius: 5px;
        }

        #video,
        #canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 300px;
            height: 225px;
        }

        #captureBtn {
            background-color: green;
        }

        #captureBtn:disabled {
            background: red;
            cursor: not-allowed;
        }

        #login {
            background-color: black;
        }

        #loading {
            display: none;
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



            <button type="submit" id="login">Login</button>

        </form>

        <div class="footer">
            Welcome Back
        </div>

    </div>



    <!-- ========================Check Face======================== -->

    <div class="face">

        <div class="camera-box">

            <video id="video" autoplay playsinline width="300"></video>

            <canvas id="canvas"></canvas>
            <p id="loading">Loading...</p>
        </div>
        <button id="captureBtn" disabled>التقط</button>
    </div>
</body>






<script>
    let faceDetected = false;
    let detectorInterval = null;
    const status = document.getElementById("status");
    const captureBtn = document.getElementById("captureBtn");
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");

    async function init() {
// using cpu instad of gpu
if (typeof faceapi !== 'undefined' && faceapi.tf) {
            await faceapi.tf.setBackend('cpu');
        }
        await faceapi.nets.tinyFaceDetector.loadFromUri("/models");

    }

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

            await init();
            startCamera();

        } else {
            alert(data.message);
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


    // Start the camera and show live video
    async function startCamera() {

        const stream = await navigator.mediaDevices.getUserMedia({
            video: true
        });

        video.srcObject = stream;

        document.getElementsByClassName("container")[0].style = "display:none;"
        document.getElementsByClassName("face")[0].style = "display:block;"

        video.addEventListener("play", () => {
            checkFace();


            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
        });
    }

    function checkFace() {
        if (detectorInterval) {
            clearInterval(detectorInterval);
        }
        detectorInterval = setInterval(async () => {

            const detection = await faceapi.detectSingleFace(
                video,
                new faceapi.TinyFaceDetectorOptions()
            );
            const ctx = canvas.getContext("2d");

            ctx.clearRect(
                0,
                0,
                canvas.width,
                canvas.height
            );
            if (detection) {

                faceDetected = true;
                captureBtn.disabled = false;



                const box = detection.box;

                ctx.strokeStyle = "red";
                ctx.lineWidth = 3;

                ctx.strokeRect(
                    box.x,
                    box.y,
                    box.width,
                    box.height
                );

            } else {

                faceDetected = false;
                captureBtn.disabled = true;
            }

        }, 200);
    }


    captureBtn.addEventListener("click", async () => {

        if (!faceDetected) return;


        captureBtn.style = "display:none;";

        video.style = "display:none;"
        canvas.style = "display:none;"
        flag = document.getElementById("loading");
        flag.style = "display:block;"




        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        let ctx = canvas.getContext("2d");
        ctx.drawImage(video, 0, 0);

        const blob = await new Promise(resolve =>
            canvas.toBlob(resolve, "image/jpeg")
        );

        let formData = new FormData();

        formData.append("image", blob, "face.jpg");
        formData.append("_token", "{{ csrf_token() }}");

        const faceResponse = await fetch("/user-face", {
            method: "POST",
            body: formData
        });

        try {

            const res = await faceResponse.json();



            if (res.success) {
                window.location.href = '/entranceGate'
                clearInterval(detectorInterval);
                video.srcObject
                    .getTracks()
                    .forEach(track => track.stop());
            } else {
                alert(res.message);
            }
        } catch (error) {
            flag.textContent = error.message;
        }

    });


</script>

</html>