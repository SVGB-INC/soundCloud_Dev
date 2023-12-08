<?php
function check_login($pdo)
{

    if (isset($_SESSION['user_name']))
    {
        try
        {

            $username = $_SESSION['user_name'];
            $select_stmt = $pdo->prepare("SELECT * FROM reg_data_bank WHERE user_name=:uname limit 1;");
            $select_stmt->execute(array(
                ':uname' => $username
            ));
            $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

            if ($select_stmt->rowCount() > 0)
            {
                
                return $row;
            }
            else
            {
                //redirect to login page if the above code is unsuccessful
                header("Location: sign-in.php");
                die;
            }
        }
        catch(PDOException $e)
        {
            $e->getMessage();
        }

    }
    else
    {
        //redirect to login page if the above code is unsuccessful
        header("Location: sign-in.php");
        die;
    }
}

function activePodcast($pdo)
{
    if (isset($_SESSION['user_name']))
    {
        try
        {

            $username = $_SESSION['user_name'];
            $select_stmt = $pdo->prepare("SELECT podcast_address FROM current_podcast WHERE user_name=:uname and isActive = 'true' limit 1;");
            $select_stmt->execute(array(
                ':uname' => $username
            ));
            $row = $select_stmt->fetchColumn();

            if ($select_stmt->rowCount() > 0)
            {
                return $row;
            }
            else
            {
                
            }
        }
        catch(PDOException $e)
        {
            $e->getMessage();
        }

    }
    else
    {
        //redirect to login page if the above code is unsuccessful
        header("Location: sign-in.php");
        die;
    }
}

//$query = "select * from reg_data_bank where user_name = '$username' limit 1;";
//$result = mysqli_query($con,$query);
// if($result && mysqli_num_rows($result) > 0)
// {
//     $user_data = mysqli_fetch_assoc($result);
//     $user_data["password"] = NULL;
//     return $user_data;
// }

?>
