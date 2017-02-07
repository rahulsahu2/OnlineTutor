
<?php
include('db.php');
/*** begin our session ***/
session_start();
/*** check if the users is already logged in ***/
if(isset( $_SESSION['user_id'] ))
{
    $message = 'User is already logged in';
}
/*** check that both the username, password have been submitted ***/
if(!isset( $_POST['phpro_username'], $_POST['phpro_password']))
{
    $message = 'Please enter a valid username and password';
}
/*** check the username is the correct length ***/
elseif (strlen( $_POST['phpro_username']) > 20 || strlen($_POST['phpro_username']) < 4)
{
    $message = 'Usename must be between 5-20 characters';
}
/*** check the password is the correct length ***/
elseif (strlen( $_POST['phpro_password']) > 20 || strlen($_POST['phpro_password']) < 4)
{
    $message = 'Password must be between 5-20 characters';
}
/*** check the username has only alpha numeric characters ***/
elseif (ctype_alnum($_POST['phpro_username']) != true)
{
    /*** if there is no match ***/
    $message = "Username must be alpha numeric";
}
/*** check the password has only alpha numeric characters ***/
elseif (ctype_alnum($_POST['phpro_password']) != true)
{
        /*** if there is no match ***/
        $message = "Password must be alpha numeric";
}
else
{
    /*** if we are here the data is valid and we can insert it into database ***/
    $phpro_username = filter_var($_POST['phpro_username'], FILTER_SANITIZE_STRING);
    $phpro_password = filter_var($_POST['phpro_password'], FILTER_SANITIZE_STRING);

    /*** now we can encrypt the password ***/
    //$phpro_password = sha1( $phpro_password );
    

    try
    {

        /*** set the error mode to excptions ***/
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        /*** prepare the select statement ***/
        $stmt = $dbh->prepare("SELECT tutor_id, tutor_username, tutor_password FROM tutors 
                    WHERE tutor_username = :phpro_username AND tutor_password = :phpro_password");

        /*** bind the parameters ***/
        $stmt->bindParam(':phpro_username', $phpro_username, PDO::PARAM_STR);
        $stmt->bindParam(':phpro_password', $phpro_password, PDO::PARAM_STR, 40);

        /*** execute the prepared statement ***/
        $stmt->execute();

        /*** check for a result ***/
        $user_id = $stmt->fetchColumn(1);

        /*** if we have no result then fail boat ***/
        if($user_id == false)
        {
                $message = 'Login Failed';
        }
        /*** if we do have a result, all is well ***/
        else
        {
                /*** set the session user_id variable ***/
                $_SESSION['user_id'] = $user_id;

                /*** tell the user we are logged in ***/
                $message = 'You are now logged in';
                header("Location: board_info.php");
                exit;
        }


    }
    catch(Exception $e)
    {
        /*** if we are here, something has gone wrong with the database ***/
        $message = 'We are unable to process your request. Please try again later"';
    }
}
?>

<html>
<head>
<title>PHPRO Login</title>
</head>
<body>
<p><?php echo $message; ?>
</body>
</html>