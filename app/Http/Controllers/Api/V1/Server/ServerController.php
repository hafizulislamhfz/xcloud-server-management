<?php

namespace App\Http\Controllers\Api\V1\Server;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Server\ServerIndexRequest;
use App\Http\Requests\V1\Server\ServerStoreRequest;
use App\Http\Requests\V1\Server\ServerUpdateRequest;
use App\Http\Resources\V1\Server\ServerResource;
use App\Models\Server;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class ServerController extends Controller
{
    public function index(ServerIndexRequest $request)
    {
        try {
            $search = $request->query('search');
            $provider = $request->query('provider');
            $status = $request->query('status');
            $sortBy = $request->query('sort_by', 'created_at');
            $sortOrder = $request->query('sort_order', 'desc');
            $perPage = $request->query('per_page', 25);
            $pageNumber = $request->query('page_number', 1);

            $servers = Server::when($search, function ($q) use ($search) {
                $q->whereAny(['name', 'provider', 'ip_address'], 'LIKE', "%{$search}%");
            })->when($provider, fn ($q) => $q->where('provider', $provider))
                ->when($status, fn ($q) => $q->where('status', $status))
                ->orderBy($sortBy, $sortOrder)
                ->paginate($perPage, ['*'], 'page', $pageNumber);

            return $this->successResponse(
                ServerResource::collection($servers),
                'Servers Retrieved'
            );

        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    public function store(ServerStoreRequest $request)
    {
        try {
            $server = Server::create($request->validated());

            return $this->successResponse(
                new ServerResource($server),
                'Server created successfully.',
                Response::HTTP_CREATED
            );

        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    public function show(int $id)
    {
        try {
            $server = Server::findOrFail($id);

            return $this->successResponse(
                new ServerResource($server),
                'Server Retrieved'
            );

        } catch (ModelNotFoundException) {
            return $this->errorResponse('Server not found.', Response::HTTP_NOT_FOUND);

        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    public function update(ServerUpdateRequest $request, int $id)
    {
        try {
            $server = Server::findOrFail($id);
            $server->update($request->validated());

            return $this->successResponse(
                new ServerResource($server),
                'Server updated successfully.'
            );

        } catch (ModelNotFoundException) {
            return $this->errorResponse('Server not found.', Response::HTTP_NOT_FOUND);

        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $server = Server::findOrFail($id);
            $server->delete();

            return response()->noContent();

        } catch (ModelNotFoundException) {
            return $this->errorResponse('Server not found.', Response::HTTP_NOT_FOUND);

        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
}
