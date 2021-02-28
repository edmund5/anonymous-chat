<?php

/*

	this function connects to the 'YOUR_DATABASE' database

 */
	
function connectdb()
{
    $con = mysqli_connect("localhost", "USERNAME", "PASSWORD", "YOUR_DATABASE");

    if (mysqli_connect_errno($con)) {

        exit("Failed to connect! <br>");

    } else {

        return $con;

    }
}
