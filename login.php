<?php
include "db.php";
session_start();

if (isset($_POST["email"]) && isset($_POST["password"])) {
    try {
        $email = mysqli_real_escape_string($con, $_POST["email"]); // Secure email input
        $password = $_POST["password"]; // Capture password input

        // **Authenticate user using AWS Cognito**:
        // This utilizes AWS Cognito through the `CloudServices` class, which integrates
        // the Cognito Identity Provider for managing user authentication.
        $authRes = CloudServices::loginUser($email, $password);

        if (isset($authRes['AuthenticationResult'])) {
            // If Cognito authentication is successful, verify user in the database.
            $sql = "SELECT * FROM user_info WHERE email = '$email' AND password = '$password'";
            $run_query = mysqli_query($con, $sql);
            $count = mysqli_num_rows($run_query);

            if ($count > 0) {
                $row = mysqli_fetch_array($run_query);

                // Store user details in the session for application use
                $_SESSION["uid"] = $row["user_id"];
                $_SESSION["name"] = $row["first_name"];

                // Link any cart items associated with this user's IP to their account
                $uid = $row["user_id"];
                $ip_add = getenv("REMOTE_ADDR");
                $sql = "UPDATE `cart` SET user_id = '$uid' WHERE ip_add = '$ip_add' AND user_id = -1";
                mysqli_query($con, $sql);

                echo "login_success";
            } else {
                echo "Incorrect credentials.";
            }
        } else {
            // Handle errors from Cognito response
            echo implode(',', $authRes);
        }
    } catch (\Aws\Exception\AwsException $e) {
        // **Handle AWS Cognito Exceptions**:
        // If the user is unconfirmed, send a confirmation code via AWS Cognito.
        if ($e->getAwsErrorCode() === 'UserNotConfirmedException') {
            try {
                CloudServices::sendConfirmCode($email); // Resend confirmation code
                $_SESSION["email"] = $email; // Store email in session for verification flow
                echo json_encode(['confirm' => 1, 'email' => $email, 'message' => $e->getAwsErrorMessage()]);
            } catch (\Aws\Exception\AwsException $ee) {
                echo "<div class='alert alert-warning'>AWS Error: " . $ee->getAwsErrorMessage() . "</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>AWS Error: " . $e->getAwsErrorMessage() . "</div>";
        }
    } catch (Exception $e) {
        // Handle generic exceptions
        echo "<div class='alert alert-warning'>Error: " . $e->getMessage() . "</div>";
    }
    exit();
}
?>
