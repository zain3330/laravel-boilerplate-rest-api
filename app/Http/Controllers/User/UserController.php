<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Traits\PaginationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use PaginationTrait;

    /**
     * Display a listing of the users.
     * @param Request $request
     * @return JsonResponse
     */
  public function index(Request $request): JsonResponse
  {
      $search = $request->input('search', '');

      $paginated = $this->paginateModel(
          new User(),
          ['*'],
          [],
          10,
          [],
          $search ? [['name', 'like', '%' . $search . '%']] : [],
          $search ? [['email', 'like', '%' . $search . '%']] : [],
          [],
          [],
          ['id', 'asc']
      );

      return response()->json([
          'total'             => $paginated->total(),
          'pages'             => $paginated->lastPage(),
          'current_page'      => $paginated->currentPage(),
          'next_page_url'     => $paginated->nextPageUrl(),
          'previous_page_url' => $paginated->previousPageUrl(),
          'data'              => $paginated->items()],
          200);

  }

    /**
     * Store a newly created user in storage.
     *
     * @param  UserStoreRequest $request
     * @return JsonResponse
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $password = Str::random(12);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
        ]);


        return response()->json([
            'message' => 'User created successfully. Temporary password sent to email.',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  UserUpdateRequest  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
            'data'=>$user
        ], 200);
    }

}
