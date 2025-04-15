<?php

namespace App\Http\Controllers;

use App\Models\Event;
use DateTime;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NaptarController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        // Példa: assume 'day' oszlopban van a dátum (YYYY-MM-DD formátumban)
        $query = Event::query();

        if ($start && $end) {
            $query->whereBetween('day', [$start, $end]);
        }

        $events = $query->get();
        return response()->json($events);
    }
}
