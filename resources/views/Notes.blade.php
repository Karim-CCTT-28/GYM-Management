@extends("layouts.navigation")

@section("Notes")

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .btn-new {
            background-color: #ccc;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.2s;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;

            top: 20px;
            right: 20px;
        }

        .btn-new:hover {
            background-color: #000;
        }

        #notes-container {
            display: flex;
            gap: 20px;
        }

        .note-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .note-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .note-card p {
            color: #374151;
            line-height: 1.6;
            margin-top: 0;
            flex-grow: 1;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            border-top: 1px solid #f3f4f6;
            padding-top: 15px;
        }

        .btn {
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            font-size: 14px;
        }

        .edit-btn {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        .edit-btn:hover {
            background-color: #e5e7eb;
            color: #1f2937;
        }

        .delete-btn {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .delete-btn:hover {
            background-color: #fecaca;
            color: #b91c1c;
        }

        .dialog {
            display: none;
            flex-direction: column;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            z-index: 1000;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 100vw rgba(0, 0, 0, 0.5);
        }

        .dialog textarea {
            width: 100%;
            min-height: 150px;
            padding: 15px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            resize: vertical;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
            outline: none;
            transition: border-color 0.2s;
        }

        .dialog textarea:focus {
            border-color: #ccc;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .dialog-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .save-btn {
            background-color: #000;
            color: white;
        }

        .save-btn:hover {
            background-color: #ccc;
        }
    </style>

    <button id="new-note" class="btn-new">+</button>

    <div id="notes-container">
        @foreach($notes as $note)
            <div class="note-card" data-id="{{ $note->id }}">
                <p>{{ $note->note }}</p>

                <div class="actions">
                    <button class="btn edit-btn">Edit</button>
                    <button class="btn delete-btn">Delete</button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="dialog" id="dialog">
        <h3 style="margin-top:0; margin-bottom: 15px; color:#111827;">Note Details</h3>

        <input type="hidden" id="note_id">
        <textarea id="note_text" placeholder="Write your note here..."></textarea>

        <div class="dialog-actions">
            <button id="cancel-note" class="btn edit-btn">Cancel</button>
            <button id="save-note" class="btn save-btn">Save</button>
        </div>
    </div>

    <script>
        let dialog = document.getElementById('dialog');

        document.getElementById('new-note').addEventListener('click', () => {
            document.getElementById('note_id').value = '';
            document.getElementById('note_text').value = '';
            dialog.style.display = 'flex';
        });

        document.getElementById('cancel-note').addEventListener('click', () => {
            dialog.style.display = 'none';
        });

        document.getElementById('save-note').addEventListener('click', async () => {
            let id = document.getElementById('note_id').value;
            let note = document.getElementById('note_text').value;

            let url = '/notes';
            let method = 'POST';

            if (id) {
                url = `/notes/${id}`;
                method = 'PUT';
            }

            let response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    note: note
                })
            });

            let result = await response.json();

            if (result.status) {
                location.reload();
            }
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {

            if (btn.id === 'cancel-note') return;

            btn.addEventListener('click', async function () {
                let id = this.closest('.note-card').dataset.id;
                let response = await fetch(`/notes/${id}`);
                let note = await response.json();

                document.getElementById('note_id').value = note.id;
                document.getElementById('note_text').value = note.note;

                dialog.style.display = 'flex';
            });
        });

        // حذف الملاحظة
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async function () {
                if (!confirm('Are you sure you want to delete this note?')) return;

                let id = this.closest('.note-card').dataset.id;
                let response = await fetch(`/notes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                let result = await response.json();

                if (result.status) {
                    location.reload();
                }
            });
        });
    </script>

@endsection