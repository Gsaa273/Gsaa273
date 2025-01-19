<?php


include "header.php";
?>
<div class="main main-raised">
    <div id="hot-deal" class="section mainn mainn-raised">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="hot-deal">
                        <a class="primary-btn cta-btn" href="store.php">Shop now</a>
                    </div>
                </div>
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>

    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row">
                <!-- section title -->
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">Products</h3>
                    </div>
                </div>
                <!-- /section title -->

                <!-- Products tab & slick -->
                <div class="col-md-12 mainn mainn-raised">
                    <div class="row">
                        <div class="products-tabs">
                            <!-- tab -->
                            <div id="tab2" class="tab-pane fade in active">
                                <div class="products-slick" data-nav="#slick-nav-2">
                                    <!-- product -->
                                    <?php
                                    include 'db.php';

                                    $product_query = "SELECT * FROM products,categories WHERE product_cat=cat_id  order by RAND()";
                                    $run_query = mysqli_query($con, $product_query);
                                    if (mysqli_num_rows($run_query) > 0) {

                                        while ($row = mysqli_fetch_array($run_query)) {
                                            $pro_id = $row['product_id'];
                                            $pro_cat = $row['product_cat'];
                                            $pro_brand = $row['product_brand'];
                                            $pro_title = $row['product_title'];
                                            $pro_price = $row['product_price'];
                                            $pro_image = CloudServices::S3_BUCKET_IMAGES_URL.$row['product_image'];

                                            $cat_name = $row["cat_title"];

                                            echo "
								<div class='product'>
									<a href='product.php?p=$pro_id'><div class='product-img'>
										<img src='$pro_image' style='max-height: 170px;' alt=''>
									</div></a>
									<div class='product-body'>
										<p class='product-category'>$cat_name</p>
										<h3 class='product-name header-cart-item-name'><a href='product.php?p=$pro_id'>$pro_title</a></h3>
										<h4 class='product-price header-cart-item-info'>$pro_price</h4>
									</div>
									<div class='add-to-cart'>
										<button pid='$pro_id' id='product' class='add-to-cart-btn block2-btn-towishlist' href='#'><i class='fa fa-shopping-cart'></i> add to cart</button>
									</div>
								</div>
                               
							
                        
			";
                                        };

                                    }
                                    ?>

                                    <!-- /product -->
                                </div>
                                <div id="slick-nav-2" class="products-slick-nav"></div>
                            </div>
                            <!-- /tab -->
                        </div>
                    </div>
                </div>
                <!-- /Products tab & slick -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->


</div>

<?php
include "footer.php";
?>
		
		