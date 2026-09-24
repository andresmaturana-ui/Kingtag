<nav class="admin-nav">
    <a href="{{ route('admin.index') }}" @class(['active' => request()->routeIs('admin.index')])>Resumen</a>
    <a href="{{ route('admin.messages') }}" @class(['active' => request()->routeIs('admin.messages')])>Mensajes</a>
    <a href="{{ route('admin.tags') }}" @class(['active' => request()->routeIs('admin.tags')])>Tags</a>
    <a href="{{ route('admin.users') }}" @class(['active' => request()->routeIs('admin.users')])>Usuarios</a>
</nav>
