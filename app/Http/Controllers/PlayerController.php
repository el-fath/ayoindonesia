<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlayerResource;
use App\Models\Player;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    /**
     * @OA\Get(
     *      path="/players",
     *      operationId="getPlayerList",
     *      tags={"Players"},
     *      summary="get list of players",
     *      description="Returns list of players",
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
        return PlayerResource::collection(Player::with(['positions', 'team'])->orderByDesc('id')->paginate());
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
     *      path="/players",
     *      operationId="createPlayer",
     *      tags={"Players"},
     *      summary="create new player",
     *      description="create data player",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="create player",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", format="text", example="Hansamu Yama"),
     *              @OA\Property(property="height", type="number", format="number", example="170"),
     *              @OA\Property(property="weight", type="number", format="number", example="70"),
     *              @OA\Property(property="number", type="number", format="number", example="5"),
     *              @OA\Property(property="team_id", type="number", format="number", example="4"),
     *              @OA\Property(
     *                  property="positions",
     *                  description="player positions",
     *                  type="array",
     *                  example={
     *                      1,2
     *                  },
     *                  @OA\Items(type="number", format="number", example="")
     *             ),
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
            'name'        => 'required|string',
            'height'      => 'required|integer',
            'weight'      => 'required|integer',
            'number'      => 'required|integer',
            'team_id'     => 'required|integer|exists:teams,id',
            'positions'   => 'required|array',
            'positions.*' => 'integer|exists:positions,id'
        ]);

        if ($validator->fails()) return response(['errors' => $validator->errors()->all()], 422);

        $number = Team::where('id', $request->team_id)->whereRelation('players', 'number', $request->number)->exists();
        if ($number) return response(['errors' => 'players number was used, please change the number'], 422);

        DB::beginTransaction();
		try {
            $data = $request->except('positions');
            $data = Player::create($data);
            $data->positions()->sync($request->positions);

			DB::commit();
            $res = new PlayerResource(Player::with(['positions', 'team'])->find($data->id));
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
     *      path="/players/{id}",
     *      operationId="getPlayerById",
     *      tags={"Players"},
     *      summary="get player by id",
     *      description="get player by id",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="get player by id",
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
    public function show(Player $player)
    {
        return new PlayerResource(Player::with(['positions', 'team'])->find($player->id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Player  $player
     * @return \Illuminate\Http\Response
     */
    public function edit(Player $player)
    {
        //
    }

    /**
     * @OA\Put(
     *      path="/players/{id}",
     *      operationId="updatePlayerById",
     *      tags={"Players"},
     *      summary="update player by id",
     *      description="update player data",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="update player by id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="update player by id",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", format="text", example="Hansamu Yama"),
     *              @OA\Property(property="height", type="number", format="number", example="170"),
     *              @OA\Property(property="weight", type="number", format="number", example="70"),
     *              @OA\Property(property="number", type="number", format="number", example="5"),
     *              @OA\Property(property="team_id", type="number", format="number", example="4"),
     *              @OA\Property(
     *                  property="positions",
     *                  description="player positions",
     *                  type="array",
     *                  example={
     *                      3,4
     *                  },
     *                  @OA\Items(type="number", format="number", example="")
     *              ),
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
    public function update(Request $request, Player $player)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'string',
            'height'      => 'integer',
            'weight'      => 'integer',
            'number'      => 'integer',
            'team_id'     => 'integer|exists:teams,id',
            'positions'   => 'array',
            'positions.*' => 'integer|exists:positions,id'
        ]);

        if ($validator->fails()) return response(['errors' => $validator->errors()->all()], 422);

        if ($request->has('number') && $request->has('team_id') && $request->number != $player->number) {
            $number = Team::where('id', $request->team_id)->whereRelation('players', 'number', $request->number)->exists();
            if ($number) return response(['errors' => 'players number was used, please change the number'], 422);
        }

        $data = $request->except('positions');
        $player->update($data);

        if ($request->has('positions')) {
            $player->positions()->sync($request->positions);
        }

        return $this->show($player);
    }

    /**
     * @OA\Delete(
     *      path="/players/{id}",
     *      operationId="deletePlayerById",
     *      tags={"Players"},
     *      summary="delete player by id",
     *      description="delete player by Id",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="delete player by id",
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
    public function destroy(Player $player)
    {
        $player->delete();
        return response()->noContent();
    }
}
