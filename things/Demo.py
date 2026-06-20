from deepface import DeepFace
from flask import Flask, request, jsonify
import base64
from flask_cors import CORS
import matplotlib.pyplot as plt
import cv2
import time

app = Flask(__name__)
CORS(app)



@app.route("/upload", methods=["POST"])
def upload():
    
    storedImage = "me.jpg"
   
    start_time = time.perf_counter()

    data = request.json["image"]
    header, encoded = data.split(",", 1)

    image_bytes = base64.b64decode(encoded)

    currentImage = "captured.jpg"

    with open(currentImage, "wb") as f:
        f.write(image_bytes)





    result = DeepFace.verify(
        img1_path=storedImage,
        img2_path=currentImage,
        model_name="Facenet"
    )

    # img1 = cv2.imread(storedImage)
    # img2 = cv2.imread(currentImage)

    # img1 = cv2.cvtColor(img1, cv2.COLOR_BGR2RGB)
    # img2 = cv2.cvtColor(img2, cv2.COLOR_BGR2RGB)

    # plt.figure(figsize=(10, 5))

    # plt.subplot(1, 2, 1)
    # plt.imshow(img1)
    # plt.title("Stored Image")
    # plt.axis("off")

    # plt.subplot(1, 2, 2)
    # plt.imshow(img2)
    # plt.title("Captured Image")
    # plt.axis("off")

    # plt.suptitle(
    #     f"Match: {result['verified']} | Distance: {round(result['distance'], 4)}"
    # )

    # plt.show()

    end_time = time.perf_counter()

    comparison_time = end_time - start_time

    print(f"Comparison Time: {comparison_time:.4f} seconds")

    




    with open("embeddings.txt", "w", encoding="utf-8") as f:

        f.write("===== STORED IMAGE =====\n")
        f.write(",".join(map(str, stored_embedding)))

        f.write("\n\n")

        f.write("===== CURRENT IMAGE =====\n")
        f.write(",".join(map(str, current_embedding)))
        
    return jsonify({
        "match": bool(result["verified"]),
        "distance": result["distance"],
        "raw": result,
        "Comparison Time":comparison_time
    })
if __name__ == "__main__":
    app.run(port=5001, debug=True)