<style>
    .new-subscriber {

        height: 400px;
        width: 600px;
        position: absolute;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 30px;
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        background-color: white;
        z-index: 1;
    }

    #finger {

        background-color: #ccc;
        opacity: 40%;
        border-radius: 5px;
    }

    #finger img,
    #finger {
        height: 200px;
        width: 150px;
    }

    #save,
    #cancel-add {
        width: 100px;
        height: 30px;
        border: none;
        border-radius: 5px;
    }

    #save {
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

    div {
        height: 100%;
        display: flex;
        justify-content: center;
    }
</style>



<div>

    <form class="new-subscriber" action="/subscribers" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" accept="image/*" required>
        <input type="text" name="name" id="" placeholder="Name" required>
        <input type="text" name = "phone" placeholder="Phone" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
        <div type="button" id="finger"><img src="{{ asset("images/finger.png") }}" alt=""></div>

        <div class="buttons">
            <button type="button" id="cancel-add">cancel</button>
            <button type="submit" id="save">save</button>
        </div>
    </form>

    
    
    
    <script>
        let cancel = document.getElementById("cancel-add");
        cancel.addEventListener("click", function () {
            window.location.href = "/subscribers"
        })
    </script>
</div>


