<?php

//declare db server variables
$servername = "localhost";
$username = "root";
$password = 'E$DNY4366';
$dbname = "StatusBoard";

//variable for defining reload waiting time
$reloadDelay = 0;

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//Do things with the buttons
if (array_key_exists('closeButton', $_POST)) {
    header('Refresh: 0; URL=http://statusboard.esdny.com');
}
if (array_key_exists('updateStatus', $_POST)) {
    //parse the first name last name and rate/rank of member to be removed
    $update_names = $_POST['updateName'];
    foreach ($update_names as $selected_name) {
        $update_lastName = mysqli_real_escape_string($conn, strstr($selected_name, ",", true));
        
        $update_firstName = mysqli_real_escape_string($conn, strstr(str_replace($update_lastName . ", ", "", $selected_name), ",", true));
        
        $update_rankRate = mysqli_real_escape_string($conn, substr($selected_name, strrpos($selected_name, ', ') + 2));
        //get the new stuff
        $new_location = mysqli_real_escape_string($conn, $_POST["newLocation"]);
        $raw_duty = mysqli_real_escape_string($conn, $_POST["newDuty"]);
        $new_duty;
        if ($raw_duty == "On Duty") {
            $new_duty = 1;
            $removeOldDutyQuery = "UPDATE members SET duty='0' WHERE duty='1' AND rate_rank LIKE '" . substr($update_rankRate, 0, 2) . "%'";
            if (mysqli_query($conn, $removeOldDutyQuery)) {
                echo "<h4>Old duty removed successfully.</h4>";
            } else {
                echo "ERROR: Was not able to execute $removeOldDutyQuery. " . mysqli_error($conn);
                mysqli_close($conn);
            }
        } else {
            $new_duty = 0;
        }
        $raw_leave = mysqli_real_escape_string($conn, $_POST["newLeave"]);
        $new_leave;
        if ($raw_leave == "On Leave") {
            $new_leave = 1;
        } else {
            $new_leave = 0;
        }
        $raw_orders = mysqli_real_escape_string($conn, $_POST["newOrders"]);
        $new_orders;
        if ($raw_orders == "On Orders") {
            $new_orders = 1;
        } else {
            $new_orders = 0;
        }
        //begin the query string
        $oneBigQuery = "UPDATE members SET ";
        //if location is not blank, add it to the big query
        if ($new_location != "") {
            //add to the sql query
            $oneBigQuery .= "location='$new_location', ";
            echo "<h4>Attempting Location Change.</h4>";
        }
        //if duty is not blank, add it to the big query
        if ($raw_duty != "") {
            //add to the sql query
            $oneBigQuery .= "duty='$new_duty', ";
            echo "<h4>Attempting Duty Status Change.</h4>";
        }
        //if leave is not blank, add it to the big query
        if ($raw_leave != "") {
            //add to the sql query
            $oneBigQuery .= "leave_status='$new_leave', ";
            echo "<h4>Attempting Leave Status Change.</h4>";
        }
        //if orders is not blank, add it to the big query
        if ($raw_orders != "") {
            //add to the sql query
            $oneBigQuery .= "on_orders='$new_orders', ";
            echo "<h4>Attempting Orders Status Change.</h4>";
        }
        //trim any extra commmas out
        $oneBigQuery = rtrim($oneBigQuery, ", ");
        //re-add a space at the end
        $oneBigQuery .= " ";
        //finish the query string
        $oneBigQuery .= "WHERE first_name='$update_firstName' AND last_name='$update_lastName' AND rate_rank='$update_rankRate'";
        //echo "<h4>The final product</h4>";
        //echo $oneBigQuery;
        //do the concatenated query only if there are changes to be made
        if ($new_location == "" && $raw_duty == "" && $raw_leave == "" && $raw_orders == "") {
            echo "<h4>Nothing Changed.</h4>";
            mysqli_close($conn);
            echo "Page will refresh in $reloadDelay seconds.";
            header("Refresh: $reloadDelay; URL=http://statusboard.esdny.com");
        } else {
            if (mysqli_query($conn, $oneBigQuery)) {
                echo "<h4>All changes made successfully.</h4>";
            } else {
                echo "ERROR: Was not able to execute $oneBigQuery. " . mysqli_error($conn);
                mysqli_close($conn);
            }
        }
    }
    mysqli_close($conn);
    echo "Page will refresh in $reloadDelay seconds.";
    header("Refresh: $reloadDelay; URL=http://statusboard.esdny.com");
}
if (array_key_exists('updateMember', $_POST)) {
    //parse the first name last name and rate/rank of member to be removed
    $update_name = $_POST["updateName"];
    $update_lastName = mysqli_real_escape_string($conn, strstr($update_name, ",", true));
    $update_firstName = mysqli_real_escape_string($conn, strstr(str_replace($update_lastName . ", ", "", $update_name), ",", true));
    $update_rankRate = mysqli_real_escape_string($conn, substr($update_name, strrpos($update_name, ', ') + 2));
    $new_firstName = mysqli_real_escape_string($conn, $_POST["newFirstName"]);
    $new_lastName = mysqli_real_escape_string($conn, $_POST["newLastName"]);
    $new_rateRank = mysqli_real_escape_string($conn, $_POST["newRateRank"]);
    //begin the query string
    $oneBigQuery = "UPDATE members SET ";
    //if first name is not blank, add it to the big query
    if ($new_firstName != "") {
        //create the sql query
        $oneBigQuery .= "first_name='$new_firstName', ";
        echo "<h4>Attempting First Name Change.</h4>";
    }
    //if last name is not blank, add it to the big query
    if ($new_lastName != "") {
        //create the sql query add add it to the big one
        $oneBigQuery .= "last_name='$new_lastName', ";
        echo "<h4>Attempting Last Name Change.</h4>";
    }
    //if rateRank is not blank, add it to the big query
    if ($new_rateRank != "") {
        //create the sql query
        $oneBigQuery .= "rate_rank='$new_rateRank', ";
        echo "<h4>Attempting Rate/Rank Change.</h4>";
    }
    //trim any extra commmas out
    $oneBigQuery = rtrim($oneBigQuery, ", ");
    //re-add a space at the end
    $oneBigQuery .= " ";
    //finish the query string
    $oneBigQuery .= "WHERE first_name='$update_firstName' AND last_name='$update_lastName' AND rate_rank='$update_rankRate'";
    //echo "<h4>The final product</h4>";
    //echo $oneBigQuery;
    //do the concatenated query only if there are changes to be made
    if ($new_firstName == "" && $new_lastName == "" && $new_rateRank == "") {
        echo "<h4>Nothing Changed.</h4>";
        mysqli_close($conn);
        echo "Page will refresh in $reloadDelay seconds.";
        header("Refresh: $reloadDelay; URL=http://statusboard.esdny.com");
    } else {
        if (mysqli_query($conn, $oneBigQuery)) {
            echo "<h4>All changes made successfully.</h4>";
            mysqli_close($conn);
            echo "Page will refresh in $reloadDelay seconds.";
            header("Refresh: $reloadDelay; URL=http://statusboard.esdny.com");
        } else {
            echo "ERROR: Was not able to execute $oneBigQuery. " . mysqli_error($conn);
            mysqli_close($conn);
        }
    }
}
if (array_key_exists('addMember', $_POST)) {
    //declare and set variables
    $rate_rank = mysqli_real_escape_string($conn, $_POST["rateRank"]);
    $first_name = mysqli_real_escape_string($conn, $_POST["firstName"]);
    $last_name = mysqli_real_escape_string($conn, $_POST["lastName"]);
    $leave = mysqli_real_escape_string($conn, 0);
    $duty = mysqli_real_escape_string($conn, 0);
    $on_orders = mysqli_real_escape_string($conn, 0);
    $location = mysqli_real_escape_string($conn, "Select *Update Status* to set a location...");
    $recruitment_type = mysqli_real_escape_string($conn, $_POST["recruitmentType"]);
    //create query
    $addMemberQuery = "INSERT INTO members (rate_rank, first_name, last_name, leave_status, duty, on_orders, location, recruitment_type)
                                    VALUES ('$rate_rank', '$first_name', '$last_name','$leave', '$duty', '$on_orders', '$location', '$recruitment_type')";
    //do the mySql thing
    if (mysqli_query($conn, $addMemberQuery)) {
        echo "<h4>Member added successfully.</h4>";
        echo "Page will refresh in $reloadDelay seconds.";
        mysqli_close($conn);
        header("Refresh: $reloadDelay; URL=http://statusboard.esdny.com");
    } else {
        echo "ERROR: Was not able to execute $addMemberQuery. " . mysqli_error($conn);
        mysqli_close($conn);
    }
}
if (array_key_exists('removeMember', $_POST)) {
    //parse the first name last name and rate/rank of member to be removed
    $remove_name = $_POST["removeName"];
    $remove_lastName = mysqli_real_escape_string($conn, strstr($remove_name, ",", true));
    $remove_firstName = mysqli_real_escape_string($conn, strstr(str_replace($remove_lastName . ", ", "", $remove_name), ",", true));
    $remove_rateRank = mysqli_real_escape_string($conn, substr($remove_name, strrpos($remove_name, ', ') + 2));
    //create the sql query
    $removeMemberQuery = "DELETE FROM members WHERE first_name='$remove_firstName' AND last_name='$remove_lastName' AND rate_rank='$remove_rateRank'";
    //do the mySql thing
    if (mysqli_query($conn, $removeMemberQuery)) {
        echo "<h4>Member removed successfully.</h4>";
        echo "Page will refresh in $reloadDelay seconds.";
        mysqli_close($conn);
        header("Refresh: $reloadDelay; URL=http://statusboard.esdny.com");
    } else {
        echo "ERROR: Was not able to execute $removeMemberQuery. " . mysqli_error($conn);
        mysqli_close($conn);
    }
}
if (array_key_exists('addProject', $_POST)) {
    //declare and set variables
    $project_date = mysqli_real_escape_string($conn, $_POST["projectDate"]);
    $project_description = mysqli_real_escape_string($conn, $_POST["projectDescription"]);
    $project_rate = mysqli_real_escape_string($conn, $_POST["projectRate"]);
    //create query
    $addProjectQuery = "INSERT INTO projects (due_date, title_description, rate) "
            . "VALUES ('$project_date', '$project_description', '$project_rate')";
    //do the mySql thing
    if (mysqli_query($conn, $addProjectQuery)) {
        echo "<h4>Project added successfully.</h4>";
        echo "Page will refresh in $reloadDelay seconds.";
        mysqli_close($conn);
        header("Refresh: $reloadDelay; URL=http://statusboard.esdny.com");
    } else {
        echo "ERROR: Was not able to execute $addProjectQuery. " . mysqli_error($conn);
        mysqli_close($conn);
    }
}
if (array_key_exists('removeProject', $_POST)) {
    //parse rate, date, and desc of member to be removed
    $remove_summary = $_POST["projectName"];
    $remove_rate = mysqli_real_escape_string($conn, strstr($remove_summary, ",", true));
    $remove_date = mysqli_real_escape_string($conn, strstr(str_replace($remove_rate . ", ", "", $remove_summary), ",", true));
    $remove_desc = mysqli_real_escape_string($conn, substr($remove_summary, strlen("$remove_rate" . ", " . "$remove_date" . ", ") + 0));
    //create the sql query
    $removeProjectQuery = ""
            . "DELETE FROM projects "
            . "WHERE due_date='$remove_date' "
            . "AND rate='$remove_rate' "
            . "AND title_description LIKE '$remove_desc%'";
    //do the mySql thing

    if (mysqli_query($conn, $removeProjectQuery)) {
        echo "<h4>Project removed successfully.</h4>";
        echo "Page will refresh in $reloadDelay seconds.";
        mysqli_close($conn);
        header("Refresh: $reloadDelay; URL=http://statusboard.esdny.com");
    } else {
        echo "ERROR: Was not able to execute $removeProjectQuery. " . mysqli_error($conn);
        mysqli_close($conn);
    }
}
if (array_key_exists('updateProject', $_POST)) {
    //parse rate, date, and desc of member to be removed
    $update_summary = $_POST["projectName"];
    $update_rate = mysqli_real_escape_string($conn, strstr($update_summary, ",", true));
    $update_date = mysqli_real_escape_string($conn, strstr(str_replace($update_rate . ", ", "", $update_summary), ",", true));
    $update_desc = mysqli_real_escape_string($conn, substr($update_summary, strlen("$update_rate" . ", " . "$update_date" . ", ") + 0));
    $new_rate = mysqli_real_escape_string($conn, $_POST["newProjectRate"]);
    $new_date = mysqli_real_escape_string($conn, $_POST["newProjectDate"]);
    $new_desc = mysqli_real_escape_string($conn, $_POST["newProjectDescription"]);
    //begin the query string
    $oneBigQuery = "UPDATE projects SET ";
    //if first name is not blank, add it to the big query
    if ($new_date != "") {
        //create the sql query
        $oneBigQuery .= "due_date='$new_date', ";
        echo "<h4>Attempting Date Change.</h4>";
    }
    //if last name is not blank, add it to the big query
    if ($new_desc != "") {
        //create the sql query add add it to the big one
        $oneBigQuery .= "title_description='$new_desc', ";
        echo "<h4>Attempting Description Change.</h4>";
    }
    //if rateRank is not blank, add it to the big query
    if ($new_rate != "") {
        //create the sql query
        $oneBigQuery .= "rate='$new_rate', ";
        echo "<h4>Attempting Rate Change.</h4>";
    }
    //trim any extra commmas out
    $oneBigQuery = rtrim($oneBigQuery, ", ");
    //re-add a space at the end
    $oneBigQuery .= " ";
    //finish the query string
    $oneBigQuery .= "WHERE due_date='$update_date' AND title_description LIKE '$update_desc%' AND rate='$update_rate'";
    //echo "<h4>The final product</h4>";
    //echo $oneBigQuery;
    //do the concatenated query only if there are changes to be made
    if ($new_date == "" && $new_desc == "" && $new_rate == "") {
        echo "<h4>Nothing Changed.</h4>";
        mysqli_close($conn);
        echo "Page will refresh in $reloadDelay seconds.";
        header("Refresh: $reloadDelay; URL=http://statusboard.esdny.com");
    } else {
        if (mysqli_query($conn, $oneBigQuery)) {
            echo "<h4>All changes made successfully.</h4>";
            mysqli_close($conn);
            echo "Page will refresh in $reloadDelay seconds.";
            header("Refresh: $reloadDelay; URL=http://statusboard.esdny.com");
        } else {
            echo "ERROR: Was not able to execute $oneBigQuery. " . mysqli_error($conn);
            mysqli_close($conn);
        }
    }
}
