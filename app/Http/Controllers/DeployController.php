<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeployController extends Controller
{
    public function webhook(Request $request)
    {
        // Incluir o script de deploy
        include __DIR__ . '/../../deploy.php';
    }
}