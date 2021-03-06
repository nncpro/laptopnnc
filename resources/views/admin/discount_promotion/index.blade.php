@extends('admin.layout.master')
@section('content')
    <div class="box box-info">
        <div class="box-header with-border">
            <span class="h4 text-purple">Danh sách Chương trình Khuyến mại</span>
        </div>
        <div class="box-body">
            <div style="margin-bottom: 15px">
                <a href="{{ route('promotion.create') }}"><button class="btn btn-info">Tạo chương trình giảm giá</button></a>
            </div>
            <table class="table" id="discountTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Ngày tạo</th>
                        <th>Ngày sửa</th>
                        <th>Ngày hết hạn</th>
                        <th>Đường dẫn</th>
                        <th>Tùy chọn</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($discountList as $discount)
                       <tr>
                           <td>{{ $discount->id }}</td>
                           <td>{{ $discount->title }}</td>
                           <td>{{ $discount->created_at }}</td>
                           <td>{{ $discount->updated_at }}</td>
                           <td>{{ $discount->expired_at }}</td>
                           <td>{{ route('discount.show',Str::slug($discount->title)) }}</td>
                           <td class="text-center">
                                <a href="{{ route('promotion.edit',$discount->id) }}"><button class="btn"><i class="fa fa-pencil-square-o"></i></button></a>
                                <form style="display: inline" action="{{ route('promotion.destroy') }}" method="post">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="id" id="" value="{{ $discount->id }}">
                                    <button class="btn"><i class="fa fa-trash text-red"></i></button>
                                </form>
                           </td>
                       </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(()=>{
            $('.table').DataTable();
        })
    </script>
@endsection
