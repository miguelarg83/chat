<?php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\TimeColumn;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\BooleanColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class ProductsTable extends LivewireDatatable
{
    public $hideable = 'select';
    public $exportable = true;
    public $dates = 'created_at|m-d-Y h:i:s';
    public $model = Product::class;
    public $beforeTableSlot = 'livewire.datatables.componentes.boton';
    protected $listeners = ['deleteChecked'];

    public function alertDeleteChecked()
    {
        if(count($this->selected)){
            $this->emit('alertDeleteChecked');
            //session()->flash('message','Order deleted successfully!');
        }
    }

    public function deleteChecked()
    {
        Product::whereIn('id', $this->selected)->delete();
        $this->selected=[];
    }

    public function builder()
    {
        return Product::query();
    }

    public function columns()
    {
        return [
            Column::checkbox(),
            NumberColumn::name('id')
                ->label('ID')
                ->filterable()
                ->linkTo('user'),
            Column::name('nombre')
                ->defaultSort('asc')
                ->searchable()
                ->editable(),
            Column::callback(['category.nombre'], function ($category) {
                return $category=="quia"
                    ? '<span class="text-red-500">' . $category . '</span>'
                    : $category;
            })->label('Categoria')
                ->searchable()
                ->filterable(Category::pluck('nombre')),
            Column::name('images.nombre')
                ->label('Imagenes')
                ->truncate(10)
                ->filterable(),
            DateColumn::name('created_at')
                ->label('Fecha de Creación')
                ->filterable(),
            //Column::delete(),
            Column::callback(['id'], function ($id) {
                return view('table-actions', ['id' => $id]);
            })
        ];
    }
}