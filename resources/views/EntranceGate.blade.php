@extends("layouts.navigation")

@section("EntranceGate")


    <style>
        .warring {
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


        .camera-box {
            position: relative;
        }

        #video,
        #canvas,
        .camera-box {
            width: 300px;
            height: 120px;

        }

        #video {
            position: absolute;
            top: 0;
            left: 0;
        }

        #canvas {
            position: absolute;
            top: 0;
            left: 0;
        }

        .face {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        #loading {
            display: none;
        }
    </style>
    <div class="face">

        <div class="camera-box">

            <video id="video" autoplay playsinline width="300"></video>

            <canvas id="canvas"></canvas>
            <p id="loading">Loading...</p>
        </div>
        <button id="login" disabled>Login</button>
    </div>

    <!-- ========================================= -->
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

            </tbody>
        </table>
    </div>











    <input type="hidden" name="image" id="imageInput">



    <!-- ================================================================== -->
    <script>

        let images = document.getElementsByClassName("avatar");

        Array.from(images).forEach(img => {
            let random = Math.floor(Math.random() * 100);

            img.src = `https://randomuser.me/api/portraits/men/${random}.jpg`
        });

        // ===============================================================================


        let isProcessing = false;
        let faceDetected = false;
        let detectorInterval = null;
        const video = document.getElementById("video");
        const canvas = document.getElementById("canvas");
        let login = document.getElementById('login');


        // if (faceDetected) {
        //     login.disabled = false;
        // }
        // else {
        //     login.disabled = true;

        // }


        async function init() {

if (typeof faceapi !== 'undefined' && faceapi.tf) {
            await faceapi.tf.setBackend('cpu');
        }
            await faceapi.nets.tinyFaceDetector.loadFromUri("/models");

        }




        // Start the camera and show live video
        async function startCamera() {

            const stream = await navigator.mediaDevices.getUserMedia({
                video: true
            });

            video.srcObject = stream;



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
                    login.disabled = false;


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
                    login.disabled = true;
                }

            }, 200);
        }


        init().then(() => {
            startCamera();
        });



        async function compareFaces() {
           

            const captureCanvas = document.createElement("canvas");
            captureCanvas.width = video.videoWidth;
            captureCanvas.height = video.videoHeight;

            const captureCtx = captureCanvas.getContext("2d");

            captureCtx.drawImage(video, 0, 0);

            captureCanvas.toBlob(async (blob) => {

                const file = new File([blob], "face.jpg", {
                    type: "image/jpeg"
                });

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);

                document.getElementById("imageInput").files = dataTransfer.files;

                const formData = new FormData();
                formData.append("image", file);

                try {
                    const response = await fetch("/subscriber-face", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                        },
                        body: formData
                    });

                    const result = await response.json();
                    console.log(result);

                } catch (error) {
                    console.error(error);
                } finally {
                    isProcessing = false;
                }

            }, "image/jpeg");
        }




        login.addEventListener("click", function () {
            compareFaces();
        })
    </script>
@endsection