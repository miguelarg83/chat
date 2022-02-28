<?php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Kdion4891\LaravelLivewireForms\ArrayField;
use Kdion4891\LaravelLivewireForms\Field;
use Kdion4891\LaravelLivewireForms\FormComponent;

class ProductCreateForm extends FormComponent
{
    public function fields()
    {
        $category_options=Category::orderBy('nombre')->get()->pluck('id','nombre')->all();

        return [
            Field::make('Nombre')->input()->rules(['required','unique:products,nombre'])->autocomplete('off'),
            Field::make('Categoria', 'category_id')->select($category_options)->rules(['required', Rule::exists('categories', 'id')]),
            Field::make('Photos')->file()->multiple()->rules('required|max:2')->placeholder('máximo 2 imágenes'),
        ];
    }

    public function success()
    {
        Product::create($this->form_data);
    }

    public function saveAndStayResponse()
    {
        return redirect()->route('productos.create');
    }

    public function saveAndGoBackResponse()
    {
        return redirect()->route('productos.data-table');
    }
}
