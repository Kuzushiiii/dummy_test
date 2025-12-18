<?php


namespace App\Helpers\Datatables\Drivers;


use App\Helpers\Datatables\Source\Request;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class Drivers
{

    protected $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * @param Model|BaseBuilder $query
     * @return Model|BaseBuilder
     * */
    public function filter($query)
    {
        if ($this->request->search()->isNotEmpty() && $this->request->getDatabaseColumns()->count() > 0) {
            $query->GroupStart();

            $index = 0;
            $searchValue = $this->request->search()->value();

            foreach ($this->request->columns() as $column) {
                if ($column->isNameEmpty() && $column->isDataNotEmpty()) {
                    $dbcolumn = $this->request->getDatabaseColumns()->get($column->data());

                    if (!empty($dbcolumn)) {
                        $field = $dbcolumn->name();

                        if (!empty($field)) {
                            if ($dbcolumn->isCallableRaw() && !$dbcolumn->isCallableQuery()) {
                                $index == 0
                                    ? $query->where($dbcolumn->raw($field, $dbcolumn->format($searchValue)))
                                    : $query->orWhere($dbcolumn->raw($field, $dbcolumn->format($searchValue)));
                            } else if ($dbcolumn->isCallableQuery()) {
                                $query = $dbcolumn->query($query, $dbcolumn->field($field), $dbcolumn->format($searchValue));
                            } else {
                                $index == 0
                                    // ? $query->like("lower(" . $dbcolumn->field($field) . ")", $dbcolumn->format($searchValue))
                                    // : $query->orLike("lower(" . $dbcolumn->field($field) . ")", $dbcolumn->format($searchValue));
                                    ? $query->like("lower (CAST(" . $dbcolumn->field($field) . " AS VARCHAR))", $dbcolumn->format(strtolower($searchValue))) 
                                    : $query->orLike ("lower (CAST(". $dbcolumn->field($field) . " AS VARCHAR))", $dbcolumn->format(strtolower($searchValue)));
                            }

                            $index++;
                        }
                    }
                } else if ($column->isNameNotEmpty()) {
                    $index == 0
                        ? $query->like($column->name(), $searchValue)
                        : $query->orLike($column->name(), $searchValue);

                    $index++;
                }
            }

            $query->groupEnd();
        }

        foreach ($this->request->orders() as $order) {
            $column = $this->request->getDatabaseColumns()->get($order->column());
            if (!is_null($column) && $column->isNameNotEmpty()) {
                $query->orderBy($column->name(), $order->dir());
            }
        }

        return $query;
    }
}