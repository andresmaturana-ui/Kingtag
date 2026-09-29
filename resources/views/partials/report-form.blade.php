{{-- Botón «Reportar» que despliega los motivos. Recibe $action (la URL del reporte). --}}
<details class="report">
    <summary class="link-button">Reportar</summary>
    <form method="POST" action="{{ $action }}" class="form report-form">
        @csrf
        <p class="small">¿Por qué no debería estar?</p>
        @foreach (\App\Models\Report::REASONS as $value => $label)
            <label class="radio"><input type="radio" name="reason" value="{{ $value }}" required> {{ $label }}</label>
        @endforeach
        <button class="button small-button">Enviar reporte</button>
    </form>
</details>
