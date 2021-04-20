
{{-- Setup data for datatables --}}
@php
$heads = [
    'ID',
    'Name',
    'Style',
    'Price',
    ['label' => 'Image Url', 'width' => 40],
    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
];


function btnEdit($icon)
{
    return "<button onclick='editClicked($icon)' data-toggle='modal' data-target='#editModal' class='btn btn-xs btn-default text-primary mx-1 shadow' title='Edit'>
                <i class='fa fa-lg fa-fw fa-pen'></i>
            </button>";
}

function btnDelete($icon)
{
    return  "<button onclick='deleteClicked($icon)' data-toggle='modal' data-target='#deleteModal' class='btn btn-xs btn-default text-danger mx-1 shadow'  title='Delete'>
                  <i class='fa fa-lg fa-fw fa-trash'></i>
              </button>";
}

function btnDetails($icon)
{
    return   "<button onclick='detailsClicked($icon)' data-toggle='modal' data-target='#detailsModal' class='btn btn-xs btn-default text-teal mx-1 shadow' title='Details'>
                   <i class='fa fa-lg fa-fw fa-eye'></i>
               </button>";
}

// dd($members);

$config = [
    'data' => $icons->map(function ($icon){
        return [
            $icon->id,
            $icon->name,
            $icon->style->value,
            $icon->price,
            $icon->img_url,
            '<nobr>'.btnEdit($icon).btnDelete($icon).btnDetails($icon).'</nobr>'
    ];
    })->toArray(),
    'order' => [[1, 'asc']],
    'columns' => [null, null, null, ['orderable' => false]],
];
@endphp
@extends('adminlte::page')

@section('content')
    {{-- Minimal example / fill data using the component slot --}}
    <x-adminlte-datatable id="table1" with-buttons :heads="$heads">
        @foreach($config['data'] as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{!! $cell !!}</td>
                @endforeach
            </tr>
        @endforeach
    </x-adminlte-datatable>
    <x-adminlte-button theme="primary" data-toggle='modal' data-target='#newIcon' label="Create new icon"/>
    <x-adminlte-modal id="editModal" title="Edit Icon" theme="blue"
        icon="fas fa-bolt" size='lg' disable-animations>
            <form method="POST" action="/">
                <label for="basic-url">Name</label>
                <div class="input-group mb-3">
                    <input name="name" type="text" class="form-control"  id="editName" placeholder="Name">
                </div>
                <label for="basic-url">Style</label>
                <div class="input-group mb-3">
                    <input name="style" type="text" class="form-control"  id="editStyle" placeholder="style">
                </div>
                <label for="basic-url">Price</label>
                <div class="input-group mb-3">
                    <input name="price" type="text" class="form-control"  id="editPrice" placeholder="price">
                </div>
                <label for="basic-url">Image Url</label>
                <div class="input-group mb-3">
                    <input name="url" type="text" class="form-control"  id="editImage" placeholder="Image">
                </div>
                <label for="basic-url">Categories</label>
                <div class="input-group mb-3">
                    <input name="categories" type="text" class="form-control"  id="editCategories" placeholder="categories">
                </div>
                <label for="basic-url">Tags</label>
                <div class="input-group mb-3">
                    <input name="tags" type="text" class="form-control"  id="editTags" placeholder="tags">
                </div>
                <label for="basic-url">Colors(Hex Format)</label>
                <div class="input-group mb-3">
                    <input name="colors" type="text" class="form-control"  id="editColors" placeholder="Color">
                </div>
                <x-adminlte-button type="submit" id="editButton" onclick="submitEdit(event)" class="ml-auto" theme="success" label="Edit"/>
                <x-slot name="footerSlot">
                    <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
                </x-slot>
            </form>
    </x-adminlte-modal>
    <x-adminlte-modal id="deleteModal" title="Delete member" size="sm" theme="blue" disable-animations>
        <div id="deleteText"></div>
        <x-slot name="footerSlot">
            <x-adminlte-button class="mr-auto" theme="success" id="deleteButton" onclick="submitDelete()" label="Accept"/>
            <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
    <x-adminlte-modal id="detailsModal" title="View member" size="lg" theme="blue" disable-animations>
        <ul class="list-group">
            <li id="detailsId" class="list-group-item"></li>
            <li id="detailsName" class="list-group-item"></li>
            <li id="detailsStyle" class="list-group-item"></li>
            <li id="detailsPrice" class="list-group-item"></li>
            <li id="detailsImage" class="list-group-item"></li>
            <li id="detailsCategories" class="list-group-item"></li>
            <li id="detailsTags" class="list-group-item"></li>
            <li id="detailsColors" class="list-group-item"></li>
        </ul>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
    <x-adminlte-modal id="newIcon" title="Create New Icon" theme="blue"
        icon="fas fa-bolt" size='lg' disable-animations>
            <form method="POST" action="/">
                <label for="basic-url">Name</label>
                <div class="input-group mb-3">
                    <input name="name" type="text" class="form-control"  id="newName" placeholder="Name">
                </div>
                <label for="basic-url">Style</label>
                <div class="input-group mb-3">
                    <input name="style" type="text" class="form-control"  id="newStyle" placeholder="style">
                </div>
                <label for="basic-url">Price</label>
                <div class="input-group mb-3">
                    <input name="price" type="text" class="form-control"  id="newPrice" placeholder="price">
                </div>
                <label for="basic-url">Image Url</label>
                <div class="input-group mb-3">
                    <input name="url" type="text" class="form-control"  id="newImage" placeholder="Image">
                </div>
                <label for="basic-url">Categories(Seperate each category by comma)</label>
                <div class="input-group mb-3">
                    <input name="categories" type="text" class="form-control"  id="newCategories" placeholder="categories">
                </div>
                <label for="basic-url">Tags(Seperate each tag by comma)</label>
                <div class="input-group mb-3">
                    <input name="tags" type="text" class="form-control"  id="newTags" placeholder="tags">
                </div>
                <label for="basic-url">Colors in Hex Format(Seperate each color by comma)</label>
                <div class="input-group mb-3">
                    <input name="colors" type="text" class="form-control"  id="newColors" placeholder="Color">
                </div>
                <x-adminlte-button id="createButton" type="submit" onclick="submitNew(event)" class="ml-auto" theme="success" label="Create"/>
                <x-slot name="footerSlot">
                    <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
                </x-slot>
            </form>
    </x-adminlte-modal>
@stop

@section('js')
    <script src="{{  asset('js/icon.js') }}"></script>
@stop





