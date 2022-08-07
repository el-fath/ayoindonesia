<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeamResource;
use App\Models\Player;
use App\Models\Team;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    private $location = 'files/';

    /**
     * @OA\Get(
     *      path="/teams",
     *      operationId="getTeamList",
     *      tags={"Teams"},
     *      summary="get list of teams",
     *      description="Returns list of teams",
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
        return TeamResource::collection(Team::with(['players'])->orderByDesc('id')->paginate());
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
     *      path="/teams",
     *      operationId="createteam",
     *      tags={"Teams"},
     *      summary="create new team",
     *      description="create data team",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="create team",
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "name",
     *                      "logo", 
     *                      "since", 
     *                      "address", 
     *                      "city_id",
     *                  },
     *                  @OA\Property(property="name", type="string", format="text", example="Persebaya"),
     *                  @OA\Property(property="logo", type="file", format="file", example=""),
     *                  @OA\Property(property="since", type="date", format="date", example="1973-08-05"),
     *                  @OA\Property(property="address", type="string", format="text", example="Glora Bung Tomo Surabaya"),
     *                  @OA\Property(property="city_id", type="number", format="number", example="1"),
     *              ),
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
            'name'    => 'required|string',
            'logo'    => 'required|mimes:jpeg,png,jpg,gif|max:10000',
            'since'   => 'required|date',
            'address' => 'required|string',
            'city_id' => 'required|integer|exists:cities,id',
            'player_ids'   => 'array',
            'player_ids.*' => 'required|integer|exists:players,id',
        ]);

        if ($validator->fails()) return response(['errors' => $validator->errors()->all()], 422);

		DB::beginTransaction();
		try {
            $data = $request->except('player_ids');

            // get & rename file
            $file = $request->file('logo');
            $filename = time() . "-" . $file->getClientOriginalName();

            // upload &save file
            Storage::disk('local')->put($this->location . $filename, file_get_contents($file));
            $data['logo'] = $filename;

            $data = Team::create($data);

            if ($request->has('player_ids')) {
                Player::whereIn('id', $request->player_ids)->update(['team_id' => $data->id]);
            }
			DB::commit();

            $res = new TeamResource($data);
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
     *      path="/teams/{id}",
     *      operationId="getTeamById",
     *      tags={"Teams"},
     *      summary="get team by id",
     *      description="get team by id",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="get team by id",
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
    public function show(Team $team)
    {
        return new TeamResource(Team::with(['players'])->find($team->id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team)
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/teams/{id}",
     *      operationId="updateteamById",
     *      tags={"Teams"},
     *      summary="update team by id",
     *      description="update team data",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="update team by id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="update team by id",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", format="text", example="Persebaya"),
     *              @OA\Property(property="since", type="date", format="date", example="1973-08-05"),
     *              @OA\Property(property="address", type="string", format="text", example="Glora Bung Tomo Surabaya"),
     *              @OA\Property(property="city_id", type="number", format="number", example="1"),
     *          ),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="logo", type="file", format="file", example="")
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
    public function update(Request $request, Team $team)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'string',
            'logo'    => 'mimes:jpeg,png,jpg,gif|max:10000',
            'since'   => 'date',
            'address' => 'string',
            'city_id' => 'integer|exists:cities,id',
            'player_ids'   => 'array',
            'player_ids.*' => 'required|integer|exists:players,id',
        ]);

        if ($validator->fails()) return response(['errors' => $validator->errors()->all()], 422);

        $data = $request->except('player_ids');
        $disk = Storage::disk('local');

        if ($request->file('logo')) {
            // delete old file when existing
            if ($disk->exists($this->location . $team->logo)) $disk->delete($this->location . $team->logo);

            // get & rename file
            $file = $request->file('logo');
            $filename = time() . "-" . $file->getClientOriginalName();

            // upload &save file
            $disk->put($this->location . $filename, file_get_contents($file));
            $data['logo'] = $filename;
        }

        $team->update($data);

        if ($request->has('player_ids')) {
            Player::whereIn('id', $request->player_ids)->update(['team_id' => $team->id]);
        }

        return $this->show($team);
    }

    /**
     * @OA\Delete(
     *      path="/teams/{id}",
     *      operationId="deleteTeamById",
     *      tags={"Teams"},
     *      summary="delete team by id",
     *      description="delete team by Id",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="delete team by id",
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
    public function destroy(Team $team)
    {
        // delete file when existing
        $disk = Storage::disk('local');
        if ($disk->exists($this->location . $team->logo)) $disk->delete($this->location . $team->logo);

        $team->delete();
        return response()->noContent();
    }
}
