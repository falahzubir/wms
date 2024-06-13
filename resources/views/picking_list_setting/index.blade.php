<x-layout :title="$title">
    <style>
        label {
            font-weight: bold;
            font-size: 10pt;
        }

        .form-select {
            width: 40%;
            font-size: 10pt;
        }

        .customBtn,
        .form-control {
            font-size: 10pt;
        }

        .customBtnSave {
            font-size: 10pt;
            background-color: #7166e0;
        }

        .box {
            width: 45%;
            height: 500px;
            background-color: silver;
            overflow: auto;
            padding: 10px;
        }

        .draggable {
            cursor: move;
            border: 1px solid #ccc;
            background-color: #fff;
            margin-bottom: 5px;
            padding: 5px;
        }

        .remove-btn {
            float: right;
            cursor: pointer;
            color: red;
        }

        .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .leftBox,
        .rightBox {
            width: 400px;
            height: 600px;
            margin: 20px;
            border: 1px solid black;
            background-color: #ccc;
            overflow: auto;
        }

        .list {
            background: white;
            height: 40px;
            margin: 20px;
            color: #7166e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: grab;
            font-size: 10pt;
            padding: 5px;
        }

        .arrow-icon {
            color: red;
            font-size: 35px;
            margin: 0 10px 0 0;
        }

        .columnList {
            margin: 10px 0 0 20px;
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
        }

        .columnElement {
            margin: 10px 0 0 20px;
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            color: #012970;
        }

        .error-message {
            font-size: 10pt;
            color: red;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-right: 30px;
        }

        .rightBox-header {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .rightBox-label {
            display: none;
            margin: 10px 0 0 20px;
            font-size: 10pt;
            color: #695b5b;

        }
    </style>

    <section class="section">
        <div class="card p-4">
            <div class="mb-3">
                <p class="columnElement">The picking list will be generated based on the following sorted sequence.</p>
                <div class="container">
                    <div id="leftBox" class="leftBox">
                        <p class="columnList">Product</p>
                        @foreach ($products as $item)
                            <div id="item-{{ $item->id }}" class="list" draggable="true"
                                data-list-id="{{ $item->id }}">
                                {{ $item->name }}
                            </div>
                        @endforeach
                    </div>

                    <i class='bx bxs-right-arrow-alt arrow-icon'></i>

                    <div id="rightBox" class="rightBox">
                        <div class="rightBox-header">
                            <p class="columnList">Picking Sequence</p>
                            <p class="rightBox-label" id="rightBoxLabel">Sorted Product(s)</p>
                        </div>
                        <div class="rightBox-content" id="sortableItem">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="column_order" value="">
            </div>
            <div class="modal-footer">
                <button id="btnSave" type="submit" class="btn btn-primary customBtnSave">Update</button>
            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <x-slot name="script">
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let lists = document.getElementsByClassName("list");
                let rightBox = document.getElementById("rightBox");
                let leftBox = document.getElementById("leftBox");
                let sortableItem = document.getElementById("sortableItem");
                let rightBoxLabel = document.getElementById("rightBoxLabel");
                let DragFromLeftBox = false; //flag to track whether the dragged item coming from the left box

                for (let list of lists) {
                    list.addEventListener("dragstart", function(e) {
                        DragFromLeftBox = true;
                        e.dataTransfer.setData("text/plain", list.innerText);
                        e.dataTransfer.setData("list-id", list.getAttribute("data-list-id"));
                    });
                }

                rightBox.addEventListener("dragover", function(e) {
                    e.preventDefault();
                });

                rightBox.addEventListener("drop", function(e) {
                    e.preventDefault();

                    if (DragFromLeftBox) {
                        let data = e.dataTransfer.getData("text/plain");
                        let listId = e.dataTransfer.getData("list-id");

                        // Check if the item already exists in the right box
                        let existingItem = document.querySelector(
                            `#sortableItem .list[data-list-id="${listId}"]`);

                        if (!existingItem) {
                            // Create a new item if it doesn't exist in the right box
                            let draggedItem = document.createElement("div");
                            draggedItem.className = "list";
                            draggedItem.innerText = data;
                            draggedItem.setAttribute("draggable", "true");
                            draggedItem.setAttribute("data-list-id", listId);

                            // Add remove button
                            let removeButton = document.createElement("i");
                            removeButton.className = "bx bx-x-circle remove-btn";
                            removeButton.addEventListener("click", function() {
                                sortableItem.removeChild(draggedItem);
                                let originalItem = document.querySelector(
                                    `#leftBox .list[data-list-id="${listId}"]`);
                                if (originalItem) {
                                    originalItem.style.display =
                                        "block"; // Show the original item in the left box
                                }
                                updateColumnOrder();
                                updateRightBoxLabel();
                            });

                            // Append remove button to dragged item
                            draggedItem.appendChild(removeButton);

                            // Append dragged item to sortable box
                            sortableItem.appendChild(draggedItem);

                            // Hide the original item in the left box
                            let originalItem = document.querySelector(
                                `#leftBox .list[data-list-id="${listId}"]`);
                            if (originalItem) {
                                originalItem.style.display = "none";
                            }

                            // Update the order of column IDs
                            updateColumnOrder();
                            updateRightBoxLabel();
                        }

                        // Reset the flag
                        DragFromLeftBox = false;
                    }
                });
                
                //update comment
                function updateRightBoxLabel() {
                    let draggedItems = document.querySelectorAll("#sortableItem .list");
                    if (draggedItems.length > 0) {
                        rightBoxLabel.style.display = "block";
                    } else {
                        rightBoxLabel.style.display = "none";
                    }
                }

                // Initialize SortableJS on the sortable box
                new Sortable(sortableItem, {
                    animation: 80,
                    handle: '.list', //only handle the class list
                    onEnd: function() {
                        updateColumnOrder();
                    }
                });

                function updateColumnOrder() {
                    let columnOrder = [];
                    let draggedItems = document.querySelectorAll("#sortableItem .list");

                    for (let draggedItem of draggedItems) {
                        let listId = draggedItem.getAttribute("data-list-id");
                        columnOrder.push(listId);
                    }
                    
                    // Store the order in a hidden input field or send it directly to the server
                    document.querySelector("input[name='column_order']").value = JSON.stringify(columnOrder);
                }
            });
        </script>
    </x-slot>

</x-layout>
