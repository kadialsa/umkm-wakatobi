@extends('layouts.admin')

@section('content')
<style>
    /* Sesuaikan style table dan tombol seperti template sebelumnya */

    .flex {
        display: flex;
        align-items: center;
    }
    .flex-wrap {
        flex-wrap: wrap;
    }
    .justify-between {
        justify-content: space-between;
    }
    .justify-end {
        justify-content: flex-end;
    }
    .gap10 {
        gap: 10px;
    }
    .gap20 {
        gap: 20px;
    }
    .mb-27 {
        margin-bottom: 27px;
    }
    .btn-add {
        background-color: #6a6e51;
        color: #fff;
        border: none;
        padding: 8px 18px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.3s ease;
        text-decoration: none;
        font-size: 14px;
    }
    .btn-add:hover {
        /* background-color: #575b43;
    } */
    .btn-add i {
        font-size: 16px;
    }

    /* Style header and breadcrumbs */
    h3.page-title {
        font-weight: 600;
        font-size: 1.8rem;
        color: #333;
    }
    ul.breadcrumbs {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    ul.breadcrumbs li {
        font-size: 13px;
        color: #777;
    }
    ul.breadcrumbs li a {
        color: #777;
        text-decoration: none;
    }
    ul.breadcrumbs li i {
        margin: 0 6px;
        font-size: 10px;
        color: #aaa;
    }

    /* Table styling as per previous template */
    table.table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        color: #333;
    }
    table.table thead tr {
        /* background-color: #6a6e51; */
        color: #000000;
    }
    table.table thead tr th {
        padding: 10px 15px;
        text-align: left;
        border: 1px solid #6a6e51;
    }
    table.table tbody tr td {
        border: 1px solid #6a6e51;
        padding: 12px 15px;
        vertical-align: middle;
    }

    /* Action icons */
    .list-icon-function {
        display: flex;
        gap: 12px;
    }
    .list-icon-function .item {
        cursor: pointer;
        padding: 5px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        color: #6a6e51;
    }
    .list-icon-function .item.edit:hover {
        background-color: #a6af7e;
        color: #fff;
    }
    .list-icon-function .item.delete:hover {
        background-color: #e55353;
        color: #fff;
    }

    /* Responsive for smaller devices */
    @media(max-width: 768px) {
        .flex.justify-between {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        .btn-add {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Manajemen Alamat Admin</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Address</div>
                    </li>
                </ul>
            </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap mb-20">
                {{-- Optional: bisa tambahkan form search di sini --}}
                <div></div>

                <a class="tf-button style-1 w208" href="{{ route('admin.address.add') }}"><i class="icon-plus"></i>Add
                        new</a>
            </div>

            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    @if (Session::has('status'))
                        <div class="alert alert-success">
                            <p class="alert alert-success">{{ Session::get('status') }}</p>
                        </div>
                    @endif
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Pengguna</th>
                                <th>Alamat Lengkap</th>
                                <th>Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($addresses as $index => $address)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $address->name }}
                                    @if ($address->isdefault)
                                    <span title="Alamat Utama" style="color:#4CAF50; font-weight:bold;">&#10003;</span>
                                    @endif
                                </td>
                                <td>{{ $address->user->name }} <br><small>{{ $address->user->email }}</small></td>
                                <td>
                                    {{ $address->address }},<br>
                                    {{ $address->locality }}, {{ $address->city }},<br>
                                    {{ $address->state }}, {{ $address->country }},<br>
                                    @if ($address->landmark)
                                    Patokan: {{ $address->landmark }},<br>
                                    @endif
                                    Kode Pos: {{ $address->zip }}
                                </td>
                                <td>{{ $address->phone }}</td>
                                <td>
                                    <div class="list-icon-function">
                                        <a href="{{ route('admin.address.edit', $address->id) }}" title="Edit">
                                            <div class="item edit"><i class="icon-edit-3"></i></div>
                                        </a>
                                        <form action="{{ route('admin.address.delete', $address->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="item delete" title="Hapus" style="border:none; background:none; color:red;">
                                                <i class="icon-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 15px;">Belum ada alamat yang ditambahkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
