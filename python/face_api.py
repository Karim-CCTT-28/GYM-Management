import numpy as np
import tempfile
from flask import Flask, request, jsonify
from deepface import DeepFace
import os
import json
import requests
import matplotlib.pyplot as plt
import cv2
import time
from datetime import datetime
from flask_cors import CORS

app = Flask(__name__)
CORS(app)
# storage/app/public
BASE_DIR = os.path.join(
    os.path.dirname(os.path.abspath(__file__)),
    "..",
    "storage",
    "app",
    "public"
)

USERS_DIR = os.path.join(BASE_DIR, "users")
SUBSCRIBERS_DIR = os.path.join(BASE_DIR, "Subscribers")


def save_comparison_image(known_path, unknown_path, result):

    img1 = cv2.imread(known_path)
    img2 = cv2.imread(unknown_path)

    img1 = cv2.cvtColor(img1, cv2.COLOR_BGR2RGB)
    img2 = cv2.cvtColor(img2, cv2.COLOR_BGR2RGB)

    plt.figure(figsize=(10, 5))

    plt.subplot(1, 2, 1)
    plt.imshow(img1)
    plt.title("Stored Image")
    plt.axis("off")

    plt.subplot(1, 2, 2)
    plt.imshow(img2)
    plt.title("Captured Image")
    plt.axis("off")

    plt.suptitle(
        f"Match: {result['verified']} | Distance: {round(result['distance'], 4)}"
    )

    filename = datetime.now().strftime(
        "result_%Y%m%d_%H%M%S.png"
    )

    plt.savefig(filename)

    plt.close()


def userFace(user_vector, file):
    print("UserFace called")
   




    vector2 = DeepFace.represent(
            img_path=file,  
            model_name="ArcFace",
            detector_backend="opencv",
            normalization="ArcFace"
        )[0]["embedding"]

    score = cosine_similarity(user_vector , vector2)

    print("similarity : ")
    print( float(score))
    print( bool(score > 0.45))
    return jsonify({
            "success": True,
            "similarity": float(score),
            "same_person": bool(score > 0.45)
        })


def cosine_similarity(v1, v2):
    v1 = np.array(v1)
    v2 = np.array(v2)

    return np.dot(v1, v2) / (
        np.linalg.norm(v1) * np.linalg.norm(v2)
    )

def subscriberFace(file , subscribers):


        start = time.perf_counter()
        current_vector = DeepFace.represent(
            img_path=file,  
            model_name="ArcFace",
            detector_backend="opencv",
            normalization="ArcFace"
        )[0]["embedding"]



        best_score = 0
        subscriber_id = None
        

        for subscriber in subscribers:
         
            sub_vector = subscriber["vector"]
            

            score = cosine_similarity(
                current_vector,
                sub_vector
            )

            if score > best_score:
                best_score = score
                subscriber_id = subscriber["id"]

            print("similarity")
            print(bool(score>0.45))
            print(float(score))
            print("id")
            print(subscriber['id'])
            print("----------------------------------------------------------")
        end = time.perf_counter()
        result = bool(best_score > 0.45)
        if not result:
            subscriber_id = None
        return jsonify({
            "success": True,
            "similarity": float(best_score),
            "same_person": result,
            'subscriber_id':subscriber_id,
            'Time' : end - start
        })

 



def setVector(file):

        embedding = DeepFace.represent(
            img_path=file,
            model_name="ArcFace",
            normalization="ArcFace"
        )[0]["embedding"]
        return jsonify({
            "success": True,
            "vector": embedding
        })


   






@app.route('/check-face', methods=['POST'])
def check_face():

    if 'image' not in request.files:
        return jsonify({
            "success": False,
            "message": "No image provided"
        })

    file = request.files['image']

    UPLOAD_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), "uploaded_faces")
    if not os.path.exists(UPLOAD_DIR):
            os.makedirs(UPLOAD_DIR) 

    filename = datetime.now().strftime("face_%Y%m%d_%H%M%S.jpg")
    save_path = os.path.join(UPLOAD_DIR, filename)

    file.save(save_path)


    try:
        

          
        if request.form['functionID'] == '1':

            user_vector = json.loads(request.form['user_vector'])

            return userFace(
                user_vector,
                file
            )
        elif request.form['functionID']=='2':

            return subscriberFace(file , json.loads(request.form['subscribers']))
        
        elif request.form['functionID']=='3':
           return setVector(file)
        

        elif request.form['functionID']=='4':
            return setVector(file)




    except Exception as e:

        print(str(e))
        return jsonify({
            "success": False,
            "message": "python Error :"+str(e)
        })


if __name__ == "__main__":
    app.run(
        debug=True,
        port=5001
    )