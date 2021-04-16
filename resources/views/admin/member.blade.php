
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
    return "<button onclick='myFunction($member)' data-toggle='modal' data-target='#editModal' class='btn btn-xs btn-default text-primary mx-1 shadow' title='Edit'>
                <i class='fa fa-lg fa-fw fa-pen'></i>
            </button>";
}

// $btnEdit = "<button onclick='myFunction($ghd)' data-toggle='modal' data-target='#modalPurple' class='btn btn-xs btn-default text-primary mx-1 shadow' title='Edit'>
//                 <i class='fa fa-lg fa-fw fa-pen'></i>
//             </button>";
$btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow"  title="Delete">
                  <i class="fa fa-lg fa-fw fa-trash"></i>
              </button>';
$btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                   <i class="fa fa-lg fa-fw fa-eye"></i>
               </button>';


// dd($members);

$config = [
    'data' => $members->map(function ($member) use ($btnDelete, $btnDetails) {
        return [
            $member->id,
            $member->user->name,
            $member->user->email,
            '<nobr>'.btnEdit($member).$btnDelete.$btnDetails.'</nobr>'
    ];
    })->toArray(),
    // 'data' => [
    //     [22, 'John Bender', '+02 (123) 123456789', '<nobr>'.btnEdit(22).$btnDelete.$btnDetails.'</nobr>'],
    //     [19, 'Sophia Clemens', '+99 (987) 987654321', '<nobr>'.btnEdit(19).$btnDelete.$btnDetails.'</nobr>'],
    //     [3, 'Peter Sousa', '+69 (555) 12367345243', '<nobr>'.btnEdit(3).$btnDelete.$btnDetails.'</nobr>'],
    // ],
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
@stop

@section('js')
    <script>
        let selectedMember;

        const myFunction = (member) => {
            selectedMember = member;
            let name = document.getElementById("editName");
            name.placeholder = member.user.name;
            let email = document.getElementById("editEmail");
            email.placeholder = member.user.email;
        }

        const submitEdit = (event) => {
            event.preventDefault();
            let name = document.getElementById("editName").value
            let email = document.getElementById("editEmail").value
            if (!name && !email) {
                return;
            }
            let data = {
                name:   name
                        ? name
                        : selectedMember.user.name,
                email: email
                        ? email
                        : selectedMember.user.email
            }
            fetch(`members/${selectedMember.id}`, {
                method: 'PUT', // or 'PUT'
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(data),
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Success:', data);
                    location.reload();
                })
                .catch((error) => {
                    console.error('Error:', error);
                    //TODO implement error
                });
            }
     </script>
@stop





