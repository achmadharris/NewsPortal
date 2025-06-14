@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Daftar Berita</h4>
        <a href="{{ route('news.create') }}" class="btn btn-primary">Tambah Berita</a>
    </div>
    @if($news->count())
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($news as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->category->name ?? '-' }}</td>
                            <td>{{ $item->author->name ?? '-' }}</td>
                            <td>{{ $item->status }}</td>
                            <td>{{ $item->created_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('news.show', $item->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                <a href="{{ route('news.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <!-- Tambahkan tombol hapus jika perlu -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">
            Belum ada berita. Klik tombol <b>Tambah Berita</b> untuk membuat berita baru.
        </div>
    @endif
</div>
@endsection 