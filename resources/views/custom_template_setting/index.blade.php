<x-layout :title="$title">

    <style>
        label {
            font-weight: bold;
            font-size: 10pt;
        }

        .form-select {
            width: 40%;
        }

        #btn {
            font-size: 10pt;
        }

        #btnSave {
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

        #leftBox, #rightBox {
            width: 300px;
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

        #columnList {
            margin: 10px 0 0 20px;
            font-size: 10pt;
            font-weight: bold;
        }        
    </style>

    <section class="section">

        <div class="card p-4">
                <div class="d-flex justify-content-start mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTemplate"><i class='bx bx-plus-medical'></i></button>
                </div>
                <table class="table table-striped is-fullwidth border mb-2 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Template Name</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>J&T</td>
                            <td>Pending List</td>
                            <td>
                                <a href="" class="btn btn-warning text-white"><i class='bx bx-pencil'></i></a>
                                <a href="" class="btn btn-danger"><i class='bx bxs-trash'></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
        </div>

    </section>

    <!-- The Modal -->
    <div class="modal fade" id="addTemplate">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
        
                <!-- Modal Header -->
                <div class="modal-header text-center">
                    <h5 class="modal-title mx-auto"><strong>Create Custom Template</strong></h5>
                </div>
            
                <!-- Modal body -->
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="mb-2">Template Name: </label>
                        <input type="text" class="form-control" name="template_name">
                    </div>

                    <div class="mb-3">
                        <label class="mb-2">Template Type: </label>
                        <select name="template_type" class="form-select">
                            <option value="1">Pending List</option>
                            <option value="2">Bucket List</option>
                            <option value="3">Packing List</option>
                            <option value="4">Pending Shipping List</option>
                            <option value="5">In Transit List</option>
                            <option value="6">Delivered List</option>
                            <option value="7">Return List</option>
                            <option value="8">Claim List</option>
                            <option value="9">Reject List</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="mb-2">Template Header </label>
                        <br>
                        <textarea class="form-control" name="template_header" rows="5"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="mb-2">Template Column</label>

                        <div class="container">
                            <div id="leftBox">
                                <p id="columnList">Column List</p>
                                @foreach($dataFromDB as $item)
                                <div class="list" draggable="true" data-column-id="{{ $item->column_id }}">{{ $item->column_display_name }}</div>
                                @endforeach
                            </div>

                            <i class='bx bxs-right-arrow-alt arrow-icon'></i>

                            <div id="rightBox">
                                <p id="columnList">Column List</p>
                            </div>
                        </div>
                    </div>
                </div>
            
                    <!-- Modal footer -->
                <div class="modal-footer d-flex justify-content-center">
                    <button id="btnSave" type="submit" class="btn btn-primary">Save</button>
                    <button id="btn" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
        
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                let lists = document.getElementsByClassName("list");
                let rightBox = document.getElementById("rightBox");
                let leftBox = document.getElementById("leftBox");

                for (let list of lists) {
                    list.addEventListener("dragstart", function (e) {
                        e.dataTransfer.setData("text/plain", list.innerText);
                        e.dataTransfer.setData("column-id", list.getAttribute("data-column-id"));
                    });
                }

                rightBox.addEventListener("dragover", function (e) {
                    e.preventDefault();
                });

                rightBox.addEventListener("drop", function (e) {
                    e.preventDefault();

                    let data = e.dataTransfer.getData("text/plain");
                    let columnId = e.dataTransfer.getData("column-id");
                    let draggedItem = document.createElement("div");
                    
                    draggedItem.className = "list";
                    draggedItem.innerText = data;

                    // Add remove button
                    let removeButton = document.createElement("i");
                    removeButton.className = "bx bx-x-circle remove-btn";
                    removeButton.addEventListener("click", function () {
                        rightBox.removeChild(draggedItem);
                    });

                    // Append remove button to dragged item
                    draggedItem.appendChild(removeButton);

                    // Append column_id as data attribute
                    draggedItem.setAttribute("data-column-id", columnId);

                    // Append dragged item to right box
                    rightBox.appendChild(draggedItem);
                });

                // Submit Modal
                $("#btnSave").on("click", function (e) {
                    e.preventDefault();

                    // Collect data from the modal fields
                    let templateName = document.querySelector("input[name='template_name']").value;
                    let templateType = document.querySelector("select[name='template_type']").value;
                    let templateHeader = document.querySelector("textarea[name='template_header']").value;
                    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Collect data from the rightBox (dragged items)
                    let draggedItems = document.querySelectorAll("#rightBox .list");
                    let columnData = Array.from(draggedItems).map(item => item.innerText);

                    // Prepare the data to be sent to the server
                    let data = {
                        template_name: templateName,
                        template_type: templateType,
                        template_header: templateHeader,
                        columns: columnData,
                    };

                    // Use AJAX to send the data to a Laravel route
                    $.ajax({
                        url: '{{ route("template_setting.save_template") }}',
                        method: 'POST',
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        success: function (response) {
                            console.log(response);
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Your template has been saved.',
                            });
                        },
                        error: function (error) {
                            console.error(error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.responseJSON.message || 'Something went wrong.',
                            });
                        }
                    });
                });
            });
        </script>
    </x-slot>

</x-layout>
