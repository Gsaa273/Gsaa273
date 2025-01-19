<?php
// Check if an action is requested and handle the delete operation
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $order_id = $_GET['order_id'];
    // Delete query to remove the order
    mysqli_query($con, "DELETE FROM orders WHERE order_id='$order_id'")
    or die("Delete query failed...");
    echo "<script>alert('Order deleted successfully'); location.href='index.php?page=orders';</script>";
}

// Pagination logic to determine the starting record for the current page
$page = $_GET['page'];
$page1 = ($page == "" || $page == "1") ? 0 : (intval($page) * 10) - 10;
?>
<!-- Orders Table -->
<div class="content">
    <div class="container-fluid">
        <div class="col-md-14">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">Orders / Page <?php echo $page; ?> </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive ps">
                        <table class="table table-hover table-striped" id="ordertbl">
                            <thead>
                            <tr>
                                <th>Ref</th>
                                <th>Order</th>
                                <th>Customer Info</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            // Query to fetch orders and related data
                            $result = mysqli_query($con, "SELECT * FROM orders o INNER JOIN orders_info oi ON oi.order_id = o.order_id")
                            or die("Query failed...");
                            while ($row = mysqli_fetch_array($result)) {
                                $prod = mysqli_query($con, "SELECT * FROM order_products op INNER JOIN products p ON op.product_id = p.product_id WHERE op.order_id = " . $row['order_id']);
                                ?>
                                <tr>
                                    <td><?php echo $row['ref_id']; ?></td>
                                    <td>
                                        <a data-toggle="collapse" href="#prod<?php echo $row['order_id']; ?>" role="button">
                                            Orders <i class="fa fa-angle-down"></i>
                                        </a>
                                        <div class="collapse" id="prod<?php echo $row['order_id']; ?>">
                                            <?php while ($prow = mysqli_fetch_assoc($prod)) { ?>
                                                <small>
                                                    <p><b>Product:</b> <?php echo $prow['product_title']; ?></p>
                                                    <p><b>Price:</b> <?php echo $prow['product_price']; ?></p>
                                                    <p><b>Qty:</b> <?php echo $prow['qty']; ?></p>
                                                    <p><b>Total:</b> <?php echo $prow['amt']; ?></p>
                                                </small>
                                                <hr>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <p><b>Name:</b> <?php echo ucwords($row['f_name']); ?></p>
                                        <p><b>Address:</b> <?php echo $row['address']; ?></p>
                                        <p><b>Email:</b> <?php echo $row['email']; ?></p>
                                    </td>
                                    <td>
                                        <?php
                                        $status_badges = [
                                            0 => "badge badge-danger",
                                            1 => "badge badge-info",
                                            2 => "badge badge-warning",
                                            3 => "badge badge-success"
                                        ];
                                        $status_text = [
                                            0 => "Cancelled",
                                            1 => "Pending",
                                            2 => "Shipped",
                                            3 => "Delivered"
                                        ];
                                        echo "<div class='{$status_badges[$row['status']]}'>{$status_text[$row['status']]}</div>";
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 1): ?>
                                            <button class="btn btn-sm btn-primary changestatus" data-stat="2" data-id="<?php echo $row['order_id']; ?>">Mark as Shipped</button>
                                        <?php elseif ($row['status'] == 2): ?>
                                            <button class="btn btn-sm btn-primary changestatus" data-stat="3" data-id="<?php echo $row['order_id']; ?>">Mark as Delivered</button>
                                        <?php elseif ($row['status'] == 3): ?>
                                            <div class="badge badge-success" disabled>Delivered</div>
                                            <a class="btn btn-sm btn-danger" href="index.php?page=orders&action=delete&order_id=<?php echo $row['order_id']; ?>">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Initialize DataTable and handle status changes
    $('#ordertbl').dataTable();
    $('.changestatus').click(function() {
        if (confirm("Are you sure to change the status of this order?")) {
            start_load();
            $.ajax({
                url: 'orederstatsupdate.php',
                method: "POST",
                data: { status: $(this).data('stat'), id: $(this).data('id') },
                success: function(resp) {
                    if (resp == 1) {
                        alert("Order updated successfully.");
                        location.reload();
                    }
                }
            });
        }
    });
</script>
