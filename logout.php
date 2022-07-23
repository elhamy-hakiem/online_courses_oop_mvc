<?php
require("globals.php");

session_destroy();

Redirect::TO('login.php');