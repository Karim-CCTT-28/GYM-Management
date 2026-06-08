<div class="camera-box">

    <video id="video" autoplay playsinline width="300"></video>

    <canvas id="canvas" style="display:none;"></canvas>

</div>
<button type="button" onclick="login()">
    Login
</button>


<script>

const video = document.getElementById("video");
const canvas = document.getElementById("canvas");

// Start the camera and show live video
async function startCamera() {

    const stream = await navigator.mediaDevices.getUserMedia({
        video: true
    });

    video.srcObject = stream;
}

startCamera();

async function login() {

    let user = document.getElementById("user").value;
    let password = document.getElementById("password").value;

    // data check
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

    if(data.success){

        //  auto photo
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        let ctx = canvas.getContext("2d");

        // Draw the video at the top left of the canvas
        ctx.drawImage(video, 0, 0);

        canvas.toBlob(async function(blob){

            let formData = new FormData();

            formData.append("image", blob, "face.jpg");
            formData.append("user", user);

            let faceResponse = await fetch("/check-face", {
                method: "POST",
                body: formData
            });

            let faceData = await faceResponse.json();

            if(faceData.matched){
                alert("Login Success");
            }else{
                alert("Face Not Match");
            }

        }, "image/jpeg");

    }else{
        alert("Wrong Password");
    }

}

</script>