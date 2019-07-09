<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TicketsExport implements FromView
{

    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function view(): View
    {
        return view('reports.tickets.tickets-excel', [
            'tickets' => $this->query->get()
        ]);
    }
}
