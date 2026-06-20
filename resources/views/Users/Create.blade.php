<head>
    <script src="{{ asset('js/face-api.min.js') }}"></script>
</head>

<style>
    .new-user {

        height: 400px;
        width: 600px;
        position: absolute;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        padding: 30px;
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        background-color: white;
        z-index: 1;
    }

    .face-container {
        position: relative;
        display: flex;
        /* background-color: #ccc; */
        border-radius: 5px;
        height: 100%;
        justify-content: center;
        height: 200px;
        width: 150px;
        border: 2px solid black;
    }




    #save,
    #cancel-add {
        width: 100px;
        height: 30px;
        border: none;
        border-radius: 5px;
    }

    #save,
    #captureBtn {
        background-color: green;
        color: white;
    }

    #save:hover {
        background-color: #0D530E;
    }

    .buttons {
        margin: auto;
        gap: 5px;
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

    #captureBtn {
        cursor: pointer;
        /* display: none; */
        border: none;
        border-radius: 5px;

    }

    #captureBtn:disabled {
        background: red;
        cursor: not-allowed;
    }

    #flex {
        display: flex;
        justify-content: center;
    }

    /* =================== */

    .face-container {
        position: relative;
        width: 200px;
        height: 200px;
    }

    #users-face {
        display: none;
        width: 200px;
        height: 200px;
    }

    #video,
    #canvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
    }

    #video {
        object-fit: cover;
    }

    #face_image {
        display: none;
    }

    .upload-btn {
        margin: 0;
        display: inline-block;
        font-size: 1.5rem;
        background: inherit;
        cursor: pointer;
        font-weight: bold;
    }
</style>



<div id="flex">

    <div class="new-user">
        <input type="text" name="name" id="name" placeholder="Name" required>
        <input type="password" name="password" placeholder="password" id="password" required>
        <input type="file" name="face_image" id="face_image" required>


        <div class="face-container">
            <img src="" alt="user" id="users-face">
            <video id="video" autoplay muted></video>
            <canvas id="canvas"></canvas>
        </div>

        <button type="button" id="captureBtn" disabled>إلتقط</button>
        <span>OR</span>
        <input type="file" id="uploadFace" accept="image/*" hidden>

        <label for="uploadFace" class="upload-btn">
            +
        </label>

        <div class="buttons">
            <button type="button" id="cancel-add">cancel</button>
            <button type="submit" id="save">save</button>
        </div>
    </div>

</div>


<script>
    let cancel = document.getElementById("cancel-add");
    cancel.addEventListener("click", function () {
        window.location.href = "/employees"
    })




    //=====================================================



    let faceDetected = false;
    let detectorInterval = null;
    const captureBtn = document.getElementById("captureBtn");
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");

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

        const captureCanvas = document.createElement("canvas");

        captureCanvas.width = video.videoWidth;
        captureCanvas.height = video.videoHeight;

        const ctx = captureCanvas.getContext("2d");

        ctx.drawImage(video, 0, 0, captureCanvas.width, captureCanvas.height);


        captureCanvas.toBlob((blob) => {

            if (!blob) return;

            const file = new File([blob], "face.jpg", {
                type: "image/jpeg"
            });

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);

            const input = document.getElementById("face_image");
            input.files = dataTransfer.files;

            // preview
            const imgUrl = URL.createObjectURL(blob);
            document.getElementById("users-face").src = imgUrl;
            document.getElementById("users-face").style.display = "block";

        }, "image/jpeg", 0.9);


        if (detectorInterval) {
            clearInterval(detectorInterval);
            detectorInterval = null;
        }

        const stream = video.srcObject;
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            video.srcObject = null;
        }
    });

    init().then(() => {
        startCamera();
    });




    //====================================

    const form = document.querySelector(".new-user");

    form.addEventListener("submit", function (e) {

        const faceInput = document.getElementById("face_image").value;

        if (!faceInput) {
            e.preventDefault(); // إيقاف الإرسال

            alert("يجب التقاط صورة أولاً");

            return false;
        }
    });




    //===================================================
    const uploadFace = document.getElementById("uploadFace");

    uploadFace.addEventListener("change", async function () {
        console.log("change event");

        const file = this.files[0];

        if (!file) return;

        const img = await faceapi.bufferToImage(file);

        const detection = await faceapi.detectSingleFace(
            img,
            new faceapi.TinyFaceDetectorOptions()
        );

        if (!detection) {

            alert("لا يوجد وجه في الصورة");

            this.value = "";

            return;
        }


        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);

        document.getElementById("face_image").files = dataTransfer.files;





        video.style.display = "none";
        canvas.style.display = "none";

        const preview = document.getElementById("users-face");
        preview.src = URL.createObjectURL(file);
        preview.style.display = "block";
        video.srcObject
            .getTracks()
            .forEach(track => track.stop());

    });


    video.srcObject = null;










    async function getVector(img) {



        // res = 
        // {
        //     "success": true,
        //     "vector": [

        //         0.13196337223052979,
        //         -0.03568825125694275,
        //         0.2123171091079712
        //     ]
        // }




        let form = new FormData();
        form.append('image', img);
        form.append('functionID', '4');
        let res = await fetch('http://127.0.0.1:5001/check-face', {
            method: 'POST',
            body: form
        }
        );

        data = await res.json();

        return data.vector;

    }




    async function submit(e) {

        e.preventDefault();

        const imageInput = document.getElementById("face_image");

        if (!imageInput.files.length) {
            alert("يجب اختيار صورة");
            return;
        }

        try {

            const vector = await getVector(
                imageInput.files[0]
            );

            const formData = new FormData();

            formData.append(
                "user_name",
                document.querySelector(
                    '#name'
                ).value
            );

            formData.append(
                "password",
                document.querySelector(
                    '#password'
                ).value
            );

      

            formData.append(
                "vector",
                JSON.stringify(vector)
            );

            const response = await fetch(
                "/users",
                {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: formData
                }
            );

            const result = await response.json();

            window.location.href = '/employees';
            alert(result.message);

        } catch (error) {

            console.error(error);
            alert("حدث خطأ");

        }
    }


    document
        .getElementById("save")
        .addEventListener("click", submit);
</script>