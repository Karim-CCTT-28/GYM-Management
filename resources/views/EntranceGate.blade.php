@extends("layouts.navigation")

@section("EntranceGate")


    <style>
        .warring {
            background-color: rgba(255, 0, 0, 0.3);

        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;

        }


        .EntranceGate {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;

            overflow-y: auto;
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

        .table-container {
            overflow-y: auto;
            min-height: 0;
            width: 100%;
        }

        #loading {
            display: none;
        }
    </style>
    <p id="loading">Loading...</p>
    <div class="face" id="face">

        <div class="camera-box">

            <video id="video" autoplay playsinline width="300"></video>

            <canvas id="canvas"></canvas>
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
                    <th>Time</th>
                    <th>ID</th>
                </tr>
            </thead>
            <tbody id="tbody">

            </tbody>
        </table>
    </div>











    <input type="hidden" name="image" id="imageInput">



    <!-- ================================================================== -->
    <script>



        // ===============================================================================


        let isProcessing = false;
        let faceDetected = false;
        let detectorInterval = null;
        const video = document.getElementById("video");
        const canvas = document.getElementById("canvas");
        let login = document.getElementById('login');
        let tbody = document.getElementById('tbody');

        let faceblcok = document.getElementById('face');
        let loading = document.getElementById("loading");

        // if (faceDetected) {
        //     login.disabled = false;
        // }
        // else {
        //     login.disabled = true;

        // }


        async function init() {

            // if (typeof faceapi !== 'undefined' && faceapi.tf) {
            //             await faceapi.tf.setBackend('cpu');
            //         }
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


                console.log('working!');
                
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
            getCheckInToday()
        });



        async function compareFaces() {


            faceblcok.style = "display:none";
            loading.style = "display:block";

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
                    // result = 
                    // {
                    //     "success": true,
                    //     "subscriber": {
                    //         "id": 4,
                    //         "name": "Karim Hamza"
                    //     },
                    //     "allow": true
                    // }                    


                    

                    if (!result.ok) {
                        alert(result.message)

                    }




                } catch (error) {
                    console.error(error);
                }

            }, "image/jpeg");
        }




        login.addEventListener("click", function () {
            compareFaces();
            window.location.reload()
        })


        async function getCheckInToday() {




//response = 
            // {
            //     "subscribersToday": [
            //         {
            //             "id": 2,
            //             "name": "Karim Hamza",
            //             "isAllow": 0,
            //             "time": "07:05"
            //         }
            //     ]
            // }

            try {

                let res = await fetch('/subscribers-today', { method: 'GET' })
                data = await res.json();



              

                subscribers = data.subscribersToday;
                // console.log(subscribers);
                subscribers.forEach(sub => {

                    renderUser(sub)

                });
            } catch (e) {
                console.log(e.message);

            }


        }


        function renderUser(sub) {


            const imageUrl = `/storage/Subscribers/${sub.id}.jpg`;
            const warring = sub.isAllow ? '' : 'warring';
            tbody.innerHTML += `
                                                                <tr class="${warring}">
                                                                            <td><img src = "${imageUrl}" class = "avatar"/></td>
                                                                            <td>${sub.name}</td>
                                                                            <td>${sub.time}</td>
                                                                            <td>${sub.id}</td>
                                                                            </tr>
                                                                            `
        }


    </script>
@endsection