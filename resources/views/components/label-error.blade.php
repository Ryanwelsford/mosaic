@props(['label', 'error' => $label])

<label>{{ ucfirst($label) }}: @error($error) <span class="error-text">*</span> @enderror</label>
