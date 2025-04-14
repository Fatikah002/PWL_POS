<form id="formFoto" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label for="foto">Foto Profil</label><br>
        @if (auth()->user()->foto)
            <img id="previewFoto" src="{{ asset('storage/' . auth()->user()->foto) }}" width="100" class="mb-2 rounded">
        @else
            <img id="previewFoto" src="https://via.placeholder.com/100" width="100" class="mb-2 rounded">
        @endif
        <input type="file" name="foto" id="foto" class="form-control-file">
    </div>

    <button type="submit" class="btn btn-primary mt-2">Update Foto</button>
</form>

<!-- Alert -->
<div id="alertSuccess" class="alert alert-success mt-2 d-none">
    Foto berhasil diupload!
</div>

<script>
    document.getElementById('formFoto').addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch("{{ route('profile.foto.update') }}", {
                method: "POST",
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('previewFoto').src = data.foto_url;
                    document.getElementById('alertSuccess').classList.remove('d-none');
                }
            })
            .catch(err => console.error(err));
    });
</script>
