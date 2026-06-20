<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Types</title>
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            color: #000000;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table-container {
            width: 100%;
            max-width: 800px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: #ffffff;
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #000000;
        }

        th {
            background-color: #000000;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .action-icon {
            cursor: pointer;
            width: 22px;
            height: 22px;
            padding: 5px;
            border-radius: 4px;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin: 0 4px;
            vertical-align: middle;
        }

        .btn-delete:hover {
            background-color: #e2e8f0;
            transform: scale(1.1);
        }

      
        .fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: #000000;
            color: #ffffff;
            border-radius: 50%;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease, background-color 0.2s ease;
            z-index: 99;
        }

        .fab:hover {
            transform: scale(1.05);
            background-color: #222222;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); 
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-content {
            background-color: #ffffff;
            padding: 30px;
            border: 2px solid #000000;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            gap: 15px;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.show .modal-content {
            transform: translateY(0);
        }

        .modal-content h3 {
            margin: 0 0 10px 0;
            text-transform: uppercase;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .modal-content input, .modal-content select {
            width: 100%;
            padding: 10px;
            border: 1px solid #000000;
            font-size: 1rem;
            box-sizing: border-box;
            outline: none;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .modal-btn {
            flex: 1;
            padding: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #000000;
            transition: all 0.2s ease;
        }

        .btn-save {
            background-color: #000000;
            color: #ffffff;
        }

        .btn-save:hover {
            background-color: #ffffff;
            color: #000000;
        }

        .btn-cancel {
            background-color: #ffffff;
            color: #000000;
        }

        .btn-cancel:hover {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h2>Subscription Types</h2>

    <button class="fab" id="openModalBtn">+</button>

    <div class="modal-overlay" id="formModal">
        <div class="modal-content">
            <h3>Add New Subscription Type</h3>
            
            <form action="/subscription-types" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <input type="number" name="duration" placeholder="Duration" required min="1">
                    
                    <select name="duration_unit" required>
                        <option value="" disabled selected>Select Unit</option>
                        <option value="day">Day</option>
                        <option value="day">Week</option>
                        <option value="month">Month</option>
                        <option value="year">Year</option>
                    </select>

                    <input type="number" name="price" placeholder="Price" required min="0" step="0.01">

                    <div class="modal-buttons">
                        <button type="button" class="modal-btn btn-cancel" id="closeModalBtn">Cancel</button>
                        <button type="submit" class="modal-btn btn-save">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Duration</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($types as $type)
                    <tr data-id="{{ $type->id }}">
                        <td>{{ $type->id }}</td>
                        <td>{{ $type->duration }}</td>
                        <td>{{ $type->duration_unit }}</td>
                        <td>{{ $type->price }}</td>
                        <td>
                            <img src="{{ asset('/images/delete.svg') }}" alt="delete" class="action-icon btn-delete">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

<script>
    const modal = document.getElementById('formModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');

    openBtn.addEventListener('click', () => {
        modal.classList.add('show');
    });

    closeBtn.addEventListener('click', () => {
        modal.classList.remove('show');
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('show');
        }
    });

    document.querySelectorAll('.btn-delete').forEach(d => {
        d.addEventListener("click", function () {
            let id = this.closest('tr').dataset.id;
            deleteType(id);
        });
    });

    async function deleteType(id) {
        if (!confirm("Are you sure you want to delete this type?")) {
            return;
        }

        try {
            let response = await fetch(`/subscription-types/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });

            let result = await response.json();
            alert(result.message);
            window.location.reload(); 
            
        } catch (error) {
            console.error("Error during deletion:", error);
        }
    }
</script>
</body>
</html>