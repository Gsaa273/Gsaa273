<?php
// Fetch product details if editing an existing product
if (isset($_GET['id'])) {
    $qry = mysqli_query($con, "SELECT * FROM products WHERE product_id = " . $_GET['id']);
    foreach (mysqli_fetch_array($qry) as $key => $val) {
        if ($key == 'product_image') {
            $val = CloudServices::S3_BUCKET_IMAGES_URL . $val;
        }
        $meta[$key] = $val;
    }
}
?>

<!-- Product Management Form -->
<form action="" id="manage-prod">
    <div class="row">
        <!-- Product Information -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h5 class="title">Add Product</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Product Name -->
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="hidden" name="product_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>" class="form-control">
                                <input type="text" id="product_name" name="product_name" required value="<?php echo isset($meta['product_title']) ? $meta['product_title'] : ''; ?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Product Image -->
                            <div>
                                <img src="<?php echo isset($meta['product_image']) ? $meta['product_image'] : ''; ?>" alt="" class="img-field" width="75" height="100">
                                <label>Product Image</label>
                                <input type="file" name="picture" class="btn btn-fill" id="picture" onchange="displayImg(this, $(this))" <?php echo !isset($meta['product_image']) ? 'required' : ''; ?>>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <!-- Product Description -->
                            <div class="form-group">
                                <label>Description</label>
                                <textarea rows="4" cols="80" id="details" name="details" required class="form-control"><?php echo isset($meta['product_desc']) ? $meta['product_desc'] : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <!-- Product Price -->
                            <div class="form-group">
                                <label>Pricing</label>
                                <input type="text" id="price" name="price" required value="<?php echo isset($meta['product_price']) ? $meta['product_price'] : ''; ?>" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Categories and Brands -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h5 class="title">Categories</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <!-- Product Category -->
                        <label>Product Category</label>
                        <select name="category_id" id="category_id" class="custom-select select2">
                            <option value=""></option>
                            <?php
                            $cat = mysqli_query($con, "SELECT * FROM categories");
                            while ($row = mysqli_fetch_assoc($cat)) {
                                $selected = isset($meta['product_cat']) && $meta['product_cat'] == $row['cat_id'] ? 'selected' : '';
                                echo "<option value='{$row['cat_id']}' $selected>{$row['cat_title']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <!-- Product Brand -->
                        <label>Product Brand</label>
                        <select name="brand_id" id="brand_id" class="custom-select select2">
                            <option value=""></option>
                            <?php
                            $brand = mysqli_query($con, "SELECT * FROM brands");
                            while ($row = mysqli_fetch_assoc($brand)) {
                                $selected = isset($meta['product_brand']) && $meta['product_brand'] == $row['brand_id'] ? 'selected' : '';
                                echo "<option value='{$row['brand_id']}' $selected>{$row['brand_title']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product Keywords</label>
                        <input type="text" id="tags" name="tags" required class="form-control"
                               value="<?php echo isset($meta['product_keywords']) ? $meta['product_keywords'] : '' ?>">
                    </div>
                </div>
                <div class="card-footer">
                    <!-- Save Button -->
                    <button type="submit" id="btn_save" name="btn_save" class="btn btn-fill btn-primary">Save Product</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    // Enable Select2 for dropdowns
    $('.select2').select2({ placeholder: "Please select here", width: '100%' });

    // Handle product form submission
    $('#manage-prod').submit(function (e) {
        e.preventDefault();
        start_load();
        $.ajax({
            url: 'save_prod.php',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function (resp) {
                if (resp == 1) {
                    alert("Data successfully saved.");
                    location.href='index.php?page=productlist';
                }else
                end_load();
            }
        });
    });

    // Preview uploaded product image
    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                _this.parent().find('.img-field').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
