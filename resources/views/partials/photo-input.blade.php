<div class="photo-input">
    <img class="photo-preview" alt="" hidden data-photo-preview>
    <input type="file" name="photo" accept="image/*" capture="environment" id="photo-camera" class="visually-hidden" data-photo-input>
    <label for="photo-camera" class="button secondary">Sacar foto</label>
    @if ($allowGallery ?? false)
        <input type="file" accept="image/*" id="photo-gallery" class="visually-hidden" data-photo-gallery>
        <label for="photo-gallery" class="button secondary">Elegir de la galería</label>
    @endif
</div>
