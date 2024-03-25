<?php

// Database connection
include __DIR__ . '/../config/db.php';

// Swift Mailer lib
require_once __DIR__ . '/../vendor/autoload.php';

// Error & success messages
$email_exist = $email_verify_err = $email_verify_success = '';
$fNameEmptyErr = $lNameEmptyErr = $emailEmptyErr = $mobileEmptyErr = $passwordEmptyErr = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $mobilenumber = $_POST["mobilenumber"];
    $password = $_POST["password"];

    // Check if email already exists
    $email_check_query = mysqli_query($connection, "SELECT * FROM users WHERE email = '{$email}'");
    $rowCount = mysqli_num_rows($email_check_query);

    // PHP validation
    if (!empty($firstname) && !empty($lastname) && !empty($email) && !empty($mobilenumber) && !empty($password)) {
        if ($rowCount > 0) {
            $email_exist = '<div class="alert alert-danger" role="alert">
                        <strong>Error!</strong> User with email already exists!
                    </div>';
        } else {
            // Clean form data before inserting into the database
            $firstname = mysqli_real_escape_string($connection, $firstname);
            $lastname = mysqli_real_escape_string($connection, $lastname);
            $email = mysqli_real_escape_string($connection, $email);
            $mobilenumber = mysqli_real_escape_string($connection, $mobilenumber);
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Insert user data into the database
            $sql = "INSERT INTO users (firstname, lastname, email, mobilenumber, password) 
                    VALUES ('{$firstname}', '{$lastname}', '{$email}', '{$mobilenumber}', '{$password_hash}')";

            if (mysqli_query($connection, $sql)) {
                $email_verify_success = '<div class="alert alert-success">
                            <strong>Success!</strong> Verification email has been sent!
                        </div>';

                // Send verification email
                $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
                    ->setUsername('jykebajande@gmail.com')
                    ->setPassword('09126312418011103');

                $mailer = new Swift_Mailer($transport);
                $message = (new Swift_Message('Please Verify Email Address!'))
                    ->setFrom([$email => $firstname . ' ' . $lastname])
                    ->setTo($email)
                    ->setBody('Hello! User');

                $result = $mailer->send($message);

                if (!$result) {
                    $email_verify_err = '<div class="alert alert-danger">
                                <strong>Error!</strong> Verification email could not be sent!
                            </div>';
                }
            } else {
                die('<div class="alert alert-danger">
                            <strong>Error!</strong> MySQL query failed!' . mysqli_error($connection) . '
                        </div>');
            }
        }
    } else {
        // Handle empty form fields
        if (empty($firstname)) {
            $fNameEmptyErr = '<div class="alert alert-danger">
                    <strong>Error!</strong> First name cannot be blank.
                </div>';
        }
        if (empty($lastname)) {
            $lNameEmptyErr = '<div class="alert alert-danger">
                    <strong>Error!</strong> Last name cannot be blank.
                </div>';
        }
        if (empty($email)) {
            $emailEmptyErr = '<div class="alert alert-danger">
                    <strong>Error!</strong> Email cannot be blank.
                </div>';
        }
        if (empty($mobilenumber)) {
            $mobileEmptyErr = '<div class="alert alert-danger">
                    <strong>Error!</strong> Mobile number cannot be blank.
                </div>';
        }
        if (empty($password)) {
            $passwordEmptyErr = '<div class="alert alert-danger">
                    <strong>Error!</strong> Password cannot be blank.
                </div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP User Registration System Example</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center">Register</h3>
                        <form id="registrationForm" method="post" action="controllers/register.php"> <!-- Update action attribute here -->
                            <?php echo $email_exist; ?>
                            <?php echo $email_verify_err; ?>
                            <?php echo $email_verify_success; ?>
                            <?php echo $fNameEmptyErr; ?>
                            <?php echo $lNameEmptyErr; ?>
                            <?php echo $emailEmptyErr; ?>
                            <?php echo $mobileEmptyErr; ?>
                            <?php echo $passwordEmptyErr; ?>
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstname" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastname" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="mobileNumber">Mobile Number</label>
                                <input type="tel" class="form-control" id="mobileNumber" name="mobilenumber" pattern="[0-9]{10}" required>
                                <small class="form-text text-muted">Please enter a 10-digit mobile number.</small>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>
