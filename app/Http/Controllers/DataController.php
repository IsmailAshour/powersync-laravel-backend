<?php

namespace App\Http\Controllers;

use App\Services\PostgresPersister;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class DataController extends Controller
{
    /**
     * Handle a batch of events.
     */
    public function postBatch(Request $request): JsonResponse
    {
        $request->validate([
            'body' => 'required'
        ]);


        try {
            PostgresPersister::make()->updateBatch($request->get('body'));

            return response()->json(['message' => 'Batch completed']);
        } catch (Exception $e) {

            Log::error('Request failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => "Request failed: {$e->getMessage()}"], 400);
        }
    }

    /**
     * Handle all PUT events sent to the server by the client PowerSync application
     */
    public function putBatch(Request $request): JsonResponse
    {
        $request->validate([
            'body' => 'required',
            'body.table' => 'required',
            'body.data' => 'required',
        ]);

        try {
            PostgresPersister::make()->updateBatch($request->get('body'));

            $table = $request->get('body')['table'];

            return response()->json(['message' => "PATCH completed for $table"]);
        } catch (\Exception $e) {
            Log::error('Request failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => "Request failed: {$e->getMessage()}"], 400);
        }
    }

    /**
     * Handle checkpoint creation.
     */
    public function putCheckpoint(Request $request): JsonResponse
    {
        $request->validate([
            'body' => 'required',
            'user_id' => 'required',
            'client_id' => 'required',
        ]);

        $userId = $request->input('user_id', 'user_id');
        $clientId = $request->input('client_id', '1');

        $checkpoint = PostgresPersister::make()->createCheckpoint($userId, $clientId);

        return response()->json([$checkpoint]);
    }

    /**
     * Handle all PATCH events sent to the server by the client PowerSync application
     */
    public function patchBatch(Request $request): JsonResponse
    {
        $request->validate([
            'body' => 'required'
        ]);

        try {
            PostgresPersister::make()->updateBatch($request->get('body'));

            $table = $request->get('body')['table'];

            return response()->json(['message' => "PATCH completed for $table"]);
        } catch (\Exception $e) {
            Log::error('Request failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => "Request failed: {$e->getMessage()}"], 400);
        }
    }

    /**
     * Handle all DELETE events sent to the server by the client PowerSync application
     */
    public function deleteBatch(Request $request): Application|Response|ResponseFactory
    {
        $request->validate([
            'body' => 'required',
            'body.table' => 'required',
            'body.data' => 'required',
        ]);

        try {
            PostgresPersister::make()->updateBatch($request->get('body'));

            $table = $request->get('body')['table'];
            $data = $request->get('body')['data'];

            return response(['message' => "DELETE completed for " . $table . ' ' . $data['id']], 200);

        } catch (Exception $e) {
            return response(['message' => "Request failed:" . $e->getMessage()], 400);
        }
    }
}
