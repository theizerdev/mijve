<div>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Foto de perfil</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="d-flex flex-column align-items-center">
                    <!-- Vista previa del avatar -->
                    <div class="mb-3">
                        @if($tempAvatar)
                            <img src="{{ $tempAvatar }}" class="rounded-circle" width="150" height="150" alt="Vista previa">
                        @elseif($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" class="rounded-circle" width="150" height="150" alt="Avatar actual">
                        @else
                            <div class="rounded-circle bg-light d-flex justify-content-center align-items-center" style="width: 150px; height: 150px;">
                                <i class="ri ri-user-line" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Input para subir archivo -->
                    <div class="mb-3">
                        <input type="file" wire:model="avatar" class="form-control" accept="image/*">
                        @error('avatar') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <!-- Botones -->
                    <div class="d-flex gap-2">
                        @if($avatar)
                            <button type="submit" class="btn btn-primary">
                                <i class="ri ri-save-line me-1"></i> Guardar
                            </button>
                            <button type="button" wire:click="$set('avatar', null)" class="btn btn-secondary">
                                <i class="ri ri-close-line me-1"></i> Cancelar
                            </button>
                        @else
                            <button type="button" class="btn btn-primary" onclick="document.querySelector('input[type=file]').click()">
                                <i class="ri ri-upload-line me-1"></i> Seleccionar imagen
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
