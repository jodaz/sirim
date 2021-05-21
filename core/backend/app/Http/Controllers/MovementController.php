<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Concept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = DB::table('concepts')
            ->select(
                'concepts.id AS id',
                'concepts.name AS name',
                'concurrent',
                DB::raw('COUNT(movements.id) AS movements_count'),
                DB::raw('CAST(SUM(movements.amount) AS NUMERIC) AS amount')
            )
            ->join('movements', 'movements.concept_id', '=', 'concepts.id')
            ->groupBy('concurrent', 'concepts.id')
            ->whereNull('movements.deleted_at')
            ->orderBy('concepts.id', 'ASC');
        $results = $request->perPage ?? 10;

        if ($request->has('filter')) {
            $filters = $request->filter;

            if (array_key_exists('gt_date', $filters)) {
                $query->whereDate('movements.created_at', '>=', $filters['gt_date']);
            }
            if (array_key_exists('lt_date', $filters)) {
                $query->whereDate('movements.created_at', '<', $filters['lt_date']);
            }
            if (array_key_exists('concept', $filters)) {
                $name = $filters['concept'];

                $query->where('concepts.name', 'ILIKE', "%{$name}%");
            }
        }

        return $query->paginate($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Movement  $movement
     * @return \Illuminate\Http\Response
     */
    public function show(Movement $movement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Movement  $movement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Movement $movement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Movement  $movement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movement $movement)
    {
        //
    }
}
