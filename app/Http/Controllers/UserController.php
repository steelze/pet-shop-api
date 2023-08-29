<?php

namespace App\Http\Controllers;

use App\Helpers\RespondWith;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function listing(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 20;

        $allowedSortBy = ['first_name', 'email', 'created_at'];
        $sortBy = in_array($request->sortBy, $allowedSortBy) ? $request->sortBy : null;
        $sortByDirection = $request->boolean('desc', false) ? 'desc' : 'asc';

        $filterByMarketing = in_array($request->marketing, [1, 0]);

        $users = User::users()
            ->when($request->first_name, fn($sql, $value) => $sql->where('first_name', 'like', "%$value%"))
            ->when($request->email, fn($sql, $value) => $sql->where('email', 'like', "%$value%"))
            ->when($request->phone, fn($sql, $value) => $sql->where('phone_number', 'like', "%$value%"))
            ->when($request->address, fn($sql, $value) => $sql->where('address', 'like', "%$value%"))
            ->when($request->created_at, fn($sql, $value) => $sql->where('created_at', 'like', "%$value%"))
            ->when($filterByMarketing, fn($sql) => $sql->where('is_marketing', $request->marketing))
            ->when(!empty($sortBy), fn($sql) => $sql->orderBy($sortBy, $sortByDirection))
            ->paginate($limit);

        return RespondWith::raw($users);
    }
}
