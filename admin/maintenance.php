<div class="col-lg-12">
    <div class="row">
        <!-- Category List Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">Category List</h4>
                </div>
                <div class="card-body">
                    <div class="col-md-4 offset-md-9">
                        <a class="btn btn-primary" href="javascript:void(0)" id="newcat">Add New</a>
                    </div>
                    <br>
                    <div class="table-responsive ps">
                        <table class="table table-striped" id="brand">
                            <thead>
                            <tr>
                                <th>Category</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            // Fetch categories from the database
                            $result = mysqli_query($con, "SELECT * FROM categories ORDER BY cat_title ASC");
                            while (list($cat_id, $cat_title) = mysqli_fetch_array($result)) {
                                echo "<tr>
                                        <td>$cat_title</td>
                                        <td>
                                            <a class='btn btn-primary btn-sm edit_cat' href='javascript:void(0)' data-id='$cat_id' data-name='$cat_title'>Edit</a>
                                            <a class='btn btn-danger btn-sm remove_cat' href='javascript:void(0)' data-id='$cat_id'>Delete</a>
                                        </td>
                                      </tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brand List Section -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">Brand List</h4>
                </div>
                <div class="card-body">
                    <div class="col-md-4 offset-md-9">
                        <a class="btn btn-primary" href="javascript:void(0)" id="newbrand">Add New</a>
                    </div>
                    <br>
                    <div class="table-responsive ps">
                        <table class="table table-striped" id="cat">
                            <thead>
                            <tr>
                                <th>Brand</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            // Fetch brands from the database
                            $result = mysqli_query($con, "SELECT * FROM brands ORDER BY brand_title ASC");
                            while (list($brand_id, $brand_title) = mysqli_fetch_array($result)) {
                                echo "<tr>
                                        <td>$brand_title</td>
                                        <td>
                                            <a class='btn btn-primary btn-sm edit_brand' href='javascript:void(0)' data-id='$brand_id' data-name='$brand_title'>Edit</a>
                                            <a class='btn btn-danger btn-sm remove_brand' href='javascript:void(0)' data-id='$brand_id'>Delete</a>
                                        </td>
                                      </tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Adding/Editing Categories and Brands -->
<div class="modal fade" id="catmodal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Category</h5>
            </div>
            <div class="modal-body">
                <form action="" id="catfrm">
                    <input type="hidden" name="id">
                    <input type="hidden" name="cat" value="1">
                    <input type="hidden" name="type" value="add">
                    <div class="form-group">
                        <label class="control-label">Category Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit" onclick="$('#catmodal form').submit()" data-dismiss="modal">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="brandmodal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Brand</h5>
            </div>
            <div class="modal-body">
                <form action="" id="brandfrm">
                    <input type="hidden" name="id">
                    <input type="hidden" name="brand" value="1">
                    <input type="hidden" name="type" value="add">
                    <div class="form-group">
                        <label class="control-label">Brand Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit" onclick="$('#brandmodal form').submit()" data-dismiss="modal">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize DataTables for Category and Brand
    $('#cat').dataTable();
    $('#brand').dataTable();

    // Add New Category
    $('#newcat').click(function () {
        $('#catfrm [name="id"]').val('');
        $('#catfrm [name="name"]').val('');
        $('#catfrm [name="type"]').val('add');
        $('#catmodal .modal-title').html('New Category');
        $('#catmodal').modal('show');
    });

    // Add New Brand
    $('#newbrand').click(function () {
        $('#brandfrm [name="id"]').val('');
        $('#brandfrm [name="name"]').val('');
        $('#brandfrm [name="type"]').val('add');
        $('#brandmodal .modal-title').html('New Brand');
        $('#brandmodal').modal('show');
    });

    // Form Submission for Category and Brand
    $('#catfrm, #brandfrm').submit(function (e) {
        e.preventDefault();
        start_load();
        $.ajax({
            url: 'maintenance_operations.php',
            method: "POST",
            data: $(this).serialize(),
            success: function (resp) {
                if (resp == 1) {
                    alert("Data successfully saved.");
                    location.reload();
                }
            }
        });
    });

    // Edit Category
    $('.edit_cat').click(function () {
        $('#catfrm [name="id"]').val(this.dataset.id);
        $('#catfrm [name="name"]').val(this.dataset.name);
        $('#catfrm [name="type"]').val('update');
        $('#catmodal .modal-title').html('Edit Category');
        $('#catmodal').modal('show');
    });

    // Edit Brand
    $('.edit_brand').click(function () {
        $('#brandfrm [name="id"]').val(this.dataset.id);
        $('#brandfrm [name="name"]').val(this.dataset.name);
        $('#brandfrm [name="type"]').val('update');
        $('#brandmodal .modal-title').html('Edit Brand');
        $('#brandmodal').modal('show');
    });

    // Delete Category or Brand
    $('.remove_cat, .remove_brand').click(function () {
        if (confirm('Are you sure to delete this data?')) {
            start_load();

            let data = {
                type: 'delete',
                id: $(this).attr('data-id')
            };
            if ($(this).hasClass('remove_cat'))
                data.cat = 1;
            else if ($(this).hasClass('remove_brand'))
                data.brand = 1;
            $.ajax({
                url: 'maintenance_operations.php',
                type: 'POST',
                data: data,
                success: function (resp) {
                    if (resp == 1) {
                        alert('Data successfully deleted.');
                        location.reload();
                    }
                    end_load();

                },
                error:function (xhr){
                    alert("error")
                    end_load();
                }
            });
        }
    });
</script>
