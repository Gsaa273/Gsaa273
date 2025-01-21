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
        echo 'error: ' . $e->getMessage();
    }
    exit();
}
?>
