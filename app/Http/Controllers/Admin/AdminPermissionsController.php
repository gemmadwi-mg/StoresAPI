<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminPermissionsController extends Controller
{
    public function show($id)
    {
        if (!$user = User::find($id)) {
            throw new NotFoundHttpException('User not found with id = ' . $id);
        }

        return $user->getAllPermissions();
    }
}
