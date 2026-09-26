<nav class="admin-nav">
    <a href="{{ route('admin.index') }}" @class(['active' => request()->routeIs('admin.index')])>Resumen</a>
    <a href="{{ route('admin.inbox') }}" @class(['active' => request()->routeIs('admin.inbox*')])>Mensajes @if ($adminUnreadReplies ?? 0)<span class="badge">{{ $adminUnreadReplies }}</span>@endif</a>
    <a href="{{ route('admin.messages') }}" @class(['active' => request()->routeIs('admin.messages')])>Contacto</a>
    <a href="{{ route('admin.tags') }}" @class(['active' => request()->routeIs('admin.tags')])>Tags</a>
    <a href="{{ route('admin.users') }}" @class(['active' => request()->routeIs('admin.users')])>Usuarios</a>
</nav>
