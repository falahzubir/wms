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

        .customBtn, .form-control {
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

        .leftBox, .rightBox {
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

        .columnList {
            margin: 10px 0 0 20px;
            font-size: 10pt;
            font-weight: bold;
        }

        .error-message {
            font-size: 10pt;
            color: red;
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
                    @php
                        $rowNumber = ($templateMain->currentPage() - 1) * $templateMain->perPage() + 1;
                    @endphp
                    @forelse ($templateMain as $row)
                        <tr>
                            <td>{{ $rowNumber++ }}</td>
                            <td>{{ $row->template_name }}</td>
                            <td>
                                @switch($row->template_type)
                                    @case(1)
                                        Pending List
                                        @break
                                    @case(2)
                                        Bucket List
                                        @break
                                    @case(3)
                                        Packing List
                                        @break
                                    @case(4)
                                        Pending Shipping List
                                        @break
                                    @case(5)
                                        In Transit List
                                        @break
                                    @case(6)
                                        Delivered List
                                        @break
                                    @case(7)
                                        Return List
                                        @break
                                    @case(8)
                                        Claim List
                                        @break
                                    @case(9)
                                        Reject List
                                        @break
                                    @default
                                @endswitch
                            </td>
                            <td>
                                <button class="btn btn-warning text-white"
                                        data-bs-toggle="modal" data-bs-target="#editTemplate"
                                        data-template-id="{{ $row->id }}"
                                        data-template-name="{{ $row->template_name }}"
                                        data-template-type="{{ $row->template_type }}"
                                        data-template-header="{{ $row->template_header }}">
                                    <i class='bx bx-pencil'></i>
                                </button>
                                <a href="#" class="btn btn-danger delete-template" data-template-id="{{ $row->id }}"><i class='bx bxs-trash'></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No template</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end">                       
                {{ $templateMain->links() }}
            </div>
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
                        <select id="template_type" name="template_type" class="form-select">
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
                            <div id="leftBox" class="leftBox">
                                <p class="columnList">Column List</p>
                                @foreach($columnMain as $item)
                                <div class="list" draggable="true" data-column-id="{{ $item->id }}">{{ $item->column_display_name }}</div>
                                @endforeach
                            </div>

                            <i class='bx bxs-right-arrow-alt arrow-icon'></i>

                            <div id="rightBox" class="rightBox">
                                <p class="columnList">Column List</p>
                            </div>
                        </div>

                        <input type="hidden" name="column_order" value="">
                    </div>
                </div>
            
                    <!-- Modal footer -->
                <div class="modal-footer d-flex justify-content-center">
                    <button id="btnSave" type="submit" class="btn btn-primary customBtnSave">Save</button>
                    <button type="button" class="btn btn-secondary customBtn" data-bs-dismiss="modal">Cancel</button>
                </div>
        
            </div>
        </div>
    </div>

    <!-- The Edit Modal -->
    <div class="modal fade" id="editTemplate">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header text-center">
                    <h5 class="modal-title mx-auto"><strong>Edit Custom Template</strong></h5>
                </div>
                <!-- Modal body -->
                <div class="modal-body p-4">

                    <input type="hidden" id="edit_template_id" name="edit_template_id">

                    <div class="mb-3">
                        <label class="mb-2">Template Name: </label>
                        <input type="text" id="edit_template_name" class="form-control" name="edit_template_name">
                    </div>

                    <div class="mb-3">
                        <label class="mb-2">Template Type: </label>
                        <select id="edit_template_type" name="edit_template_type" class="form-select">
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
                        <textarea id="edit_template_header" class="form-control" name="edit_template_header" rows="5"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="mb-2">Template Column</label>
                        <div class="container">
                            <div id="leftBoxEdit" class="leftBox">
                                <p class="columnList">Column List</p>
                                @foreach($columnMain as $item)
                                    <div class="list" draggable="true" data-column-id="{{ $item->id }}">{{ $item->column_display_name }}</div>
                                @endforeach
                            </div>

                            <i class='bx bxs-right-arrow-alt arrow-icon'></i>

                            <div id="rightBoxEdit" class="rightBox">
                            </div>
                        </div>

                        <input type="hidden" name="edit_column_order" value="">
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer d-flex justify-content-center">
                    <button id="btnUpdate" type="submit" class="btn btn-primary customBtnSave">Update</button>
                    <button type="button" class="btn btn-secondary customBtn" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>


    <x-slot name="script">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {

                // ================ Drag & Drop Function ================== //
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

                    // Update the order of column IDs
                    updateColumnOrder();
                });

                function updateColumnOrder() {
                    let columnOrder = [];
                    let draggedItems = document.querySelectorAll("#rightBox .list");

                    for (let draggedItem of draggedItems) {
                        let columnId = draggedItem.getAttribute("data-column-id");
                        columnOrder.push(columnId);
                    }

                    // Store the order in a hidden input field or send it directly to the server
                    document.querySelector("input[name='column_order']").value = JSON.stringify(columnOrder);
                }

                // ================ Add Function ================== //
                $("#btnSave").on("click", function (e) {
                    e.preventDefault();

                    // Collect data from the modal fields
                    let templateName = $("input[name='template_name']");
                    let templateType = $("select[name='template_type']");
                    let templateHeader = $("textarea[name='template_header']");
                    let csrfToken = $("meta[name='csrf-token']").attr("content");

                    // Collect data from the rightBox (dragged items)
                    let draggedItems = document.querySelectorAll("#rightBox .list");

                    // This are use to check input validation
                    templateName.removeClass("is-invalid");
                    templateType.removeClass("is-invalid");
                    templateHeader.removeClass("is-invalid");

                    $(".error-message").remove();

                    if (!templateName.val() || !templateType.val() || !templateHeader.val() || !draggedItems.length) {
                        // Add error styles to the input fields
                        if (!templateName.val()) {
                            templateName.addClass("is-invalid");
                            templateName.after('<div class="error-message">*required</div>');
                        }

                        if (!templateType.val()) {
                            templateType.addClass("is-invalid");
                            templateType.after('<div class="error-message">*select template type</div>');
                        }

                        if (!templateHeader.val()) {
                            templateHeader.addClass("is-invalid");
                            templateHeader.after('<div class="error-message">*required</div>');
                        }
                        return;
                    }

                    let columnIds = [];

                    for (let draggedItem of draggedItems) {
                        let columnId = draggedItem.getAttribute("data-column-id");
                        columnIds.push(columnId);
                    }

                    // Prepare the data to be sent to the server
                    let data = {
                        template_name: templateName.val(),
                        template_type: templateType.val(),
                        template_header: templateHeader.val(),
                        columns: columnIds,
                        column_order: JSON.parse(document.querySelector("input[name='column_order']").value),
                    };

                    // Use AJAX to send the data to a Laravel route
                    $.ajax({
                        url: '{{ route("custom_template_setting.save") }}',
                        method: 'POST',
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        success: function (response) {
                            console.log(response);
                            Swal.fire({
                                icon: 'success',
                                title: 'Done!',
                                text: 'Custom Template Created',
                            }).then(function () {
                                // Close the modal
                                $("#addTemplate").modal('hide');

                                // Refresh the page
                                location.reload();
                            });
                        },
                        error: function (error) {
                            console.error(error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.responseJSON.message || 'Something went wrong.',
                            }).then(function () {
                                // Close the modal
                                $("#addTemplate").modal('hide');

                                // Refresh the page
                                location.reload();
                            });
                        }
                    });
                });


                // ================ Edit Function ================== //
                $("#editTemplate").on("show.bs.modal", function (e) {
                    console.log("Edit Modal Show"); // Check if this line is executed

                    let templateId = $(e.relatedTarget).data("template-id");
                    let templateName = $(e.relatedTarget).data("template-name");
                    let templateType = $(e.relatedTarget).data("template-type");
                    let templateHeader = $(e.relatedTarget).data("template-header");

                    console.log("Template ID:", templateId); // Check if values are correct

                    // Populate the editTemplate modal with data
                    $("#edit_template_id").val(templateId);
                    $("#edit_template_name").val(templateName);
                    $("#edit_template_type").val(templateType);
                    $("#edit_template_header").val(templateHeader);

                    // Load the columns in the right box for the specific template
                    loadAndDisplayColumnsForTemplate(templateId);

                    // Initialize drag-and-drop for the right box in the edit modal
                    initializeDragAndDropEdit();
                });

                function initializeDragAndDropEdit() {
                    let listsEdit = document.getElementsByClassName("list");
                    let rightBoxEdit = document.getElementById("rightBoxEdit");

                    for (let listEdit of listsEdit) {
                        listEdit.addEventListener("dragstart", function (e) {
                            e.dataTransfer.setData("text/plain", listEdit.innerText);
                            e.dataTransfer.setData("column-id", listEdit.getAttribute("data-column-id"));
                        });
                    }

                    rightBoxEdit.addEventListener("dragover", function (e) {
                        e.preventDefault();
                    });

                    rightBoxEdit.addEventListener("drop", function (e) {
                        e.preventDefault();

                        let dataEdit = e.dataTransfer.getData("text/plain");
                        let columnIdEdit = e.dataTransfer.getData("column-id");
                        let draggedItemEdit = document.createElement("div");

                        draggedItemEdit.className = "list";
                        draggedItemEdit.innerText = dataEdit;

                        // Add remove button
                        let removeButtonEdit = document.createElement("i");
                        removeButtonEdit.className = "bx bx-x-circle remove-btn";
                        removeButtonEdit.addEventListener("click", function () {
                            rightBoxEdit.removeChild(draggedItemEdit);
                            updateColumnOrderEdit(); // Update the order when an item is removed
                        });

                        // Append remove button to dragged item
                        draggedItemEdit.appendChild(removeButtonEdit);

                        // Append column_id as data attribute
                        draggedItemEdit.setAttribute("data-column-id", columnIdEdit);

                        // Append dragged item to right box
                        rightBoxEdit.appendChild(draggedItemEdit);

                        // Update the order of column IDs
                        updateColumnOrderEdit();
                    });
                }

                function loadAndDisplayColumnsForTemplate(templateId) {
                    // Use AJAX to fetch the columns for the specified template
                    $.ajax({
                        url: '{{ url("custom_template_setting/get_columns") }}/' + templateId,
                        method: 'GET',
                        success: function (response) {
                            console.log("Columns received:", response.columns);

                            let rightBoxEdit = document.getElementById("rightBoxEdit");
                            rightBoxEdit.innerHTML = ''; // Clear existing columns

                            // Append the columnList element
                            let columnListElement = document.createElement("p");
                            columnListElement.className = "columnList";
                            columnListElement.innerText = "Column List";
                            rightBoxEdit.appendChild(columnListElement);

                            // Display columns in the right box
                            response.columns.forEach(function (column) {
                                let columnElement = document.createElement("div");
                                columnElement.className = "list";
                                columnElement.draggable = true;
                                columnElement.setAttribute("data-column-id", column.id);
                                columnElement.innerText = column.column_display_name;

                                // Add remove button
                                let removeButton = document.createElement("i");
                                removeButton.className = "bx bx-x-circle remove-btn";
                                removeButton.addEventListener("click", function () {
                                    rightBoxEdit.removeChild(columnElement);
                                    updateColumnOrderEdit();
                                });

                                // Append remove button to column element
                                columnElement.appendChild(removeButton);

                                // Append column element to right box
                                rightBoxEdit.appendChild(columnElement);
                            });

                            // Update the order of column IDs
                            updateColumnOrderEdit();
                        },
                        error: function (error) {
                            console.error(error);
                        }
                    });
                }

                function updateColumnOrderEdit() {
                    let columnOrder = [];
                    let draggedItems = document.querySelectorAll("#rightBoxEdit .list");

                    for (let draggedItem of draggedItems) {
                        let columnId = draggedItem.getAttribute("data-column-id");
                        columnOrder.push(columnId);
                    }

                    // Store the order in a hidden input field or send it directly to the server
                    document.querySelector("input[name='edit_column_order']").value = JSON.stringify(columnOrder);
                }

                $("#btnUpdate").on("click", function (e) {
                    e.preventDefault();

                    let templateId = $("#edit_template_id");
                    let templateNameInput = $("#edit_template_name");
                    let templateTypeInput = $("#edit_template_type");
                    let templateHeaderInput = $("#edit_template_header");
                    let csrfToken = $("meta[name='csrf-token']").attr("content");

                    // Collect data from the rightBoxEdit (dragged items)
                    let draggedItems = $("#rightBoxEdit .list");

                    // Reset previous error styles and messages
                    templateNameInput.removeClass("is-invalid");
                    templateTypeInput.removeClass("is-invalid");
                    templateHeaderInput.removeClass("is-invalid");

                    $(".error-message").remove();

                    if (!templateNameInput.val() || !templateTypeInput.val() || !templateHeaderInput.val() || draggedItems.length === 0) {
                        // Add error styles to the input fields
                        if (!templateNameInput.val()) {
                            templateNameInput.addClass("is-invalid");
                            templateNameInput.after('<div class="error-message">*required</div>');
                        }

                        if (!templateTypeInput.val()) {
                            templateTypeInput.addClass("is-invalid");
                            templateTypeInput.after('<div class="error-message">*select template type</div>');
                        }

                        if (!templateHeaderInput.val()) {
                            templateHeaderInput.addClass("is-invalid");
                            templateHeaderInput.after('<div class="error-message">*required</div>');
                        }

                        return;
                    }

                    let columnIds = [];

                    for (let draggedItem of draggedItems) {
                        let columnId = parseInt(draggedItem.getAttribute("data-column-id"), 10);
                        if (columnId) {
                            columnIds.push(columnId);
                        }
                    }

                    // Prepare the data to be sent to the server
                    let data = {
                        template_id: templateId.val(),
                        template_name: templateNameInput.val(),
                        template_type: templateTypeInput.val(),
                        template_header: templateHeaderInput.val(),
                        columns: columnIds,
                        column_order: JSON.parse(document.querySelector("input[name='edit_column_order']").value),
                    };

                    console.log('templateId before AJAX call:', templateId);

                    // Use AJAX to send the data to a Laravel route
                    $.ajax({
                        url: '{{ route("custom_template_setting.update") }}',
                        method: 'POST',
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        success: function (response) {
                            console.log(response);
                            Swal.fire({
                                icon: 'success',
                                title: 'Done!',
                                text: 'Custom Template Edited',
                            }).then(function () {
                                // Close the modal
                                $("#editTemplate").modal('hide');

                                // Refresh the page
                                location.reload();
                            });
                        },
                        error: function (error) {
                            console.error(error);
                            console.error(data);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.responseJSON.message || 'Something went wrong.',
                            }).then(function () {
                                // Close the modal
                                $("#editTemplate").modal('hide');

                                // Refresh the page
                                location.reload();
                            });
                        }
                    });
                });


                // ================ Delete Function ================== //
                $(".delete-template").on("click", function (e) {
                    e.preventDefault();

                    let templateId = $(this).data("template-id");

                    // Use SweetAlert for confirmation
                    Swal.fire({
                        text: 'Delete Custom Template',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // User confirmed, proceed with delete
                            deleteTemplate(templateId);
                        }
                    });
                });

                function deleteTemplate(templateId) {
                    let csrfToken = $('meta[name="csrf-token"]').attr('content');

                    // Use AJAX to send the data to a Laravel route
                    $.ajax({
                        url: '{{ route("custom_template_setting.delete") }}',
                        method: 'DELETE',
                        data: { template_id: templateId },
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        success: function (response) {
                            console.log(response);
                            Swal.fire({
                                icon: 'success',
                                text: 'Custom Template Deleted.',
                            }).then(function () {
                                // Refresh the page or update the table data
                                location.reload();
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
                }

            });
        </script>
    </x-slot>

</x-layout>
