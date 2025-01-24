<?php
include 'db.php';

if (isset($_POST['code'])) {
    $email = $_POST['email'];
    $code = $_POST['code'];

    try {
        // **Confirm user in AWS Cognito**:
        // This verifies the user's account using the confirmation code through
        // AWS Cognito. The `CloudServices` class handles the Cognito API request.
        CloudServices::confirmUser(email: $email, code: $code);

        // Retrieve user details from the local database and store them in the session
        $sql = "SELECT user_id, first_name FROM user_info WHERE email='$email'";
        $res = mysqli_query($con, $sql)->fetch_array();

        $_SESSION["uid"] = $res['user_id'];
        $_SESSION["f_name"] = $res['first_name'];

        echo 'success';
    } catch (Throwable $e) {
        echo "<div class='alert alert-warning'>AWS Error: " . $e->getMessage() . "</div>";

    }
    exit();
}
?>
<div class="container-fluid">
    <div class="login-marg">
        <form onsubmit="return false" id="confirm_code" class="login100-form ">
            <div class="section-title">
                <h2 class="login100-form-title p-b-49">Confirm Your Email</h2>
            </div>
            <input  type="hidden" name="email" value="<?php echo $_GET['email'];?>">

            <div class="form-group">
                <label for="code">Code</label>
                <input class="input input-borders" type="text" name="code" placeholder="#####" id="code"
                       required>
            </div>

            <input class="primary-btn btn-block" type="submit" value="Verify">

        </form>

    </div>
</div>
