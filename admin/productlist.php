<div class="col-md-14">
    <div class="card">
        <div class="card-header card-header-primary">
            <h4 class="card-title">Products List</h4>
        </div>
        <div class="card-body">
            <!-- Button to add a new product -->
            <div class="col-md-2 offset-md-10">
                <a class="btn btn-primary" href="index.php?page=addproduct">Add New</a>
            </div>
            <br>
            <div class="table-responsive ps">
                <table class="table table-striped" id="prod">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Fetch all products from the database
                    $result = mysqli_query($con, "SELECT product_id, product_image, product_title, product_price FROM products ORDER BY product_title ASC");

                    while (list($product_id, $image, $product_name, $price) = mysqli_fetch_array($result)) {
                        $image = CloudServices::S3_BUCKET_IMAGES_URL . $image; // Get product image URL from S3
                        echo "<tr>
                                <td><img src='$image' style='width:50px; height:50px;'></td>
                                <td>$product_name</td>
                                <td>$price</td>
                                <td class='text-center'>
                                    <a class='btn btn-primary btn-sm' href='index.php?page=addproduct&id=$product_id'>Edit</a>
                                    <a class='btn btn-danger btn-sm delete-product' data-id='$product_id' href='javascript:void(0)'>Delete</a>
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

<script>
    // Initialize DataTable for products
    $('#prod').dataTable();

    // Handle product deletion
    $('.delete-product').on('click', function () {
        if (!confirm('Are You Sure You Want to Delete This Product?')) return;

        start_load(); // Show loader
        $.ajax({
            url: 'save_prod.php?product_id=' + this.dataset.id + '&action=delete',
            success: function (resp) {
                if (resp == 1) {
                    alert('Product Deleted Successfully.');
                    location.reload(); // Reload the page after deletion
                }
                end_load(); // Hide loader
            },
            error: function (xhr, status, message) {
                console.error(xhr);
                alert('Error: ' + xhr.responseJson.message);
            }
        });
    });
</script>
