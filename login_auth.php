<?php
include 'dbcon.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $email = isset($data['email']) ? mysqli_real_escape_string($conn, $data['email']) : '';
    $password = isset($data['password']) ? $data['password'] : '';
    $user_id = isset($data['user_id']) ? mysqli_real_escape_string($conn, $data['user_id']) : ''; 
    $user_id = isset($data['user_fullname']) ? mysqli_real_escape_string($conn, $data['user_fullname']) : ''; 

    if (!empty($email) && !empty($password)) {
        // Query to find the user by email
        $sql = "SELECT user_id, user_fullname, user_password FROM user WHERE user_email = '$email'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $hashedPassword = $user['user_password'];
            $userId = $user['user_id']; // Retrieve the user_id from the database
            $userFullname = $user['user_fullname']; // Retrieve the user_fullname

            // Verify the password
            if (password_verify($password, $hashedPassword)) {
                $response = array(
                    'status' => 'success',
                    'message' => 'Login successful',
                    'user_id' => $userId,
                    'user_fullname' => $userFullname // Include user_fullname in the response
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => 'Invalid password'
                );
            }
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Email not found'
            );
        }
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Please provide both email and password'
        );
    }

    mysqli_close($conn);

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Invalid request method'
    );

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
