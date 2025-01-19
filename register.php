<?php
use Aws\Exception\AwsException;

session_start();
include "db.php";

if (isset($_POST["f_name"])) {
    $f_name = $_POST["f_name"];
    $l_name = $_POST["l_name"];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    $mobile = $_POST['mobile'];
    $address1 = $_POST['address1'];

    // Validation patterns
    $emailValidation = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9]+(\.[a-z]{2,4})$/";
    $numberPattern = "/^[0-9]+$/";

    if (empty($f_name) || empty($l_name) || empty($email) || empty($password) || empty($repassword) ||
        empty($mobile) || empty($address1)) {
        echo "<div class='alert alert-warning'>Please fill all fields.</div>";
        exit();
    }

    if (!preg_match($emailValidation, $email)) {
        echo "<div class='alert alert-warning'>Invalid email address.</div>";
        exit();
    }

    if ($password !== $repassword) {
        echo "<div class='alert alert-warning'>Passwords do not match.</div>";
        exit();
    }

    if (!preg_match($numberPattern, $mobile)) {
        echo "<div class='alert alert-warning'>Invalid mobile number.</div>";
        exit();
    }

    $beginTrans = false;
    try {
        mysqli_begin_transaction($con);
        $beginTrans = true;

        // Insert user data into the local database
        $sql = "INSERT INTO `user_info` (`user_id`, `first_name`, `last_name`, `email`, `password`, `mobile`, `address1`) 
                VALUES (NULL, '$f_name', '$l_name', '$email', '$password', '$mobile', '$address1')";
        mysqli_query($con, $sql);
        $id = mysqli_insert_id($con);

        $_SESSION["email"] = $email;
        $ip_add = getenv("REMOTE_ADDR");

        $sql = "UPDATE cart SET user_id = '$id' WHERE ip_add='$ip_add' AND user_id = -1";

        // **Register user in AWS Cognito**:
        // The `CloudServices` class is used here to integrate with AWS Cognito for
        // creating a new user in the Cognito user pool and sending a confirmation code.
        CloudServices::registerUser($email, $password);

        if (mysqli_query($con, $sql) && CloudServices::sendConfirmCode($email)) {
            echo json_encode(['success' => 1, 'confirm' => 1, 'email' => $email]);
            $beginTrans = false;
            mysqli_commit($con);
        } else {
            // Roll back database changes if Cognito actions fail
            $beginTrans = false;
            mysqli_rollback($con);
            CloudServices::deleteUser($email);
            echo "<div class='alert alert-warning'>Registration failed.</div>";
        }
    } catch (AwsException $e) {
        if ($beginTrans) {
            mysqli_rollback($con);
        }
        echo "<div class='alert alert-warning'>AWS Error: " . $e->getAwsErrorMessage() . "</div>";
    } catch (Exception $e) {
        if ($beginTrans) {
            mysqli_rollback($con);
        }
        echo "<div class='alert alert-warning'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
