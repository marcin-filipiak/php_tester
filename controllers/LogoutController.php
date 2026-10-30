<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class LogoutController
{
    public function handleRequest()
    {
            session_destroy();
	    header('Location: index.php');
            exit;
    }
            
}

