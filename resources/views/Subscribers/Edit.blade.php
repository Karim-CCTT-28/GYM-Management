
<head>
    <script src="{{ asset('js/face-api.min.js') }}"></script>
</head>

<style>
    .new-subscriber {
        height: auto;
        min-height: 450px;
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
        border-radius: 5px;
        justify-content: center;
        height: 200px;
        width: 150px;
        border: 2px solid black;
        overflow: hidden;
    }

    #save,
    #cancel-add {
        width: 100px;
        height: 30px;
        border: none;
        border-radius: 5px;
    }

    #save,
    #captureBtn,
    #startCamBtn {
        background-color: green;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        cursor: pointer;
    }

    #startCamBtn {
        background-color: #3182ce;
    }

    #save:hover {
        background-color: #0D530E;
    }

    .buttons {
        margin: auto;
        display: flex;
        gap: 5px;
    }

    #video {
        position: absolute;
        top: 0;
        left: 0;
        display: none; /* مخفي في البداية حتى يتم تشغيل الكاميرا */
    }

    #canvas {
        position: absolute;
        top: 0;
        left: 0;
    }

    #captureBtn {
        display: none; /* مخفي في البداية */
    }

    #captureBtn:disabled {
        background: red;
        cursor: not-allowed;
    }

    #flex {
        display: flex;
        justify-content: center;
    }

    .face-container {
        position: relative;
        width: 200px;
        height: 200px;
    }

    #subscribers-face {
        display: block; /* ظاهر بالبداية لعرض الصورة القديمة */
        width: 200px;
        height: 200px;
        object-fit: cover;
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
    <form class="new-subscriber" action="/subscribers/{{ $subscriber->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <h2>تعديل بيانات المشترك</h2>
        
        <input type="text" name="name" value="{{ $subscriber->name }}" placeholder="Name" required>
        <input type="text" name="phone" value="{{ $subscriber->phone }}" placeholder="Phone" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
        
        <input type="file" name="face_image" id="face_image">

        <div class="face-container">
            <img src="{{ asset('storage/Subscribers/' . $subscriber->id . '.jpg') }}" alt="subscriber" id="subscribers-face">
            <video id="video" autoplay muted></video>
            <canvas id="canvas"></canvas>
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
            <button type="button" id="startCamBtn">تشغيل الكاميرا</button>
            <button type="button" id="captureBtn" disabled>إلتقط</button>
        </div>
        
        <span>OR</span>
        <input type="file" id="uploadFace" accept="image/*" hidden>

        <label for="uploadFace" class="upload-btn">
            +
        </label>

        <div class="buttons">
            <button type="button" id="cancel-add">cancel</button>
            <button type="submit" id="save">save</button>
        </div>
    </form>
</div>

<script>
    let cancel = document.getElementById("cancel-add");
    cancel.addEventListener("click", function () {
        window.location.href = "/subscribers";
    });

    let faceDetected = false;
    let detectorInterval = null;
    const captureBtn = document.getElementById("captureBtn");
    const startCamBtn = document.getElementById("startCamBtn");
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");
    const previewImg = document.getElementById("subscribers-face");

    async function init() {
        await faceapi.nets.tinyFaceDetector.loadFromUri("/models");
    }

    startCamBtn.addEventListener("click", async function() {
        previewImg.style.display = "none";
        video.style.display = "block";
        captureBtn.style.display = "inline-block";
        this.style.display = "none"; 
        
        await startCamera();
    });

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

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            if (detection) {
                faceDetected = true;
                captureBtn.disabled = false;

                const box = detection.box;
                ctx.strokeStyle = "red";
                ctx.lineWidth = 3;
                ctx.strokeRect(box.x, box.y, box.width, box.height);
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

            const imgUrl = URL.createObjectURL(blob);
            previewImg.src = imgUrl;
            previewImg.style.display = "block";
            video.style.display = "none";
            canvas.style.display = "none";
            captureBtn.style.display = "none";
            startCamBtn.style.display = "inline-block";
            startCamBtn.innerText = "إعادة التقاط";

        }, "image/jpeg", 0.9);

        stopCamera();
    });

    function stopCamera() {
        if (detectorInterval) {
            clearInterval(detectorInterval);
            detectorInterval = null;
        }
        const stream = video.srcObject;
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            video.srcObject = null;
        }
    }

    init();

    const uploadFace = document.getElementById("uploadFace");

    uploadFace.addEventListener("change", async function () {
        const file = this.files[0];
        if (!file) return;

        const img = await faceapi.bufferToImage(file);
        const detection = await faceapi.detectSingleFace(
            img,
            new faceapi.TinyFaceDetectorOptions()
        );

        if (!detection) {
            alert("لا يوجد وجه في الصورة المرفوعة");
            this.value = "";
            return;
        }

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        document.getElementById("face_image").files = dataTransfer.files;

        stopCamera();
        video.style.display = "none";
        canvas.style.display = "none";
        captureBtn.style.display = "none";
        startCamBtn.style.display = "inline-block";
        startCamBtn.innerText = "تغيير الصورة";

        previewImg.src = URL.createObjectURL(file);
        previewImg.style.display = "block";
    });
</script>
