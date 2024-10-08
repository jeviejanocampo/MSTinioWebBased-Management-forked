<?php
include 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $fullName = isset($data['fullName']) ? mysqli_real_escape_string($conn, $data['fullName']) : '';
    $email = isset($data['email']) ? mysqli_real_escape_string($conn, $data['email']) : '';
    $phoneNumber = isset($data['phoneNumber']) ? mysqli_real_escape_string($conn, $data['phoneNumber']) : '';
    $birthdate = isset($data['birthdate']) ? mysqli_real_escape_string($conn, $data['birthdate']) : '';
    $password = isset($data['password']) ? mysqli_real_escape_string($conn, $data['password']) : '';

    if (!empty($fullName) && !empty($email) && !empty($phoneNumber) && !empty($birthdate) && !empty($password)) {
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO user (user_fullname, user_email, user_number, user_bdate, user_password) 
                VALUES ('$fullName', '$email', '$phoneNumber', '$birthdate', '$hashedPassword')";

        if (mysqli_query($conn, $sql)) {
            $response = array(
                'status' => 'success',
                'message' => 'User successfully added.'
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Error: ' . mysqli_error($conn)
            );
        }
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Please fill in all required fields.'
        );
    }

    mysqli_close($conn);

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Invalid request method.'
    );
    
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
