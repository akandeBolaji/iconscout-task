
{{-- Setup data for datatables --}}
@php
$heads = [
    'ID',
    'Name',
    ['label' => 'Email Address', 'width' => 40],
    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
];


function btnEdit($member)
{
    return "<button onclick='editClicked($member)' data-toggle='modal' data-target='#editModal' class='btn btn-xs btn-default text-primary mx-1 shadow' title='Edit'>
                <i class='fa fa-lg fa-fw fa-pen'></i>
            </button>";
}

function btnDelete($member)
{
    return  "<button onclick='deleteClicked($member)' data-toggle='modal' data-target='#deleteModal' class='btn btn-xs btn-default text-danger mx-1 shadow'  title='Delete'>
                  <i class='fa fa-lg fa-fw fa-trash'></i>
              </button>";
}

function btnDetails($member)
{
    return   "<button onclick='detailsClicked($member)' data-toggle='modal' data-target='#detailsModal' class='btn btn-xs btn-default text-teal mx-1 shadow' title='Details'>
                   <i class='fa fa-lg fa-fw fa-eye'></i>
               </button>";
}

// dd($members);

$config = [
    'data' => $members->map(function ($member){
        return [
            $member->id,
            $member->user->name,
            $member->user->email,
            '<nobr>'.btnEdit($member).btnDelete($member).btnDetails($member).'</nobr>'
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
    <x-adminlte-button theme="primary" data-toggle='modal' data-target='#newMember' label="Create new member"/>
    <x-adminlte-modal id="editModal" title="Edit Member" theme="blue"
        icon="fas fa-bolt" size='lg' disable-animations>
            <form method="POST" action="/">
                <label for="basic-url">Name</label>
                <div class="input-group mb-3">
                    <input name="name" type="text" class="form-control"  id="editName" placeholder="Name">
                </div>
                <label for="basic-url">Email Address</label>
                <div class="input-group mb-3">
                    <input name="email" type="text" class="form-control"  id="editEmail" placeholder="Email">
                </div>
                <x-adminlte-button type="submit" onclick="submitEdit(event)" class="ml-auto" theme="success" label="Edit"/>
                <x-slot name="footerSlot">
                    <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
                </x-slot>
            </form>
    </x-adminlte-modal>
    <x-adminlte-modal id="deleteModal" title="Delete member" size="sm" theme="blue" disable-animations>
        <div id="deleteText"></div>
        <x-slot name="footerSlot">
            <x-adminlte-button class="mr-auto" theme="success" onclick="submitDelete()" label="Accept"/>
            <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
    <x-adminlte-modal id="detailsModal" title="View member" size="sm" theme="blue" disable-animations>
        <ul class="list-group">
            <li id="detailsId" class="list-group-item"></li>
            <li id="detailsName" class="list-group-item"></li>
            <li id="detailsEmail" class="list-group-item"></li>
        </ul>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>
    <x-adminlte-modal id="newMember" title="Create New Member" theme="blue"
        icon="fas fa-bolt" size='lg' disable-animations>
            <form method="POST" action="/">
                <label for="basic-url">Name</label>
                <div class="input-group mb-3">
                    <input name="name" type="text" class="form-control"  id="newName" placeholder="Name">
                </div>
                <label for="basic-url">Email Address</label>
                <div class="input-group mb-3">
                    <input name="email" type="text" class="form-control"  id="newEmail" placeholder="Email">
                </div>
                <label for="basic-url">Password</label>
                <div class="input-group mb-3">
                    <input name="password" type="password" class="form-control"  id="newPassword" placeholder="Password">
                </div>
                <x-adminlte-button type="submit" onclick="submitNew(event)" class="ml-auto" theme="success" label="Create"/>
                <x-slot name="footerSlot">
                    <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
                </x-slot>
            </form>
    </x-adminlte-modal>
@stop

@section('js')
    <script src="{{  asset('js/member.js') }}"></script>
@stop





