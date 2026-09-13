<?php


if(session_status()===PHP_SESSION_NONE)
    {
        session_start();
    }



function dashboardFor($role)
{
    $map=[
        "manager"  => "manager/managerDashboard.php",
        "barista"  => "barista/baristaDashboard.php",
        "waiter"   => "waiter/waiterDashboard.php",
        "customer" => "customer/customerDashboard.php",
    ];

    if(isset($map[$role]))
        {
            return $map[$role];
        }
    else
        {
            return null;
        }
}


function isLoggedIn()
{
    return isset($_SESSION["userId"]) && isset($_SESSION["role"]);
}



function requireRole($role)
{
    if(!isLoggedIn())
        {
            header("Location: ../login.php");
            exit;
        }

    if($_SESSION["role"]!=$role)
        {
            $own=dashboardFor($_SESSION["role"]);

            if($own!=null)
                {
                    header("Location: ../".$own);
                }
            else
                {
                    header("Location: ../login.php");
                }
            exit;
        }
}



function e($text)
{
    if($text==null)
        {
            return "";
        }

    return htmlspecialchars($text, ENT_QUOTES, "UTF-8");
}



function showGet($key)
{
    if(isset($_GET[$key]))
        {
            echo e($_GET[$key]);
        }
}



function dashNav($role, $active, $roleBase="", $commonBase="../")
{
    $links=[
        "manager"  => [
            "home"    => ["managerDashboard.php", "Dashboard"],
            "orders"  => ["manageOrders.php",     "Incoming orders"],
            "menu"    => ["manageMenu.php",       "Menu"],
            "staff"   => ["manageStaff.php",      "Staff"],
            "reports" => ["salesReport.php",      "Sales report"],
        ],
        "barista" => [
            "home" => ["baristaDashboard.php", "Order queue"],
        ],
        "waiter" => [
            "home" => ["waiterDashboard.php", "Ready to serve"],
        ],
        "customer" => [
            "home" => ["customerDashboard.php", "My orders"],
            "menu" => ["menu.php",              "Browse menu"],
        ],
    ];

    if(!isset($links[$role]))
        {
            return;
        }

    echo '<nav class="dash-nav">';

    foreach($links[$role] as $key => $link)
        {
            $class=($key==$active) ? "active" : "";
            echo '<a class="'.$class.'" href="'.$roleBase.$link[0].'">'.e($link[1]).'</a>';
        }

    $profileClass=($active=="profile") ? "active" : "";
    echo '<a class="'.$profileClass.'" href="'.$commonBase.'profile.php">Profile</a>';
    echo '<a href="'.$commonBase.'changePassword.php">Change password</a>';
    echo '<a href="'.$commonBase.'logout.php">Sign out</a>';
    echo '</nav>';
}

?>
