<?php

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\S3\S3Client;

/**
 * CloudServices Class
 *
 * This class provides utilities for AWS services, including RDS, S3, and Cognito.
 * It encapsulates methods for database connection, user authentication, and
 * cloud storage operations.
 */
class CloudServices
{
    // Constants for AWS service configuration
    const RDS_ENDPOINT = 'cloudshophub.che8a8momo10.eu-north-1.rds.amazonaws.com';
    const RDS_DB = 'cloudshophub';
    const RDS_USER = 'admin';
    const REGIN = 'eu-north-1';
    const S3_BUCKET_IMAGES_URL = 'https://cloudshophubbucket.s3.eu-north-1.amazonaws.com/images/';
    const S3_BUCKET = 'cloudshophubbucket';
    const COGNITO_APP_CLIENT_ID = '6aq1b9hasi9ei88jssbm9no1pv';

    /**
     * Establishes a database connection using AWS RDS credentials.
     *
     * @return mysqli The database connection.
     */
    public static function db_open()
    {
        try {
            $pass = getenv('AWS_RDS_PASSWORD');
            return mysqli_connect(self::RDS_ENDPOINT, self::RDS_USER, $pass, self::RDS_DB);
        }catch (Throwable $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Creates and returns an S3 client instance.
     *
     * @return S3Client The configured S3 client.
     */
    private static function getS3()
    {
        return new S3Client([
            'region' => self::REGIN,
            'version' => 'latest',
            'credentials' => [
                'key' => getenv('AWS_IAM_ACCESS'),
                'secret' => getenv('AWS_IAM_SECRET'),
            ],
            'http' => [
                'verify' => false, // Disable SSL verification for development.
            ],
        ]);
    }

    /**
     * Creates and returns a Cognito Identity Provider client instance.
     *
     * @return CognitoIdentityProviderClient The configured Cognito client.
     */
    private static function getCognito()
    {
        return new CognitoIdentityProviderClient([
            'region' => self::REGIN,
            'version' => 'latest',
            'credentials' => [
                'key' => getenv('AWS_IAM_ACCESS'),
                'secret' => getenv('AWS_IAM_SECRET'),
            ],
            'http' => [
                'verify' => false, // Disable SSL verification for development.
            ],
        ]);
    }

    /**
     * Authenticates a user using Cognito.
     *
     * @param string $email    The user's email.
     * @param string $password The user's password.
     * @return mixed Authentication result.
     */
    public static function loginUser($email, $password)
    {
        $client = self::getCognito();
        $authResult = $client->initiateAuth([
            'AuthFlow' => 'USER_PASSWORD_AUTH',
            'ClientId' => self::COGNITO_APP_CLIENT_ID,
            'AuthParameters' => [
                'USERNAME' => $email,
                'PASSWORD' => $password,
            ],
        ]);
        return $authResult;
    }

    /**
     * Registers a new user with Cognito.
     *
     * @param string $email    The user's email.
     * @param string $password The user's password.
     */
    public static function registerUser($email, $password)
    {
        $client = self::getCognito();
        $result = $client->signUp([
            'ClientId' => self::COGNITO_APP_CLIENT_ID,
            'Username' => $email,
            'Password' => $password,
            'UserAttributes' => [
                [
                    'Name' => 'email',
                    'Value' => $email,
                ],
            ],
        ]);
    }

    /**
     * Confirms a user's registration in Cognito.
     *
     * @param string $email The user's email.
     * @param string $code  The confirmation code.
     * @return string A success message upon successful confirmation.
     */
    public static function confirmUser($email, $code)
    {
        $client = self::getCognito();
        try {
            $result = $client->confirmSignUp([
                'ClientId' => self::COGNITO_APP_CLIENT_ID,
                'Username' => $email,
                'ConfirmationCode' => $code,
            ]);
            return "User registered successfully. Sub: " . $result['UserSub'];
        } catch (Aws\Exception\AwsException $e) {
            throw new Exception('Error Verify User: ' . $e->getAwsErrorMessage());
        }
    }

    /**
     * Resends a confirmation code to the user.
     *
     * @param string $email The user's email.
     * @return bool True if the code was sent successfully.
     */
    public static function sendConfirmCode($email)
    {
        $client = self::getCognito();
        try {
            $client->resendConfirmationCode([
                'ClientId' => self::COGNITO_APP_CLIENT_ID,
                'Username' => $email,
            ]);
            return true;
        } catch (Aws\Exception\AwsException $e) {
            throw new Exception('Error Verify User: ' . $e->getAwsErrorMessage());
        }
    }

    /**
     * Deletes a user from Cognito.
     *
     * @param string $email The user's email.
     * @return bool True if the user was deleted successfully.
     */
    public static function deleteUser($email)
    {
        $client = self::getCognito();
        try {
            $client->deleteUser([
                'ClientId' => self::COGNITO_APP_CLIENT_ID,
                'Username' => $email,
            ]);
            return true;
        } catch (Aws\Exception\AwsException $e) {
            throw new Exception('Delete Error: ' . $e->getAwsErrorMessage());
        }
    }

    /**
     * Uploads an image to an S3 bucket.
     *
     * @param string $file The local file path.
     * @param string $name The file name in S3.
     * @return string The URL of the uploaded file.
     */
    public static function uploadImageToCloud($file, $name)
    {
        $s3 = self::getS3();
        try {
            $result = $s3->putObject([
                'Bucket' => self::S3_BUCKET,
                'Key' => 'images/' . $name,
                'SourceFile' => $file,
            ]);
            return $result['ObjectURL'];
        } catch (Aws\Exception\AwsException $e) {
            throw new Exception('Error uploading file: ' . $e->getAwsErrorMessage());
        }
    }

    /**
     * Deletes an image from an S3 bucket.
     *
     * @param string $name The file name in S3.
     */
    public static function deleteImageFromCloud($name)
    {
        $s3 = self::getS3();
        try {
            $s3->deleteObject([
                'Bucket' => self::S3_BUCKET,
                'Key' => 'images/' . $name,
            ]);
        } catch (Aws\Exception\AwsException $e) {
            throw new Exception('Error deleting file: ' . $e->getAwsErrorMessage());
        }
    }
}
