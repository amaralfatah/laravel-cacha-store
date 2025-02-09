<?php

namespace App\Traits;

use Yajra\DataTables\Facades\DataTables;

trait DataTableTrait
{
    protected $defaultActions = ['view', 'edit', 'delete'];

    public function handleDataTable($query)
    {
        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                return $this->getActionColumn($row);
            })
            ->addColumn('status_label', function ($row) {
                $badges = [
                    'pending' => 'bg-warning',
                    'success' => 'bg-success',
                    'failed' => 'bg-danger'
                ];
                $badge = $badges[$row->status] ?? 'bg-secondary';
                return "<span class='badge {$badge}'>{$row->status}</span>";
            })
            ->rawColumns(['action', 'status_label'])
            ->make(true);
    }

    protected function getActionColumn($row)
    {
        $actions = $this->actions ?? $this->defaultActions;
        $buttons = [];

        foreach ($actions as $action) {
            $methodName = 'get' . ucfirst($action) . 'Button';
            if (method_exists($this, $methodName)) {
                $result = $this->{$methodName}($row);
                if (!empty($result)) {
                    $buttons[] = $result;
                }
            }
        }

        return implode(' ', $buttons);
    }
}
