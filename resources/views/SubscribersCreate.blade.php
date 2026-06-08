<style>
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

    #save {
        width: 100px;
        height: 30px;
        border: none;
        border-radius: 5px;
        background-color: green;
        color: white;
    }

  

    #save:hover {
        background-color: #0D530E;
    }


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
</style>



<form class="new-subscriber">
    <input type="file" accept="image/*" required>
    <input type="text" name="" id="" placeholder="Name" required>
    <input type="text" placeholder="Phone" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
    <div type="button" id="finger"><img src="{{ asset("images/finger.png") }}" alt=""></div>

    <button type="submit" id="save">save</button>
</form>



