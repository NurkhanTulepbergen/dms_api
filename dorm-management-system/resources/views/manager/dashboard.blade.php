@extends('layouts.app')

@section('content')
   <style>:root {
           --sidebar-width: 200px;
           --header-height: 60px;
           --primary-color: #7e57c2;
           --primary-hover: #6f42c1;
           --success-color: #28a745;
           --success-hover: #218838;
           --text-color: #333;
           --border-color: #DDD;
           --bg-light: #F9F9F9;
           --bg-main: #F5F5F5;
       }

       /* Sidebar */
       .sidebar {
           position: fixed;
           top: var(--header-height);
           left: 0;
           width: var(--sidebar-width);
           height: calc(100vh - var(--header-height));
           background-color: #fff;
           border-right: 1px solid var(--border-color);
           padding-top: 20px;
       }
       .sidebar-item {
           display: flex;
           align-items: center;
           gap: 10px;
           padding: 12px 20px;
           color: var(--text-color);
           text-decoration: none;
           cursor: pointer;
       }
       .sidebar-item:hover {
           background-color: #efefef;
       }
       .sidebar-item i {
           font-size: 18px;
       }

       /* Main Content */
       .main-content {
           margin-left: var(--sidebar-width);
           padding: 80px 20px 20px;
           background-color: var(--bg-main);
           min-height: calc(100vh - var(--header-height));
       }
       .main-content h2 {
           margin-bottom: 20px;
           color: var(--text-color);
       }

       /* Table */
       .table, .users-table {
           width: 100%;
           border-collapse: collapse;
           background-color: #fff;
       }
       .table th, .table td, .users-table th, .users-table td {
           padding: 12px;
           border: 1px solid var(--border-color);
           text-align: left;
           font-size: 0.95rem;
           color: var(--text-color);
       }
       .table th, .users-table th {
           background-color: var(--bg-light);
       }
       .users-table tr:last-child td {
           border-bottom: none;
       }

       /* Form */
       .form-label {
           font-weight: bold;
           display: block;
           margin: 8px 0 4px;
       }
       .form-control {
           width: 100%;
           padding: 8px;
           margin-bottom: 12px;
           border: 1px solid #ccc;
           border-radius: 4px;
       }

       /* Buttons */
       .btn-success, .btn-primary, .plus-button {
           padding: 8px 16px;
           border-radius: 4px;
           border: none;
           color: #fff;
           cursor: pointer;
       }
       .btn-success {
           background-color: var(--success-color);
       }
       .btn-success:hover {
           background-color: var(--success-hover);
       }
       .btn-primary, .plus-button {
           background-color: var(--primary-color);
       }
       .btn-primary:hover, .plus-button:hover {
           background-color: var(--primary-hover);
       }

       /* Utility */
       .heading-row {
           display: flex;
           align-items: center;
           justify-content: space-between;
           margin-bottom: 1rem;
       }
       .user-filter {
           display: flex;
           gap: 8px;
           margin-bottom: 1rem;
       }
       .user-filter input {
           padding: 8px;
           border: 1px solid #ccc;
           border-radius: 4px;
           font-size: 0.9rem;
       }
       .logout-form button {
           background: none;
           border: none;
           color: var(--text-color);
           font-size: 0.9rem;
           cursor: pointer;
       }
       .logout-form button:hover {
           text-decoration: underline;
       }
       a.my-span-style {
           text-decoration: none;
           color: var(--text-color);
       }
       a.my-span-style:hover {
           color: #555;
       }

       /* Modal */
       .modal-overlay {
           display: none;
           position: fixed;
           top: 0;
           left: 0;
           width: 100%;
           height: 100%;
           background: rgba(0,0,0,0.5);
           align-items: center;
           justify-content: center;
           z-index: 9999;
       }
       .modal-content {
           background: #fff;
           width: 500px;
           padding: 20px;
           border-radius: 8px;
           position: relative;
       }
       .close-button {
           position: absolute;
           top: 10px;
           right: 10px;
           background: none;
           border: none;
           font-size: 1.2rem;
           cursor: pointer;
       }
       .close-button:hover {
           color: #666;
       }</style>

   <!-- Sidebar -->
   <div class="sidebar">
       <div class="sidebar-item" onclick="seeNews()"><i class="fas fa-home"></i><span>{{ __('messages.feed') }}</span></div>
       <div class="sidebar-item" onclick="showNews()"><i class="fas fa-newspaper"></i><span>{{ __('messages.news') }}</span></div>
       <div class="sidebar-item" onclick="showRequests()"><i class="fas fa-bars"></i><span>{{ __('messages.requests') }}</span></div>
   </div>

   <!-- Блок с новостями (ЛЕНТА) -->
   <div class="main-content" id="see-news-section" style="display: none;">
       <h2>{{ __('messages.news') }}</h2>
       @isset($newsList)
           @forelse($newsList as $news)
               <div class="news-item">
                   @if($news->image)
                       <img src="{{ asset('storage/' . $news->image) }}" alt="{{ __('messages.news_image') }}">
                   @endif
                   <h3>{{ $news->title }}</h3>
                   <p>{{ $news->content }}</p>
                   <small>{{ $news->created_at->format('d.m.Y H:i') }}</small>
               </div>
           @empty
               <p>{{ __('messages.no_news') }}</p>
           @endforelse
       @endisset
   </div>

   <!-- СЕКЦИЯ "Новости" (CRUD), по умолчанию скрыта -->
   <div class="main-content" id="news-section" style="display: none;">
       <div class="container">
           <h2>{{ __('messages.manage_news') }}</h2>
           @if(session('success'))
               <div class="alert alert-success">{{ session('success') }}</div>
           @endif

           <!-- Кнопка для показа формы создания новости -->
           <a href="javascript:void(0)" onclick="CreateNews()" class="btn btn-primary mb-3">{{ __('messages.create_news') }}</a>

           <table class="table">
               <thead>
               <tr>
                   <th>{{ __('messages.title') }}</th>
                   <th>{{ __('messages.date') }}</th>
                   <th>{{ __('messages.actions') }}</th>
               </tr>
               </thead>
               <tbody>
               @forelse($newsList as $news)
                   <tr>
                       <td>{{ $news->title }}</td>
                       <td>{{ $news->created_at->format('d.m.Y H:i') }}</td>
                       <td>
                           <button class="btn btn-warning btn-sm"
                                   onclick='EditNews({{ $news->id }}, {!! json_encode($news->title) !!}, {!! json_encode(strip_tags($news->content)) !!})'>
                               {{ __('messages.edit') }}
                           </button>
                           <form action="{{ route('manager.news.destroy', $news->id) }}"
                                 method="POST" style="display:inline-block">
                               @csrf
                               @method('DELETE')
                               <button class="btn btn-danger btn-sm" onclick="return confirm('{{ __('messages.confirm_delete') }}')">{{ __('messages.delete') }}</button>
                           </form>
                       </td>
                   </tr>
               @empty
                   <tr><td colspan="3">{{ __('messages.no_news') }}</td></tr>
               @endforelse
               </tbody>
           </table>
       </div>
   </div>

   <!-- СЕКЦИЯ СОЗДАНИЯ НОВОСТИ (скрыта по умолчанию) -->
   <div class="main-content" id="create-news-section" style="display: none;">
       <div class="container">
           <h2>{{ __('messages.create_news') }}</h2>
           <div class="create-news-section">
               <form action="{{ route('manager.news.store') }}" method="POST" enctype="multipart/form-data">
                   @csrf
                   <label class="form-label">{{ __('messages.title') }}</label>
                   <input type="text" name="title" class="form-control" required>

                   <label class="form-label">{{ __('messages.content') }}</label>
                   <textarea name="content" class="form-control" rows="5" required></textarea>

                   <label class="form-label">{{ __('messages.image_optional') }}</label>
                   <input type="file" name="image" class="form-control">

                   <button type="submit" class="btn-success">{{ __('messages.create') }}</button>
               </form>
           </div>
       </div>
   </div>

   <!-- СЕКЦИЯ РЕДАКТИРОВАНИЯ НОВОСТИ (скрыта) -->
   <div class="main-content" id="edit-news-section" style="display: none;">
       <div class="container">
           <h2>{{ __('messages.edit_news') }}</h2>
           <div class="create-news-section">
               <form id="editNewsForm" method="POST" enctype="multipart/form-data">
                   @csrf
                   <input type="hidden" name="_method" value="PUT">

                   <label class="form-label">{{ __('messages.title') }}</label>
                   <input type="text" id="edit-title" name="title" class="form-control" required>

                   <label class="form-label">{{ __('messages.content') }}</label>
                   <textarea id="edit-content" name="content" class="form-control" rows="5" required></textarea>

                   <label class="form-label">{{ __('messages.image_optional') }}</label>
                   <input type="file" name="image" class="form-control">

                   <button type="submit" class="btn-success">{{ __('messages.save') }}</button>
                   <button type="button" class="plus-button" onclick="cancelEdit()">{{ __('messages.cancel') }}</button>
               </form>
           </div>
       </div>
   </div>

   <!-- СЕКЦИЯ заявки на заселение -->
   <div class="main-content" id="request-section" style="display: none;">
       <h2>{{ __('messages.requests') }}</h2>

       @if(session('success'))
           <div style="background-color: #d4edda; color: #155724; padding: 10px;">
               {{ session('success') }}
           </div>
       @endif

       <table style="width:100%; border-collapse: collapse;">
           <thead>
           <tr>
               <th>{{ __('messages.student') }}</th>
               <th>{{ __('messages.building') }}</th>
               <th>{{ __('messages.floor') }}</th>
               <th>{{ __('messages.room') }}</th>
               <th>{{ __('messages.status') }}</th>
               <th>{{ __('messages.actions') }}</th>
           </tr>
           </thead>
           <tbody>
           @foreach($requests as $req)
               <tr style="border-bottom: 1px solid #ccc;">
                   <td>{{ $req->user->name }}</td>
                   <td>{{ $req->building->name}}</td>
                   <td>{{ $req->floor }}</td>
                   <td>{{ $req->room->room_number }}</td>
                   <td>{{ $req->status }}</td>
                   <td>
                       <a href="{{ route('booking.accept', $req->id) }}"
                          style="color: green; text-decoration: none; margin-right: 10px;">
                           {{ __('messages.accept') }}
                       </a>
                       <a href="{{ route('booking.reject', $req->id) }}"
                          style="color: red; text-decoration: none;">
                           {{ __('messages.reject') }}
                       </a>
{{--                       <a href="{{ route('booking.reject', $req->id) }}"--}}
{{--                          style="color: red; text-decoration: none;">--}}
{{--                           {{ __('messages.in_review') }}--}}
{{--                       </a>--}}

                   </td>
               </tr>
           @endforeach
           </tbody>
       </table>
   </div>

   <script>
        document.addEventListener('DOMContentLoaded', function() {
            seeNews();
            @if($errors->any())
            openModal();
            @endif
            @if(session('successType') === 'news_created')
            showNews();
            @endif
        });
        function seeNews() {
            hideAllSections();
            document.getElementById('see-news-section').style.display = 'block';
        }
        function showNews() {
            hideAllSections()
            document.getElementById('news-section').style.display = 'block';
        }
        function CreateNews() {
            hideAllSections()
            document.getElementById('create-news-section').style.display = 'block';
        }
        function EditNews(id, title, content) {
            hideAllSections();
            document.getElementById('edit-news-section').style.display = 'block';
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-content').value = content;
            document.getElementById('editNewsForm').action = '/manager/dashboard/news/' + id;
        }
        function showRequests() {
            hideAllSections();
            document.getElementById('request-section').style.display = 'block';
        }
        function cancelEdit() {
            document.getElementById('edit-news-section').style.display = 'none';
            showNews();
        }
        function hideAllSections() {
            document.getElementById('news-section').style.display = 'none';
            document.getElementById('see-news-section').style.display = 'none';
            document.getElementById('create-news-section').style.display = 'none';
            document.getElementById('edit-news-section').style.display = 'none';
            document.getElementById('request-section').style.display = 'none';
        }
    </script>
@endsection
