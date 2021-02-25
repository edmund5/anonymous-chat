<?php

/*

	this function connects to the 'your_database' database

 */
	
function connectdb()
{
    $con = mysqli_connect("localhost", "<user>", "<pass>", "<your_database>");

    if (mysqli_connect_errno($con)) {

        exit("Failed to connect! <br>");

    } else {

        return $con;

    }
}
