<?php

namespace App\Http\Controllers;

use App\Http\Resources\MatchesResource;
use App\Models\Matches;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MatchesController extends Controller
{
    /**
     * @OA\Get(
     *      path="/matches",
     *      operationId="getMatchesList",
     *      tags={"Matches"},
     *      summary="get list of matches",
     *      description="Returns list of matches",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      )
     * )
     */
    public function index()
    {
        return MatchesResource::collection(Matches::with(['details', 'homeTeam', 'awayTeam'])->orderByDesc('id')->paginate());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/matches",
     *      operationId="createMatches",
     *      tags={"Matches"},
     *      summary="create new matches",
     *      description="create data matches",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="create matches",
     *          @OA\JsonContent(
     *              @OA\Property(property="time", type="datetime", format="datetime", example="2022-07-28 15:00:00"),
     *              @OA\Property(property="home", type="number", format="number", example="1"),
     *              @OA\Property(property="away", type="number", format="number", example="3"),
     *              @OA\Property(
     *                  property="details",
     *                  type="array",
     *                  @OA\Items(
     *                       @OA\Property(
     *                           property="player_id",
     *                           type="number",
     *                           example="1"
     *                       ),
     *                       @OA\Property(
     *                           property="type",
     *                           type="number",
     *                           example="1"
     *                       ),
     *                       @OA\Property(
     *                           property="team",
     *                           type="text",
     *                           example="home"
     *                       ),
     *                       @OA\Property(
     *                           property="minute",
     *                           type="number",
     *                           example="30"
     *                       ),
     *                       @OA\Property(
     *                           property="note",
     *                           type="text",
     *                           example="free kick goals"
     *                       )
     *                   ),
     *               )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successfully Created",
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Content - Validation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time'      => 'required|date_format:Y-m-d H:i:s',
            'home'      => 'required|integer|exists:teams,id',
            'away'      => 'required|integer|exists:teams,id',
            'details'   => 'array',
            'details.*.player_id' => 'required|integer|exists:players,id',
            'details.*.type'      => 'required|in:1,2,3,4',
            'details.*.team'      => 'required|in:home,away',
            'details.*.minute'    => 'required|integer'
        ]);

        if ($validator->fails()) return response(['errors' => $validator->errors()->all()], 422);

        DB::beginTransaction();
		try {
            $data = $request->except('details');
            $data = Matches::create($data);

            if ($request->has('details')) {
                $data->details()->createMany($request->details);
            }

			DB::commit();
            $res = new MatchesResource(Matches::with(['details', 'homeTeam', 'awayTeam'])->find($data->id));
            return $res->response()->setStatusCode(201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/matches/{id}",
     *      operationId="getMatchesById",
     *      tags={"Matches"},
     *      summary="get matches by id",
     *      description="get matches by id",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="get matches by id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      )
     * )
     */
    public function show(Matches $matches)
    {
        return new MatchesResource(Matches::with(['details', 'homeTeam', 'awayTeam'])->find($matches->id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Matches  $matches
     * @return \Illuminate\Http\Response
     */
    public function edit(Matches $matches)
    {
        //
    }

    /**
     * @OA\Put(
     *      path="/matches/{id}",
     *      operationId="updateMatchesById",
     *      tags={"Matches"},
     *      summary="update matches by id",
     *      description="update matches data",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="update matches by id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="update matches by id",
     *          @OA\JsonContent(
     *              @OA\Property(property="time", type="datetime", format="datetime", example="2022-07-28 15:00:00"),
     *              @OA\Property(property="home", type="number", format="number", example="1"),
     *              @OA\Property(property="away", type="number", format="number", example="3"),
     *              @OA\Property(
     *                  property="details",
     *                  type="array",
     *                  example={{
     *                      "action": "create",
     *                      "player_id": 1,
     *                      "type": 1,
     *                      "team": "home",
     *                      "minute": 50,
     *                  }, {
     *                      "id": 11,
     *                      "action": "update",
     *                      "player_id": 1,
     *                      "type": 1,
     *                      "team": "home",
     *                      "minute": 20,
     *                  }, {
     *                      "id": 10,
     *                      "action": "delete",
     *                      "player_id": 2,
     *                      "type": 1,
     *                      "team": "home",
     *                      "minute": 30,
     *                  }},
     *                  @OA\Items(
     *                       @OA\Property(
     *                           property="player_id",
     *                           type="number",
     *                           example=""
     *                       ),
     *                       @OA\Property(
     *                           property="type",
     *                           type="number",
     *                           example=""
     *                       ),
     *                       @OA\Property(
     *                           property="team",
     *                           type="text",
     *                           example=""
     *                       ),
     *                       @OA\Property(
     *                           property="minute",
     *                           type="number",
     *                           example=""
     *                       ),
     *                       @OA\Property(
     *                           property="note",
     *                           type="text",
     *                           example=""
     *                       )
     *                   ),
     *               )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Content - Validation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      )
     * )
     */
    public function update(Request $request, Matches $matches)
    {
        $validator = Validator::make($request->all(), [
            'time'      => 'required|date_format:Y-m-d H:i:s',
            'home'      => 'required|integer|exists:teams,id',
            'away'      => 'required|integer|exists:teams,id',
            'details'   => 'array',
            'details.*.player_id' => 'required|integer|exists:players,id',
            'details.*.id'        => 'integer|exists:match_details,id',
            'details.*.action'    => 'required|in:create,update,delete',
            'details.*.type'      => 'required|in:1,2,3,4',
            'details.*.team'      => 'required|in:home,away',
            'details.*.minute'    => 'required|integer'
        ]);

        if ($validator->fails()) return response(['errors' => $validator->errors()->all()], 422);

        $data = $request->except('details');

        $matches->update($data);

        if ($request->has('details')) {
            foreach ($request->details as $detail) {
                switch ($detail['action']) {
                    case 'delete':
                        $matches->details()->find($detail['id'])->delete();
                        break;
                    
                    default:
                        unset($detail['action']);
                        $matches->details()->updateOrCreate(["id" => $detail['id'] ?? 0], $detail);
                        break;
                }
            }
        }

        return $this->show($matches);
    }

    /**
     * @OA\Delete(
     *      path="/matches/{id}",
     *      operationId="deleteMatchesById",
     *      tags={"Matches"},
     *      summary="delete matches by id",
     *      description="delete matches by Id",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="delete matches by id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Success - No Content",
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      )
     * )
     */
    public function destroy(Matches $matches)
    {
        $matches->delete();
        return response()->noContent();
    }
}
