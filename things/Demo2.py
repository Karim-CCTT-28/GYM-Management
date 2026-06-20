import numpy as np
import time
from flask import Flask, request, jsonify
from deepface import DeepFace
import matplotlib.pyplot as plt
import numpy as np




app = Flask(__name__)


def cosine_similarity(v1, v2):
    v1 = np.array(v1)
    v2 = np.array(v2)

    return np.dot(v1, v2) / (
        np.linalg.norm(v1) * np.linalg.norm(v2)
    )






@app.route('/check-face-demo', methods=['POST'])
def check_face():



    start = time.perf_counter()
    if 'image1'not in request.files or 'image2' not in request.files:
        return jsonify({
            "success": False,
            "message": "No image provided"
        })





    try:
        img1 = request.files['image1']
        img2 = request.files['image2']


        img1Embedding = DeepFace.represent(
            img_path=img1,  
            model_name="ArcFace",
            # detector_backend="retinaface",
            detector_backend="opencv",
            normalization="ArcFace"
        )[0]["embedding"]

        img2Embedding = DeepFace.represent(
            img_path=img2,  
            model_name="ArcFace",
            detector_backend="opencv",
            normalization="ArcFace"
        )[0]["embedding"]

        score = cosine_similarity(img1Embedding , img2Embedding)

        end = time.perf_counter()

        return jsonify({
            # "success": True,
            # "similarity": float(score),
            # "same_person": bool(score > 0.45),
            # 'Time':end - start
            'vector1':img1Embedding
        })

    except Exception as e:

        return jsonify({
            "success": False,
            "message": str(e)
        })


if __name__ == "__main__":
    app.run(
        debug=True,
        port=5002
    )