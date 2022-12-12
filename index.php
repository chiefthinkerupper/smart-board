<?PHP
// underconstruction boolean
$underconstruction = False;
//Include other php files
include "php/insert_update_queries.php";

//Connnect to database
//Declare db server variables
$servername = "localhost";
$username = "root";
$password = 'E$DNY4366';
$dbname = "StatusBoard";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<!doctype PHP>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="A personnel status web page for ESD New York">
        <meta name="author" content="Reed Boomgarden">
        <title>ESD New York - Status Board</title>
        <link rel="icon" href="resources/esdnylogo.png">

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.css" rel="stylesheet">


        <style>
            .bd-placeholder-img {
                font-size: 1.125rem;
                text-anchor: middle;
                -webkit-user-select: none;
                -moz-user-select: none;
                user-select: none;
            }

            @media (min-width: 768px) {
                .bd-placeholder-img-lg {
                    font-size: 3.5rem;
                }
            }
            #overlay {
                position: fixed; /* Sit on top of the page content */
                display: none; /* Hidden by default */
                width: 100%; /* Full width (cover the whole page) */
                height: 100%; /* Full height (cover the whole page) */
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5); /* Black background with opacity */
                z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
                cursor: pointer; /* Add a pointer on hover */
            }
            #overlaytext{
                position: absolute;
                top: 50%;
                left: 50%;
                font-size: 50px;
                color: white;
                transform: translate(-50%,-50%);
                -ms-transform: translate(-50%,-50%);
            }
            tr:active {
                color: black;
                background-color: #e9ecef;
                border-color: #dee2e6;
            }

        </style>

        <!-- Custom styles for this template -->
        <link href="css/offcanvas.css" rel="stylesheet">
        <link href="css/formStyles.css" rel="stylesheet">


    </head>
    <body onload="startTime()">
        <?php
        if ($underconstruction) {
            echo ""
            . "<div id='overlay' onclick='unblockPage()'>"
            . "<div id='overlaytext'><p>Under Construction</P><P>See Boomgarden for Status Changes</P></div>"
            . "</div>";
            echo ""
            . "<script>document.getElementById('overlay').style.display = 'block';</script>";
        }
        ?>
        <header>
            <!--=====================================
            NAV Bar
            ======================================-->

            <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark" aria-label="Main navigation">
                <div class="container-fluid"> <a class="navbar-brand" onclick="closeAllForms()">ESD New York</a>
                    <button class="navbar-toggler p-0 border-0" type="button" id="navbarSideCollapse" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button>
                    <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link active dropdown-toggle" href="#" id="dropdown01" data-bs-toggle="dropdown" aria-expanded="false">Status</a>
                                <ul class="dropdown-menu" aria-labelledby="dropdown01">
                                    <li> <a class="dropdown-item" onclick="openUpdateStatusForm()">Update Status</a> </li>
                                    <li> <a class="dropdown-item" onclick="openUpdateMemberForm()">Update Member Info</a> </li>
                                    <li> <a class="dropdown-item" onclick="openAddMemberForm()">Add Member</a> </li>
                                    <li> <a class="dropdown-item" onclick="openRemoveMemberForm()">Remove Member</a> </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link active dropdown-toggle" href="#" id="dropdown01" data-bs-toggle="dropdown" aria-expanded="false">Projects</a>
                                <ul class="dropdown-menu" aria-labelledby="dropdown01">
                                    <li> <a class="dropdown-item" onclick="openAddProjectForm()">Add Project</a> </li>
                                    <li> <a class="dropdown-item" onclick="openRemoveProjectForm()">Remove Project</a> </li>
                                    <li> <a class="dropdown-item" onclick="openUpdateProjectForm()">Update Project</a> </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link active dropdown-toggle" href="#" id="dropdown01" data-bs-toggle="dropdown" aria-expanded="false">Links</a>
                                <ul class="dropdown-menu" aria-labelledby="dropdown01" >
                                    <li><a class="dropdown-item" href="http://192.168.1.2" target="_blank">AdGuard Home</a></li>
                                    <li><a class="dropdown-item" href="https://192.168.1.5:8443" target="_blank">Unifi Controller</a></li>
                                    <li><a class="dropdown-item" href="http://192.168.1.10:10000" target="_blank">Webmin</a></li>
                                    <li><a class="dropdown-item" href="http://192.168.1.20" target="_blank">TrueNAS Core</a></li>
                                    <li><a class="dropdown-item" href="adminer/adminer-4.8.1-mysql-en.php" target="_blank">Adminer</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="navbar-brand d-none d-lg-block" id="ClockTime"></div>
            </nav>

            <!--=====================================
            Pop Down Forms
            ======================================-->
            <?php
            //get the members currently in the DB for use in the below forms
            $getAllMembersQuery = "SELECT last_name, first_name, rate_rank FROM members ORDER BY last_name ASC";
            $getAllProjectsQuery = "SELECT due_date, title_description, rate FROM projects ORDER BY due_date ASC"
            ?>
            <div class="container-fluid bg-light border border-dark">
                <div class="form-popup" id="updateStatusFormContainer">
                    <p id="updateStatusJavascriptOutput"></p>
                    <form method="post" action="php/insert_update_queries.php" class="needs-validation" id="updateStatusForm" novalidate>
                        <hr class="my-4">
                        <h4 class="mb-3">Update Status</h4>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-3">
                                <label for="updateNameSelect" class="form-label">Member Name</label>
                                <select id="statusUpdateNameSelect" multiple="multiple" class="form-select" name="updateName[]" placeholder="" value="" required>
                                    <!--option value="">Choose...</option-->
                                    <?php
                                    $allMembers = $conn->query($getAllMembersQuery) or die($conn->error);
                                    if ($allMembers == $conn->query($getAllMembersQuery)) {
                                        while ($row = $allMembers->fetch_assoc()) {
                                            $lastName = $row["last_name"];
                                            $firstName = $row["first_name"];
                                            $rateRank = $row["rate_rank"];
                                            $field1name = $lastName . ", " . $firstName . ", " . $rateRank;
                                            echo '<option id="' . $lastName . $firstName . $rateRank . '">' . $field1name . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">
                                    Member Name is required.
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="updateLocationSelect" class="form-label">Location</label>
                                <select class="form-control" name="newLocation" placeholder="" value="">
                                    <option value="">Choose...</option>
                                    <option>ESD New York</option>
                                    <option>Lunch</option>
                                    <option>Gym</option>
                                    <option>SEC New York</option>
                                    <option>STA New York</option>
                                    <option>STA Kings Point</option>
                                    <option>Sandy Hook</option>
                                    <option>Bayonne</option>
                                    <option>Saugerties</option>
                                    <option>Battery</option>
                                    <option>RO EWR</option>
                                    <option>Boston</option>
                                    <option>TDY</option>
                                    <option>Other</option>
                                    <option>RFF Alpine</option>
                                    <option>RFF Catskill</option>
                                    <option>RFF Highlands</option>
                                    <option>RFF Naveskink</option>
                                    <option>RFF Putnam Valley</option>
                                    <option>RFF Queens</option>
                                    <option>RFF Troy</option>
                                    <option>RFF Whitehall</option>
                                </select>
                                <div class="invalid-feedback">
                                    Location is required.
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label" for="updateDutySelect">On Duty</label>
                                <select class="form-control" name="newDuty" placeholder="" value="">
                                    <option value="">Choose...</option>
                                    <option>On Duty</option>
                                    <option>Off Duty</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label" for="updateLeaveSelect">On Leave</label>
                                <select class="form-control" name="newLeave" placeholder="" value="">
                                    <option value="">Choose...</option>
                                    <option>On Leave</option>
                                    <option>Off Leave</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label" for="updateOrdersSelect">On Orders</label>
                                <select class="form-control" name="newOrders" placeholder="" value="">
                                    <option value="">Choose...</option>
                                    <option>On Orders</option>
                                    <option>Off Orders</option>
                                </select>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <button class="w-100 btn btn-success" type="submit" name="updateStatus" >Update Status</button>
                            </div>
                            <div class="col-sm-6">
                                <button  class="w-100 btn btn-danger" type="reset" name="closeButton" onclick="closeAllForms()" >Close</button>
                            </div>
                        </div>
                        <hr class="my-4">
                    </form>
                </div>
                <div class="form-popup" id="updateMemberFormContainer">
                    <form method="post" action="php/insert_update_queries.php" class="needs-validation" id="updateMemberForm" novalidate>
                        <hr class="my-4">
                        <h4 class="mb-3">Update Member</h4>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-3">
                                <label for="updateNameSelect" class="form-label">Member Name</label>
                                <select class="form-control" name="updateName" placeholder="" value="" required>
                                    <option value="">Choose...</option>
                                    <?php
                                    $allMembers = $conn->query($getAllMembersQuery) or die($conn->error);
                                    if ($allMembers == $conn->query($getAllMembersQuery)) {
                                        while ($row = $allMembers->fetch_assoc()) {
                                            $lastName = $row["last_name"];
                                            $firstName = $row["first_name"];
                                            $rateRank = $row["rate_rank"];
                                            $field1name = $lastName . ", " . $firstName . ", " . $rateRank;
                                            echo '<option>' . $field1name . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">
                                    Member Name is required.
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <label for="rateRank" class="form-label">Rate/Rank</label>
                                <select class="form-control" name="newRateRank" placeholder="" value="">
                                    <option value="">Choose...</option>
                                    <option>ITCS</option>
                                    <option>ITC</option>
                                    <option>IT1</option>
                                    <option>IT2</option>
                                    <option>IT3</option>
                                    <option>ETCS</option>
                                    <option>ETC</option>
                                    <option>ET1</option>
                                    <option>ET2</option>
                                    <option>ET3</option>
                                    <option>SNET</option>
                                    <option>FNET</option>
                                    <option>SNIT</option>
                                    <option>FNET</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="firstName" class="form-label">First name</label>
                                <input type="text" class="form-control" name="newFirstName" placeholder="" value="">
                            </div>
                            <div class="col-sm-4">
                                <label for="lastName" class="form-label">Last name</label>
                                <input type="text" class="form-control" name="newLastName" placeholder="" value="">
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <button class="w-100 btn btn-success" type="submit" name="updateMember" >Update Member</button>
                            </div>
                            <div class="col-sm-6">
                                <button  class="w-100 btn btn-danger" type="reset" name="closeButton" onclick="closeAllForms()" >Close</button>
                            </div>
                        </div>
                        <hr class="my-4">
                    </form>
                </div>
                <div class="form-popup" id="addMemberFormContainer">
                    <form method="post" action="php/insert_update_queries.php" class="needs-validation" id="addMemberForm" novalidate>
                        <hr class="my-4">
                        <h4 class="mb-3">Add Member</h4>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-1">
                                <label for="rateRank" class="form-label">Rate/Rank</label>
                                <select class="form-control" name="rateRank" placeholder="" value="" required>
                                    <option value="">Choose...</option>
                                    <option>ITCS</option>
                                    <option>ITC</option>
                                    <option>IT1</option>
                                    <option>IT2</option>
                                    <option>IT3</option>
                                    <option>ETCS</option>
                                    <option>ETC</option>
                                    <option>ET1</option>
                                    <option>ET2</option>
                                    <option>ET3</option>
                                    <option>SNET</option>
                                    <option>FNET</option>
                                    <option>SNIT</option>
                                    <option>FNET</option>
                                </select>
                                <div class="invalid-feedback">
                                    Rate/Rank is required.
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="firstName" class="form-label">First name</label>
                                <input type="text" class="form-control" name="firstName" placeholder="" value="" required>
                                <div class="invalid-feedback">
                                    Valid first name is required.
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <label for="lastName" class="form-label">Last name</label>
                                <input type="text" class="form-control" name="lastName" placeholder="" value="" required>
                                <div class="invalid-feedback">
                                    Valid last name is required.
                                </div>
                            </div>

                            <div class="col-3">
                                <label for="username" class="form-label">Recruitment Type</label>
                                <select class="form-control" name="recruitmentType" placeholder="" value="" required>
                                    <option value="">Choose...</option>
                                    <option>Active Duty</option>
                                    <option>Reserve</option>
                                </select>
                                <div class="invalid-feedback">
                                    Recruitment Type is required.
                                </div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <button class="w-100 btn btn-success" type="submit" name="addMember" >Add Member</button>
                            </div>
                            <div class="col-sm-6">
                                <button  class="w-100 btn btn-danger" type="reset" name="closeButton" onclick="closeAllForms()">Close</button>
                            </div>
                        </div>
                        <hr class="my-4">
                    </form>
                </div>
                <div class="form-popup" id="removeMemberFormContainer">
                    <form method="post" action="php/insert_update_queries.php" class="needs-validation" id="removeMemberForm" novalidate>
                        <hr class="my-4">
                        <h4 class="mb-3">Remove Member</h4>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-8">
                                <label for="removeNameSelect" class="form-label">Member Name</label>
                                <select class="form-control" name="removeName" placeholder="" value="" required>
                                    <option value="">Choose...</option>
                                    <?php
                                    $allMembers = $conn->query($getAllMembersQuery) or die($conn->error);
                                    if ($allMembers == $conn->query($getAllMembersQuery)) {
                                        while ($row = $allMembers->fetch_assoc()) {
                                            $lastName = $row["last_name"];
                                            $firstName = $row["first_name"];
                                            $rateRank = $row["rate_rank"];
                                            $field1name = $lastName . ", " . $firstName . ", " . $rateRank;
                                            echo '<option>' . $field1name . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">
                                    Member Name is required.
                                </div>
                            </div>

                            <div class="col-4">
                                <label for="username" class="form-label">Are you sure?</label>
                                <select class="form-control" name="removeConfirm" placeholder="" value="" required>
                                    <option value="">Choose...</option>
                                    <option>YES</option>
                                </select>
                                <div class="invalid-feedback">
                                    Apparently you aren't sure!
                                </div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <button class="w-100 btn btn-success" type="submit" name="removeMember" >Remove Member</button>
                            </div>
                            <div class="col-sm-6">
                                <button  class="w-100 btn btn-danger" type="reset" name="closeButton" onclick="closeAllForms()">Close</button>
                            </div>
                        </div>
                        <hr class="my-4">
                    </form>
                </div>
                <div class="form-popup" id="addProjectFormContainer">
                    <form method="post" action="php/insert_update_queries.php" class="needs-validation" id="addProjectForm" novalidate>
                        <hr class="my-4">
                        <h4 class="mb-3">Add Project</h4>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-2">
                                <label for="projectDateLabel" class="form-label">Date</label>
                                <input type="date" class="form-control" name="projectDate" placeholder="" value="" required>
                                <div class="invalid-feedback">
                                    Date is required.
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <label for="projectDescription" class="form-label">Title/Description</label>
                                <input type="text" class="form-control" name="projectDescription" placeholder="" value="" required>
                                <div class="invalid-feedback">
                                    Project description is required.
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <label for="projectRate" class="form-label">Rate</label>
                                <select class="form-control" name="projectRate" placeholder="" value="" required>
                                    <option value="">Choose...</option>
                                    <option>IT</option>
                                    <option>ET</option>
                                    <option>BOTH</option>
                                </select>
                                <div class="invalid-feedback">
                                    Project Rate is required.
                                </div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <button class="w-100 btn btn-success" type="submit" name="addProject" >Add Project</button>
                            </div>
                            <div class="col-sm-6">
                                <button  class="w-100 btn btn-danger" type="reset" name="closeButton" onclick="closeAllForms()">Close</button>
                            </div>
                        </div>
                        <hr class="my-4">
                    </form>
                </div>
                <div class="form-popup" id="removeProjectFormContainer">
                    <form method="post" action="php/insert_update_queries.php" class="needs-validation" id="removeProjectForm" novalidate>
                        <hr class="my-4">
                        <h4 class="mb-3">Remove Project</h4>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-8">
                                <label for="removeProjectSelect" class="form-label">Project</label>
                                <select class="form-control" name="projectName" placeholder="" value="" required>
                                    <option value="">Choose...</option>
                                    <?php
                                    $allProjects = $conn->query($getAllProjectsQuery) or die($conn->error);
                                    if ($allProjects == $conn->query($getAllProjectsQuery)) {
                                        while ($row = $allProjects->fetch_assoc()) {
                                            $projectDate = $row["due_date"];
                                            $projectDescription = $row["title_description"];
                                            $projectRate = $row["rate"];
                                            $field1name = $projectRate . ", " . $projectDate . ", " . substr($projectDescription, 0, 90);
                                            echo '<option>' . $field1name . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">
                                    Project selection is required.
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="username" class="form-label">Are you sure?</label>
                                <select class="form-control" name="removeConfirm" placeholder="" value="" required>
                                    <option value="">Choose...</option>
                                    <option>YES</option>
                                </select>
                                <div class="invalid-feedback">
                                    Apparently you aren't sure!
                                </div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <button class="w-100 btn btn-success" type="submit" name="removeProject" >Remove Project</button>
                            </div>
                            <div class="col-sm-6">
                                <button  class="w-100 btn btn-danger" type="reset" name="closeButton" onclick="closeAllForms()">Close</button>
                            </div>
                        </div>
                        <hr class="my-4">
                    </form>
                </div>
                <div class="form-popup" id="updateProjectFormContainer">
                    <form method="post" action="php/insert_update_queries.php" class="needs-validation" id="updateProjectForm" novalidate>
                        <hr class="my-4">
                        <h4 class="mb-3">Update Project</h4>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-8">
                                <label for="updateProjectSelect" class="form-label">Project</label>
                                <select class="form-control" name="projectName" placeholder="" value="" required>
                                    <option value="">Choose...</option>
                                    <?php
                                    $allProjects = $conn->query($getAllProjectsQuery) or die($conn->error);
                                    if ($allProjects == $conn->query($getAllProjectsQuery)) {
                                        while ($row = $allProjects->fetch_assoc()) {
                                            $projectDate = $row["due_date"];
                                            $projectDescription = $row["title_description"];
                                            $projectRate = $row["rate"];
                                            $field1name = $projectRate . ", " . $projectDate . ", " . substr($projectDescription, 0, 90);
                                            echo '<option>' . $field1name . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">
                                    Project selection is required.
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="projectDateLabel" class="form-label">Date</label>
                                <input type="date" class="form-control" name="newProjectDate" placeholder="" value="">
                                <div class="invalid-feedback">
                                    Date is required.
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="projectRate" class="form-label">Rate</label>
                                <select class="form-control" name="newProjectRate" placeholder="" value="">
                                    <option value="">Choose...</option>
                                    <option>IT</option>
                                    <option>ET</option>
                                    <option>BOTH</option>
                                </select>
                                <div class="invalid-feedback">
                                    Project Rate is required.
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <label for="projectDescription" class="form-label">Title/Description</label>
                                <input type="text" class="form-control" name="newProjectDescription" placeholder="" value="">
                                <div class="invalid-feedback">
                                    Project description is required.
                                </div>
                            </div>


                        </div>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <button class="w-100 btn btn-success" type="submit" name="updateProject" >Update Project</button>
                            </div>
                            <div class="col-sm-6">
                                <button  class="w-100 btn btn-danger" type="reset" name="closeButton" onclick="closeAllForms()" >Close</button>
                            </div>
                        </div>
                        <hr class="my-4">
                    </form>
                </div>
            </div>
        </header>
        <main>
            <!--=====================================
            Main Content
            ======================================-->

            <div class="d-md-flex my-md-3 w-100 my-md-3 ps-md-3">
                <div class="col border border-dark bg-light me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                    <div class="my-3 p-3">
                        <h2 class="display-5">IT Status</h2>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Duty IT</td>
                                    <th scope="col">Member</td>
                                    <th scope="col">Location</td>
                                    <th scope="col">Leave</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                //get the ITs status
                                $getAllITsQuery = ""
                                        . "SELECT * "
                                        . "FROM members "
                                        . "WHERE rate_rank LIKE 'IT%' AND (recruitment_type LIKE 'Active%' OR on_orders = 1) "
                                        . "ORDER BY CASE "
                                        . "WHEN rate_rank LIKE '%CM' then 9 "
                                        . "WHEN rate_rank LIKE '%CS' then 8 "
                                        . "WHEN rate_rank LIKE '%C' then 7 "
                                        . "WHEN rate_rank LIKE '%1' then 6 "
                                        . "WHEN rate_rank LIKE '%2' then 5 "
                                        . "WHEN rate_rank LIKE '%3' then 4 "
                                        . "WHEN rate_rank LIKE '%SN%' then 3 "
                                        . "WHEN rate_rank LIKE '%FN%' then 3 "
                                        . "WHEN rate_rank LIKE '%AN%' then 3 "
                                        . "END DESC, "
                                        . "last_name ASC";
                                $allITs = $conn->query($getAllITsQuery) or die($conn->error);
                                //print entire table formatted
                                if ($allITs == $conn->query($getAllITsQuery)) {
                                    while ($row = $allITs->fetch_assoc()) {
                                        $field1name = $row["duty"];
                                        if ($field1name == '0') {
                                            $field1name = "";
                                        } else {
                                            $field1name = "X";
                                        }
                                        $lastName = $row["last_name"];
                                        $firstName = $row["first_name"];
                                        $rateRank = $row["rate_rank"];
                                        $field2name = $lastName . ", " . $firstName . " " . $rateRank;
                                        $field5name = $row["location"];
                                        $field6name = $row["leave_status"];
                                        if ($field6name == '0') {
                                            $field6name = "";
                                        } else {
                                            $field6name = "X";
                                        }
                                        $nameParameter = '"';
                                        $nameParameter .= $field2name . '"';
                                        echo "<tr onclick='openUpdateStatusForm(" . $nameParameter . ")' value='" . preg_replace('/[^A-Za-z0-9\-]/ ', '', $nameParameter) . "'>";
                                        if ($field6name == "X") {
                                            echo '
                                            
                                                <td style="color: #7A7A7A">' . $field1name . '</td>
                                                <td style="color: #7A7A7A">' . $field2name . '</td>
                                                <td style="color: #7A7A7A">' . $field5name . '</td>
                                                <td style="color: #7A7A7A">' . $field6name . '</td>
                                             
                                        ';
                                        } elseif ($field1name == "X") {
                                            echo '
                                            
                                                <td><b>' . $field1name . '</b></td>
                                                <td><b>' . $field2name . '</b></td>
                                                <td><b>' . $field5name . '</b></td>
                                                <td><b>' . $field6name . '</b></td>
                                             
                                        ';
                                        } else {
                                            echo '
                                            
                                                <td>' . $field1name . '</td>
                                                <td>' . $field2name . '</td>
                                                <td>' . $field5name . '</td>
                                                <td>' . $field6name . '</td>
                                             
                                        ';
                                        }
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col border border-dark bg-light me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden">
                    <div class="my-3 p-3">
                        <h2 class="display-5">ET Status</h2>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Duty ET</td>
                                    <th scope="col">Member</td>
                                    <th scope="col">Location</td>
                                    <th scope="col">Leave</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                //get the ETs status
                                $getAllETsQuery = ""
                                        . "SELECT * "
                                        . "FROM members "
                                        . "WHERE rate_rank LIKE 'ET%' AND (recruitment_type LIKE 'Active%' OR on_orders = 1) "
                                        . "ORDER BY CASE "
                                        . "WHEN rate_rank LIKE '%CM' then 9 "
                                        . "WHEN rate_rank LIKE '%CS' then 8 "
                                        . "WHEN rate_rank LIKE '%C' then 7 "
                                        . "WHEN rate_rank LIKE '%1' then 6 "
                                        . "WHEN rate_rank LIKE '%2' then 5 "
                                        . "WHEN rate_rank LIKE '%3' then 4 "
                                        . "WHEN rate_rank LIKE '%SN%' then 3 "
                                        . "WHEN rate_rank LIKE '%FN%' then 3 "
                                        . "WHEN rate_rank LIKE '%AN%' then 3 "
                                        . "END DESC, "
                                        . "last_name ASC";
                                $allETs = $conn->query($getAllETsQuery) or die($conn->error);
                                //print entire table formatted
                                if ($allETs == $conn->query($getAllETsQuery)) {
                                    while ($row = $allETs->fetch_assoc()) {
                                        $field1name = $row["duty"];
                                        if ($field1name == '0') {
                                            $field1name = "";
                                        } else {
                                            $field1name = "X";
                                        }
                                        $lastName = $row["last_name"];
                                        $firstName = $row["first_name"];
                                        $rateRank = $row["rate_rank"];
                                        $field2name = $lastName . ", " . $firstName . " " . $rateRank;
                                        $field5name = $row["location"];
                                        $field6name = $row["leave_status"];
                                        if ($field6name == '0') {
                                            $field6name = "";
                                        } else {
                                            $field6name = "X";
                                        }
                                        $nameParameter = '"';
                                        $nameParameter .= $field2name . '"';
                                        echo "<tr onclick='openUpdateStatusForm(" . $nameParameter . ")' value='" . preg_replace('/[^A-Za-z0-9\-]/ ', '', $nameParameter) . "'>";
                                        if ($field6name == "X") {
                                            echo '
                                            
                                                <td style="color: #7A7A7A">' . $field1name . '</td>
                                                <td style="color: #7A7A7A">' . $field2name . '</td>
                                                <td style="color: #7A7A7A">' . $field5name . '</td>
                                                <td style="color: #7A7A7A">' . $field6name . '</td>
                                             
                                        ';
                                        } elseif ($field1name == "X") {
                                            echo '
                                            
                                                <td><b>' . $field1name . '</b></td>
                                                <td><b>' . $field2name . '</b></td>
                                                <td><b>' . $field5name . '</b></td>
                                                <td><b>' . $field6name . '</b></td>
                                             
                                        ';
                                        } else {
                                            echo '
                                            
                                                <td>' . $field1name . '</td>
                                                <td>' . $field2name . '</td>
                                                <td>' . $field5name . '</td>
                                                <td>' . $field6name . '</td>
                                             
                                        ';
                                        }
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="border border-dark position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-light">
                <div class="col p-lg-12 mx-auto my-5">
                    <h2 class="display-5" >Projects</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Date</td>
                                <th scope="col">Title/Description</td>
                                <th scope="col">ET/IT</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            //get the projects
                            $getAllProjectsQuery = "SELECT * FROM projects ORDER BY due_date ASC";
                            $allProjects = $conn->query($getAllProjectsQuery) or die($conn->error);
                            //print entire table formatted
                            if ($allProjects == $conn->query($getAllProjectsQuery)) {
                                while ($row = $allProjects->fetch_assoc()) {
                                    $field1name = $row["due_date"];
                                    $field2name = $row["title_description"];
                                    $field3name = $row["rate"];
                                    echo
                                    '
			<tr onclick = "openUpdateProjectForm()">
			<td>' . $field1name . '</td>
			<td>' . $field2name . '</td>
			<td>' . $field3name . '</td>
                        </tr>
			';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <!--=====================================
        JavaScript
        ======================================-->
        <script>
            function openUpdateStatusForm(nameData) {
                closeAllForms();
                document.getElementById("updateStatusFormContainer").style.display = "block";
                document.querySelector('.offcanvas-collapse').classList.toggle('open');
                if (nameData !== undefined) {
                    //takes the passed name data last, first, raterank
                    //and makes it lastfirstraterank to matche the "value" values
                    let editedNameData = nameData.replace(/[^a-zA-Z]/g, "");
                    //print the modified name (not required)
                    document.getElementById("updateStatusJavascriptOutput").innerHTML = editedNameData;
                    //select the multiselect element??????
                    const $select = document.querySelector('statusUpdateNameSelect');


                    //set the value of the selected element
                    $select.value = 'editedNameData';
                    //statusUpdateNameSelect.id ='openStatusForm.value';
                }

            }
            function openUpdateMemberForm() {
                closeAllForms();
                document.getElementById("updateMemberFormContainer").style.display = "block";
                document.querySelector('.offcanvas-collapse').classList.toggle('open');
            }
            function openAddMemberForm() {
                closeAllForms();
                document.getElementById("addMemberFormContainer").style.display = "block";
                document.querySelector('.offcanvas-collapse').classList.toggle('open');
            }
            function openRemoveMemberForm() {
                closeAllForms();
                document.getElementById("removeMemberFormContainer").style.display = "block";
                document.querySelector('.offcanvas-collapse').classList.toggle('open');
            }

            function openAddProjectForm() {
                closeAllForms();
                document.getElementById("addProjectFormContainer").style.display = "block";
                document.querySelector('.offcanvas-collapse').classList.toggle('open');
            }

            function openRemoveProjectForm() {
                closeAllForms();
                document.getElementById("removeProjectFormContainer").style.display = "block";
                document.querySelector('.offcanvas-collapse').classList.toggle('open');
            }

            function openUpdateProjectForm() {
                closeAllForms();
                document.getElementById("updateProjectFormContainer").style.display = "block";
                document.querySelector('.offcanvas-collapse').classList.toggle('open');
            }
            function unblockPage() {
                document.getElementById('overlay').style.display = 'none';
                document.querySelector('.offcanvas-collapse').classList.toggle('open');
            }

            function closeAllForms() {
                document.getElementById("updateStatusFormContainer").style.display = "none";
                document.getElementById('updateStatusForm').reset();
                document.getElementById("updateMemberFormContainer").style.display = "none";
                document.getElementById("updateMemberForm").reset();
                document.getElementById("addMemberFormContainer").style.display = "none";
                document.getElementById("addMemberForm").reset();
                document.getElementById("removeMemberFormContainer").style.display = "none";
                document.getElementById("removeMemberForm").reset();
                document.getElementById("addProjectFormContainer").style.display = "none";
                document.getElementById("addProjectForm").reset();
                document.getElementById("removeProjectFormContainer").style.display = "none";
                document.getElementById("removeProjectForm").reset();
                document.getElementById("updateProjectFormContainer").style.display = "none";
                document.getElementById("updateProjectForm").reset();
            }

            function startTime() {
                const today = new Date();
                let stringDate = today.toString();
                stringDate = stringDate.substr(0, stringDate.indexOf("("));
                document.getElementById('ClockTime').innerHTML = stringDate;
                setTimeout(startTime, 1000);
            }

            closeAllForms();
        </script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="js/offcanvas.js"></script>
        <script src="js/form-validation.js"></script>
        <!--=====================================
        PHP
        ======================================-->
        <?PHP
        mysqli_close($conn);
        ?>

    </body>
</html>
